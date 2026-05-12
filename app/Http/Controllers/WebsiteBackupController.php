<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use ZipArchive;

class WebsiteBackupController extends Controller
{
    // ── Backup page ──
    public function index()
    {
        $backupDir = storage_path('app/website-backups');
        $backups   = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/website-backup-*.zip');
            rsort($files);
            foreach ($files as $file) {
                $backups[] = [
                    'name'       => basename($file),
                    'size'       => $this->formatSize(filesize($file)),
                    'created_at' => Carbon::createFromTimestamp(filemtime($file))->format('d M Y, h:i A'),
                ];
            }
        }

        return view('admin.website-backup.index', compact('backups'));
    }

    // ── Create backup ──
    public function create()
    {
        try {
            Artisan::call('website:backup');
            return back()->with('success', 'Backup created successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    // ── Download backup ──
    public function download(Request $request)
    {
        $filePath = storage_path('app/website-backups/' . basename($request->file));
        if (!file_exists($filePath)) return back()->with('error', 'File not found!');
        return response()->download($filePath);
    }

    // ── Delete backup ──
    public function delete(Request $request)
    {
        $filePath = storage_path('app/website-backups/' . basename($request->file));
        if (file_exists($filePath)) {
            unlink($filePath);
            return back()->with('success', 'Backup deleted successfully!');
        }
        return back()->with('error', 'File not found!');
    }

    // ── Restore Database Only (Fast) ──
    public function restoreDb(Request $request)
    {
        $request->validate(['backup_zip' => 'required|file|mimes:zip|max:1048576']);

        $zipPath     = $request->file('backup_zip')->store('temp-restore', 'local');
        $zipFullPath = storage_path('app/' . $zipPath);
        $extractPath = storage_path('app/temp-db-restore-' . uniqid());

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath) !== true) {
            $this->cleanupTemp($zipFullPath, $extractPath);
            return back()->with('error', 'Could not open ZIP file. File may be corrupt.');
        }

        $zip->extractTo($extractPath);
        $zip->close();

        // Validate backup
        if (!file_exists($extractPath . '/backup-info.json')) {
            $this->cleanupTemp($zipFullPath, $extractPath);
            return back()->with('error', 'Invalid backup ZIP (backup-info.json missing).');
        }

        $sqlFile = $extractPath . '/database/prosix-database.sql';
        if (!file_exists($sqlFile)) {
            $this->cleanupTemp($zipFullPath, $extractPath);
            return back()->with('error', 'Database SQL file not found in backup.');
        }

        // Run mysql restore
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $mysql   = PHP_OS_FAMILY === 'Windows' ? 'C:\\xampp\\mysql\\bin\\mysql.exe' : 'mysql';
        $command = "\"{$mysql}\" --host={$dbHost} --port={$dbPort} --user={$dbUser} --password=\"{$dbPass}\" {$dbName} < \"{$sqlFile}\" 2>&1";

        exec($command, $output, $returnCode);

        $this->cleanupTemp($zipFullPath, $extractPath);

        if ($returnCode !== 0) {
            return back()->with('error', 'Database restore failed: ' . implode(' ', $output));
        }

        Artisan::call('optimize:clear');
        return back()->with('success', 'Database restored successfully! All data is back.');
    }

    // ── Full Restore (Database + Files) ──
    public function restore(Request $request)
    {
        $request->validate(['backup_zip' => 'required|file|mimes:zip|max:1048576']);

        $zipPath     = $request->file('backup_zip')->store('temp-restore', 'local');
        $zipFullPath = storage_path('app/' . $zipPath);
        $extractPath = storage_path('app/temp-full-restore-' . uniqid());

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath) !== true) {
            $this->cleanupTemp($zipFullPath, $extractPath);
            return back()->with('error', 'Could not open ZIP file.');
        }

        $zip->extractTo($extractPath);
        $zip->close();

        if (!file_exists($extractPath . '/backup-info.json')) {
            $this->cleanupTemp($zipFullPath, $extractPath);
            return back()->with('error', 'Invalid backup ZIP (backup-info.json missing).');
        }

        $errors = [];

        // Database restore
        $sqlFile = $extractPath . '/database/prosix-database.sql';
        if (file_exists($sqlFile)) {
            $dbHost  = config('database.connections.mysql.host');
            $dbPort  = config('database.connections.mysql.port');
            $dbName  = config('database.connections.mysql.database');
            $dbUser  = config('database.connections.mysql.username');
            $dbPass  = config('database.connections.mysql.password');
            $mysql   = PHP_OS_FAMILY === 'Windows' ? 'C:\\xampp\\mysql\\bin\\mysql.exe' : 'mysql';
            $command = "\"{$mysql}\" --host={$dbHost} --port={$dbPort} --user={$dbUser} --password=\"{$dbPass}\" {$dbName} < \"{$sqlFile}\" 2>&1";
            exec($command, $output, $returnCode);
            if ($returnCode !== 0) $errors[] = 'DB: ' . implode(' ', $output);
        }

        // Files restore
        $storageBackup = $extractPath . '/storage/public';
        if (is_dir($storageBackup)) $this->copyFilesRecursive($storageBackup, storage_path('app/public'));

        $uploadsBackup = $extractPath . '/public/uploads';
        if (is_dir($uploadsBackup)) $this->copyFilesRecursive($uploadsBackup, public_path('uploads'));

        $this->cleanupTemp($zipFullPath, $extractPath);
        Artisan::call('optimize:clear');

        if (!empty($errors)) return back()->with('error', 'Partial restore failed: ' . implode(', ', $errors));

        return back()->with('success', 'Full restore completed! Database + all files are back.');
    }

    // ── Helpers ──
    private function copyFilesRecursive(string $src, string $dest): void
    {
        if (!is_dir($dest)) mkdir($dest, 0775, true);
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $src . DIRECTORY_SEPARATOR . $item;
            $d = $dest . DIRECTORY_SEPARATOR . $item;
            is_dir($s) ? $this->copyFilesRecursive($s, $d) : copy($s, $d);
        }
    }

    private function cleanupTemp(string $zipPath, string $extractPath): void
    {
        if (file_exists($zipPath)) @unlink($zipPath);
        if (is_dir($extractPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($extractPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $f) $f->isDir() ? rmdir($f->getRealPath()) : unlink($f->getRealPath());
            rmdir($extractPath);
        }
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
