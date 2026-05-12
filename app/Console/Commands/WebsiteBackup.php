<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use Carbon\Carbon;

class WebsiteBackup extends Command
{
    protected $signature   = 'website:backup';
    protected $description = 'Poori website ka backup banao — Database + Files';

    public function handle()
    {
        $backupDir = storage_path('app/website-backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0775, true);
        }

        $fileName = 'website-backup-' . now()->format('Y-m-d') . '.zip';
        $zipPath  = $backupDir . '/' . $fileName;

        $this->info('Backup shuru ho raha hai...');

        // ── Step 1: Database dump ──
        $this->info('Database backup ho raha hai...');
        $sqlFile = storage_path('app/temp-db-dump.sql');

        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        // XAMPP Windows ya Linux server — dono handle karo
        if (PHP_OS_FAMILY === 'Windows') {
            $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        } else {
            $mysqldump = 'mysqldump';
        }

        $command = "\"{$mysqldump}\" --host={$dbHost} --port={$dbPort} --user={$dbUser} --password=\"{$dbPass}\" {$dbName} > \"{$sqlFile}\" 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($sqlFile) || filesize($sqlFile) === 0) {
            $this->error('Database dump fail hua!');
            $this->error('Detail: ' . implode("\n", $output));
            return 1;
        }

        $this->info('Database dump ready!');

        // ── Step 2: ZIP banao ──
        $this->info('ZIP file ban rahi hai...');
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('ZIP file create nahi ho saki!');
            return 1;
        }

        // Database SQL add karo
        $zip->addFile($sqlFile, 'database/prosix-database.sql');

        // ── Step 3: Storage files add karo ──
        $this->info('Files add ho rahi hain...');
        $storagePublic = storage_path('app/public');

        if (is_dir($storagePublic)) {
            $this->addFolderToZip($zip, $storagePublic, 'storage/public');
        }

        // Uploads folder
        $uploadsPath = public_path('uploads');
        if (is_dir($uploadsPath)) {
            $this->addFolderToZip($zip, $uploadsPath, 'public/uploads');
        }

        // Backup info file
        $info = json_encode([
            'backup_date'     => now()->toDateTimeString(),
            'app_name'        => config('app.name'),
            'app_url'         => config('app.url'),
            'database'        => $dbName,
            'laravel_version' => app()->version(),
        ], JSON_PRETTY_PRINT);

        $zip->addFromString('backup-info.json', $info);
        $zip->close();

        // Temp SQL delete karo
        if (file_exists($sqlFile)) unlink($sqlFile);

        $sizeMB = round(filesize($zipPath) / 1024 / 1024, 2);
        $this->info("Backup ready: {$fileName} ({$sizeMB} MB)");

        // ── Step 4: 3 mahine se purane delete karo ──
        $files  = glob($backupDir . '/website-backup-*.zip');
        $cutoff = Carbon::now()->subMonths(3);

        foreach ($files as $file) {
            if ($file === $zipPath) continue;
            preg_match('/website-backup-(\d{4}-\d{2}-\d{2})\.zip$/', basename($file), $m);
            if (!empty($m[1])) {
                $fileDate = Carbon::createFromFormat('Y-m-d', $m[1]);
                if ($fileDate->lt($cutoff)) {
                    unlink($file);
                    $this->info('Purana backup delete: ' . basename($file));
                }
            }
        }

        $this->info('Website backup complete!');
        return 0;
    }

    private function addFolderToZip(ZipArchive $zip, string $folderPath, string $zipFolder): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath     = $file->getRealPath();
            $relativePath = $zipFolder . '/' . substr($filePath, strlen($folderPath) + 1);

            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } elseif ($file->isFile()) {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
