<?php

namespace App\Http\Controllers;

use App\Models\ArtworkRequest;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Testimonial;
use App\Models\Flipbook;
use App\Models\Product;
use App\Models\Deal;
use App\Models\Video;
use App\Models\Category;
use App\Models\Navigation;
use App\Models\CustomizerModel;
use App\Models\Pattern;
use App\Models\Color;
use App\Models\Template;
use App\Models\Font;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class RecycleBinController extends Controller
{
    public function index()
    {
        $artworks         = ArtworkRequest::onlyTrashed()->latest('deleted_at')->get();
        $banners          = Banner::onlyTrashed()->latest('deleted_at')->get();
        $blogs            = Blog::onlyTrashed()->latest('deleted_at')->get();
        $testimonials     = Testimonial::onlyTrashed()->latest('deleted_at')->get();
        $flipbooks        = Flipbook::onlyTrashed()->latest('deleted_at')->get();
        $products         = Product::onlyTrashed()->latest('deleted_at')->get();
        $deals            = Deal::onlyTrashed()->latest('deleted_at')->get();
        $videos           = Video::onlyTrashed()->latest('deleted_at')->get();
        $categories       = Category::onlyTrashed()->latest('deleted_at')->get();
        $navigations      = Navigation::onlyTrashed()->latest('deleted_at')->get();
        $customizerModels = CustomizerModel::onlyTrashed()->latest('deleted_at')->get();
        $patterns         = Pattern::onlyTrashed()->latest('deleted_at')->get();
        $colors           = Color::onlyTrashed()->latest('deleted_at')->get();
        $templates        = Template::onlyTrashed()->latest('deleted_at')->get();
        $fonts            = Font::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.recycle-bin.index', compact(
            'artworks', 'banners', 'blogs', 'testimonials',
            'flipbooks', 'products', 'deals', 'videos',
            'categories', 'navigations', 'customizerModels',
            'patterns', 'colors', 'templates', 'fonts'
        ));
    }

    // =========================================================
    // ARTWORK
    // =========================================================
    public function restoreArtwork($id)
    {
        ArtworkRequest::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Artwork request restored successfully.');
    }

    public function deleteArtwork($id)
    {
        ArtworkRequest::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Artwork request permanently deleted.');
    }

    // =========================================================
    // BANNER
    // =========================================================
    public function restoreBanner($id)
    {
        Banner::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Banner restored successfully.');
    }

    public function deleteBanner($id)
    {
        Banner::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Banner permanently deleted.');
    }

    // =========================================================
    // BLOG
    // =========================================================
    public function restoreBlog($id)
    {
        Blog::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Blog restored successfully.');
    }

    public function deleteBlog($id)
    {
        Blog::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Blog permanently deleted.');
    }

    // =========================================================
    // TESTIMONIAL
    // =========================================================
    public function restoreTestimonial($id)
    {
        Testimonial::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Testimonial restored successfully.');
    }

    public function deleteTestimonial($id)
    {
        Testimonial::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Testimonial permanently deleted.');
    }

    // =========================================================
    // FLIPBOOK
    // =========================================================
    public function restoreFlipbook($id)
    {
        Flipbook::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Flipbook restored successfully.');
    }

    public function deleteFlipbook($id)
    {
        $flipbook = Flipbook::onlyTrashed()->findOrFail($id);
        Storage::disk('public')->delete($flipbook->file_path);
        $flipbook->forceDelete();
        return back()->with('success', 'Flipbook permanently deleted.');
    }

    // =========================================================
    // PRODUCT
    // =========================================================
    public function restoreProduct($id)
    {
        Product::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Product restored successfully.');
    }

    public function deleteProduct($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        if ($product->image) Storage::disk('public')->delete($product->image);
        foreach ($product->gallery_images ?? [] as $img) Storage::disk('public')->delete($img);
        foreach ($product->colors ?? [] as $c) {
            if (!empty($c['image'])) Storage::disk('public')->delete($c['image']);
            foreach ($c['gallery'] ?? [] as $gp) {
                if ($gp) Storage::disk('public')->delete($gp);
            }
        }
        if ($product->size_chart_image) Storage::disk('public')->delete($product->size_chart_image);
        $product->forceDelete();
        return back()->with('success', 'Product permanently deleted.');
    }

    // =========================================================
    // DEAL
    // =========================================================
    public function restoreDeal($id)
    {
        $deal = Deal::onlyTrashed()->findOrFail($id);
        $deal->restore();
        \App\Models\DealImage::withTrashed()->where('deal_id', $id)->restore();
        \App\Models\DealBanner::withTrashed()->where('deal_id', $id)->restore();
        return back()->with('success', 'Deal restored successfully.');
    }

    public function deleteDeal($id)
    {
        $deal = Deal::onlyTrashed()->findOrFail($id);

        $images = \App\Models\DealImage::withTrashed()->where('deal_id', $id)->get();
        foreach ($images as $img) {
            Storage::delete(str_replace('/storage/', 'public/', $img->image_path));
            $img->forceDelete();
        }

        $banners = \App\Models\DealBanner::withTrashed()->where('deal_id', $id)->get();
        foreach ($banners as $banner) {
            Storage::delete(str_replace('/storage/', 'public/', $banner->image_path));
            $banner->forceDelete();
        }

        $deal->forceDelete();
        return back()->with('success', 'Deal permanently deleted.');
    }

    // =========================================================
    // VIDEO
    // =========================================================
    public function restoreVideo($id)
    {
        Video::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Video restored successfully.');
    }

    public function deleteVideo($id)
    {
        $video = Video::onlyTrashed()->findOrFail($id);
        if ($video->thumbnail) Storage::disk('public')->delete($video->thumbnail);
        $video->forceDelete();
        return back()->with('success', 'Video permanently deleted.');
    }

    // =========================================================
    // CATEGORY
    // =========================================================
    public function restoreCategory($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        Category::onlyTrashed()->where('parent_id', $id)->restore();
        return back()->with('success', 'Category restored successfully.');
    }

    public function deleteCategory($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        Category::onlyTrashed()->where('parent_id', $id)->forceDelete();
        $category->forceDelete();
        return back()->with('success', 'Category permanently deleted.');
    }

    // =========================================================
    // NAVIGATION
    // =========================================================
    public function restoreNavigation($id)
    {
        Navigation::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Navigation restored successfully.');
    }

    public function deleteNavigation($id)
    {
        Navigation::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Navigation permanently deleted.');
    }

    // =========================================================
    // CUSTOMIZER MODEL
    // =========================================================
    public function restoreCustomizerModel($id)
    {
        CustomizerModel::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Customizer Model restored successfully.');
    }

    public function deleteCustomizerModel($id)
    {
        CustomizerModel::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Customizer Model permanently deleted.');
    }

    // =========================================================
    // PATTERN
    // =========================================================
    public function restorePattern($id)
    {
        Pattern::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Pattern restored successfully.');
    }

    public function deletePattern($id)
    {
        Pattern::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Pattern permanently deleted.');
    }

    // =========================================================
    // COLOR
    // =========================================================
    public function restoreColor($id)
    {
        Color::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Color restored successfully.');
    }

    public function deleteColor($id)
    {
        Color::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Color permanently deleted.');
    }

    // =========================================================
    // TEMPLATE
    // =========================================================
    public function restoreTemplate($id)
    {
        Template::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Template restored successfully.');
    }

    public function deleteTemplate($id)
    {
        Template::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Template permanently deleted.');
    }

    // =========================================================
    // FONT
    // =========================================================
    public function restoreFont($id)
    {
        Font::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Font restored successfully.');
    }

    public function deleteFont($id)
    {
        $font = Font::onlyTrashed()->findOrFail($id);
        if ($font->file) Storage::disk('public')->delete($font->file);
        $font->forceDelete();
        return back()->with('success', 'Font permanently deleted.');
    }

    // =========================================================
    // DOWNLOAD ALL IMAGES (existing)
    // =========================================================
    public function downloadImages()
    {
        $zip         = new ZipArchive();
        $zipFileName = storage_path('app/recycle-bin-images.zip');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'ZIP file create nahi ho saki.');
        }

        foreach (Banner::onlyTrashed()->get() as $item) {
            foreach (['background_image', 'mobile_background_image', 'png_image'] as $field) {
                if ($item->$field) {
                    $path = storage_path('app/public/' . $item->$field);
                    if (file_exists($path)) $zip->addFile($path, 'Banners/' . basename($path));
                }
            }
        }

        foreach (Blog::onlyTrashed()->get() as $item) {
            if ($item->image) {
                $path = storage_path('app/public/' . $item->image);
                if (file_exists($path)) $zip->addFile($path, 'Blogs/' . basename($path));
            }
            if ($item->video) {
                $path = storage_path('app/public/' . $item->video);
                if (file_exists($path)) $zip->addFile($path, 'Blogs/Videos/' . basename($path));
            }
        }

        foreach (Testimonial::onlyTrashed()->get() as $item) {
            if ($item->image) {
                $path = storage_path('app/public/' . $item->image);
                if (file_exists($path)) $zip->addFile($path, 'Testimonials/' . basename($path));
            }
        }

        foreach (Product::onlyTrashed()->get() as $item) {
            if ($item->image) {
                $path = storage_path('app/public/' . $item->image);
                if (file_exists($path)) $zip->addFile($path, 'Products/' . basename($path));
            }
            foreach ($item->gallery_images ?? [] as $img) {
                $path = storage_path('app/public/' . $img);
                if (file_exists($path)) $zip->addFile($path, 'Products/Gallery/' . basename($path));
            }
        }

        foreach (\App\Models\DealImage::withTrashed()->get() as $img) {
            $cleanPath = str_replace('/storage/', '', $img->image_path);
            $path      = storage_path('app/public/' . $cleanPath);
            if (file_exists($path)) $zip->addFile($path, 'Deals/Images/' . basename($path));
        }

        foreach (\App\Models\DealBanner::withTrashed()->get() as $banner) {
            $cleanPath = str_replace('/storage/', '', $banner->image_path);
            $path      = storage_path('app/public/' . $cleanPath);
            if (file_exists($path)) $zip->addFile($path, 'Deals/Banners/' . basename($path));
        }

        foreach (Video::onlyTrashed()->get() as $item) {
            if ($item->thumbnail) {
                $path = storage_path('app/public/' . $item->thumbnail);
                if (file_exists($path)) $zip->addFile($path, 'Videos/' . basename($path));
            }
        }

        foreach (Category::onlyTrashed()->get() as $item) {
            foreach (['icon_image', 'highlight_image'] as $field) {
                if ($item->$field) {
                    $cleanPath = ltrim(str_replace('/storage/', '', $item->$field), '/');
                    $path      = storage_path('app/public/' . $cleanPath);
                    if (file_exists($path)) $zip->addFile($path, 'Categories/' . basename($path));
                }
            }
        }

        foreach (Flipbook::onlyTrashed()->get() as $item) {
            if ($item->file_path) {
                $path = storage_path('app/public/' . $item->file_path);
                if (file_exists($path)) $zip->addFile($path, 'Flipbooks/' . basename($path));
            }
        }

        foreach (Font::onlyTrashed()->get() as $item) {
            if ($item->file) {
                $path = storage_path('app/public/' . $item->file);
                if (file_exists($path)) $zip->addFile($path, 'Fonts/' . basename($path));
            }
        }

        foreach (Pattern::onlyTrashed()->get() as $item) {
            if ($item->svg_path) {
                $path = storage_path('app/public/' . $item->svg_path);
                if (file_exists($path)) $zip->addFile($path, 'Patterns/' . basename($path));
            }
        }

        $zip->close();

        return response()->download($zipFileName, 'recycle-bin-images.zip')->deleteFileAfterSend(true);
    }

    // =========================================================
    // EXPORT BACKUP  (WordPress style — JSON + files → ZIP)
    // =========================================================
    public function exportBackup()
    {
        $zip         = new ZipArchive();
        $zipFileName = storage_path('app/recycle-bin-backup-' . now()->format('Ymd_His') . '.zip');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'ZIP file create nahi ho saki.');
        }

        // ── Sab trashed records JSON mein ──
        $data = [
            'exported_at'       => now()->toDateTimeString(),
            'artworks'          => ArtworkRequest::onlyTrashed()->get()->toArray(),
            'banners'           => Banner::onlyTrashed()->get()->toArray(),
            'blogs'             => Blog::onlyTrashed()->get()->toArray(),
            'testimonials'      => Testimonial::onlyTrashed()->get()->toArray(),
            'flipbooks'         => Flipbook::onlyTrashed()->get()->toArray(),
            'products'          => Product::onlyTrashed()->get()->toArray(),
            'deals'             => Deal::onlyTrashed()->get()->toArray(),
            'deal_images'       => \App\Models\DealImage::withTrashed()->get()->toArray(),
            'deal_banners'      => \App\Models\DealBanner::withTrashed()->get()->toArray(),
            'videos'            => Video::onlyTrashed()->get()->toArray(),
            'categories'        => Category::onlyTrashed()->get()->toArray(),
            'navigations'       => Navigation::onlyTrashed()->get()->toArray(),
            'customizer_models' => CustomizerModel::onlyTrashed()->get()->toArray(),
            'patterns'          => Pattern::onlyTrashed()->get()->toArray(),
            'colors'            => Color::onlyTrashed()->get()->toArray(),
            'templates'         => Template::onlyTrashed()->get()->toArray(),
            'fonts'             => Font::onlyTrashed()->get()->toArray(),
        ];

        $zip->addFromString('backup-data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // ── Helper: file ZIP mein add karo ──
        $addFile = function ($relativePath, $folder) use ($zip) {
            if (!$relativePath) return;
            $abs = storage_path('app/public/' . ltrim($relativePath, '/'));
            if (file_exists($abs)) {
                $zip->addFile($abs, 'files/' . $folder . '/' . basename($abs));
            }
        };

        foreach ($data['banners'] as $r) {
            $addFile($r['background_image'],        'banners');
            $addFile($r['mobile_background_image'], 'banners');
            $addFile($r['png_image'],               'banners');
        }

        foreach ($data['blogs'] as $r) {
            $addFile($r['image'], 'blogs');
            $addFile($r['video'] ?? null, 'blogs/videos');
        }

        foreach ($data['testimonials'] as $r) {
            $addFile($r['image'], 'testimonials');
        }

        foreach ($data['flipbooks'] as $r) {
            $addFile($r['file_path'], 'flipbooks');
        }

        foreach ($data['products'] as $r) {
            $addFile($r['image'], 'products');
            foreach ($r['gallery_images'] ?? [] as $img) $addFile($img, 'products/gallery');
            foreach ($r['colors'] ?? [] as $c) {
                $addFile($c['image'] ?? null, 'products/colors');
                foreach ($c['gallery'] ?? [] as $gp) $addFile($gp, 'products/colors/gallery');
            }
            $addFile($r['size_chart_image'] ?? null, 'products');
        }

        foreach ($data['deal_images'] as $r) {
            $clean = ltrim(str_replace('/storage/', '', $r['image_path']), '/');
            $abs   = storage_path('app/public/' . $clean);
            if (file_exists($abs)) $zip->addFile($abs, 'files/deals/images/' . basename($abs));
        }

        foreach ($data['deal_banners'] as $r) {
            $clean = ltrim(str_replace('/storage/', '', $r['image_path']), '/');
            $abs   = storage_path('app/public/' . $clean);
            if (file_exists($abs)) $zip->addFile($abs, 'files/deals/banners/' . basename($abs));
        }

        foreach ($data['videos'] as $r) {
            $addFile($r['thumbnail'], 'videos');
        }

        foreach ($data['categories'] as $r) {
            $addFile($r['icon_image'] ?? null,      'categories');
            $addFile($r['highlight_image'] ?? null, 'categories');
        }

        foreach ($data['customizer_models'] as $r) {
            if (!empty($r['thumbnail'])) {
                $abs = public_path('uploads/models/' . $r['thumbnail']);
                if (file_exists($abs)) $zip->addFile($abs, 'files/models/' . $r['thumbnail']);
            }
        }

        foreach ($data['patterns'] as $r) {
            $addFile($r['svg_path'] ?? null, 'patterns');
        }

        foreach ($data['fonts'] as $r) {
            $addFile($r['file'] ?? null, 'fonts');
        }

        $zip->close();

        $fileName = 'recycle-bin-backup-' . now()->format('Ymd_His') . '.zip';
        return response()->download($zipFileName, $fileName)->deleteFileAfterSend(true);
    }

    // =========================================================
    // IMPORT BACKUP  (ZIP upload → records + files restore)
    // =========================================================
    public function importBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:204800', // 200MB max
        ]);

        $zip         = new ZipArchive();
        $zipPath     = $request->file('backup_file')->store('temp-restore', 'local');
        $zipFullPath = storage_path('app/' . $zipPath);
        $extractPath = storage_path('app/temp-restore-' . uniqid());

        if ($zip->open($zipFullPath) !== true) {
            return back()->with('error', 'ZIP file open nahi ho saka. File corrupt ho sakti hai.');
        }

        $zip->extractTo($extractPath);
        $zip->close();

        // ── JSON check ──
        $jsonFile = $extractPath . '/backup-data.json';
        if (!file_exists($jsonFile)) {
            $this->cleanupTemp($zipFullPath, $extractPath);
            return back()->with('error', 'Yeh valid backup ZIP nahi hai (backup-data.json nahi mili).');
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        DB::beginTransaction();
        try {
            $this->restoreModel(ArtworkRequest::class,     $data['artworks']          ?? []);
            $this->restoreModel(Banner::class,             $data['banners']            ?? []);
            $this->restoreModel(Blog::class,               $data['blogs']              ?? []);
            $this->restoreModel(Testimonial::class,        $data['testimonials']       ?? []);
            $this->restoreModel(Flipbook::class,           $data['flipbooks']          ?? []);
            $this->restoreModel(Product::class,            $data['products']           ?? []);
            $this->restoreModel(Deal::class,               $data['deals']              ?? []);
            $this->restoreModel(\App\Models\DealImage::class,  $data['deal_images']   ?? []);
            $this->restoreModel(\App\Models\DealBanner::class, $data['deal_banners']  ?? []);
            $this->restoreModel(Video::class,              $data['videos']             ?? []);
            $this->restoreModel(Category::class,           $data['categories']         ?? []);
            $this->restoreModel(Navigation::class,         $data['navigations']        ?? []);
            $this->restoreModel(CustomizerModel::class,    $data['customizer_models']  ?? []);
            $this->restoreModel(Pattern::class,            $data['patterns']           ?? []);
            $this->restoreModel(Color::class,              $data['colors']             ?? []);
            $this->restoreModel(Template::class,           $data['templates']          ?? []);
            $this->restoreModel(Font::class,               $data['fonts']              ?? []);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->cleanupTemp($zipFullPath, $extractPath);
            return back()->with('error', 'Database restore fail hua: ' . $e->getMessage());
        }

        // ── Files copy karo ──
        $filesDir = $extractPath . '/files';
        if (is_dir($filesDir)) {
            $this->copyFilesRecursive($filesDir, storage_path('app/public'));
        }

        $this->cleanupTemp($zipFullPath, $extractPath);

        return back()->with('success', 'Backup successfully import ho gaya! Sab records aur files restore ho gaye.');
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Insert rows into DB — agar ID already exist kare to skip karo (no duplicates)
     */
    private function restoreModel(string $modelClass, array $rows): void
    {
        if (empty($rows)) return;

        $instance       = new $modelClass;
        $table          = $instance->getTable();
        $fillable       = $instance->getFillable();
        $usesTimestamps = $instance->usesTimestamps();

        $allowed = array_merge(['id', 'deleted_at'], $fillable);
        if ($usesTimestamps) {
            $allowed[] = 'created_at';
            $allowed[] = 'updated_at';
        }

        foreach ($rows as $row) {
            $id = $row['id'] ?? null;

            // Already exist karta hai to skip
            if ($id && DB::table($table)->where('id', $id)->exists()) {
                continue;
            }

            $insert = array_filter(
                array_intersect_key($row, array_flip($allowed)),
                fn($v) => $v !== null
            );

            // Array values ko JSON string mein convert karo (DB array columns ke liye)
            // DateTime format fix karo: '2026-03-26T07:10:22.000000Z' → '2026-03-26 07:10:22'
            foreach ($insert as $key => $value) {
                if (is_array($value)) {
                    $insert[$key] = json_encode($value);
                } elseif (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                    $insert[$key] = date('Y-m-d H:i:s', strtotime($value));
                }
            }

            if (!empty($insert)) {
                DB::table($table)->insert($insert);
            }
        }
    }

    /**
     * Extracted ZIP files ko storage mein copy karo
     */
    private function copyFilesRecursive(string $src, string $dest): void
    {
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            $srcPath  = $src  . DIRECTORY_SEPARATOR . $item;
            $destPath = $dest . DIRECTORY_SEPARATOR . $item;
            if (is_dir($srcPath)) {
                if (!is_dir($destPath)) mkdir($destPath, 0775, true);
                $this->copyFilesRecursive($srcPath, $destPath);
            } elseif (!file_exists($destPath)) {
                copy($srcPath, $destPath);
            }
        }
    }

    /**
     * Temp files/folders clean karo
     */
    private function cleanupTemp(string $zipPath, string $extractPath): void
    {
        if (file_exists($zipPath)) @unlink($zipPath);

        if (is_dir($extractPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($extractPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $f) {
                $f->isDir() ? rmdir($f->getRealPath()) : unlink($f->getRealPath());
            }
            rmdir($extractPath);
        }
    }
}
