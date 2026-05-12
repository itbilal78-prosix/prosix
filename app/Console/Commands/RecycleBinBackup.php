<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use Carbon\Carbon;

class RecycleBinBackup extends Command
{
    protected $signature   = 'recycle:backup';
    protected $description = 'Monthly recycle bin backup banao aur 3 mahine se purane delete karo';

    public function handle()
    {
        $backupDir = storage_path('app/recycle-backups');

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0775, true);
        }

        $fileName = 'recycle-backup-' . now()->format('Y-m') . '.zip';
        $zipPath  = $backupDir . '/' . $fileName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('ZIP file create nahi ho saki!');
            return 1;
        }

        $data = [
            'exported_at'       => now()->toDateTimeString(),
            'artworks'          => \App\Models\ArtworkRequest::onlyTrashed()->get()->toArray(),
            'banners'           => \App\Models\Banner::onlyTrashed()->get()->toArray(),
            'blogs'             => \App\Models\Blog::onlyTrashed()->get()->toArray(),
            'testimonials'      => \App\Models\Testimonial::onlyTrashed()->get()->toArray(),
            'flipbooks'         => \App\Models\Flipbook::onlyTrashed()->get()->toArray(),
            'products'          => \App\Models\Product::onlyTrashed()->get()->toArray(),
            'deals'             => \App\Models\Deal::onlyTrashed()->get()->toArray(),
            'deal_images'       => \App\Models\DealImage::withTrashed()->get()->toArray(),
            'deal_banners'      => \App\Models\DealBanner::withTrashed()->get()->toArray(),
            'videos'            => \App\Models\Video::onlyTrashed()->get()->toArray(),
            'categories'        => \App\Models\Category::onlyTrashed()->get()->toArray(),
            'navigations'       => \App\Models\Navigation::onlyTrashed()->get()->toArray(),
            'customizer_models' => \App\Models\CustomizerModel::onlyTrashed()->get()->toArray(),
            'patterns'          => \App\Models\Pattern::onlyTrashed()->get()->toArray(),
            'colors'            => \App\Models\Color::onlyTrashed()->get()->toArray(),
            'templates'         => \App\Models\Template::onlyTrashed()->get()->toArray(),
            'fonts'             => \App\Models\Font::onlyTrashed()->get()->toArray(),
        ];

        $zip->addFromString('backup-data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $zip->close();

        $this->info("Backup bana: $fileName");

        $files  = glob($backupDir . '/recycle-backup-*.zip');
        $cutoff = Carbon::now()->subMonths(3);

        foreach ($files as $file) {
            preg_match('/recycle-backup-(\d{4}-\d{2})\.zip$/', basename($file), $m);
            if (!empty($m[1])) {
                $fileDate = Carbon::createFromFormat('Y-m', $m[1])->startOfMonth();
                if ($fileDate->lt($cutoff)) {
                    unlink($file);
                    $this->info('Purana backup delete: ' . basename($file));
                }
            }
        }

        $this->info('Backup complete!');
        return 0;
    }
}
