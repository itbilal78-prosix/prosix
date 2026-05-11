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


use Illuminate\Support\Facades\Storage;

class RecycleBinController extends Controller
{
    public function index()
    {
        $artworks     = ArtworkRequest::onlyTrashed()->latest('deleted_at')->get();
        $banners      = Banner::onlyTrashed()->latest('deleted_at')->get();
        $blogs        = Blog::onlyTrashed()->latest('deleted_at')->get();
        $testimonials = Testimonial::onlyTrashed()->latest('deleted_at')->get();
        $flipbooks    = Flipbook::onlyTrashed()->latest('deleted_at')->get();
        $products     = Product::onlyTrashed()->latest('deleted_at')->get();
        $deals        = Deal::onlyTrashed()->latest('deleted_at')->get();
        $videos = Video::onlyTrashed()->latest('deleted_at')->get();
        $categories = Category::onlyTrashed()->latest('deleted_at')->get();
        $navigations = Navigation::onlyTrashed()->latest('deleted_at')->get();
$customizerModels = CustomizerModel::onlyTrashed()->latest('deleted_at')->get();
$patterns = Pattern::onlyTrashed()->latest('deleted_at')->get();
$colors = Color::onlyTrashed()->latest('deleted_at')->get();
$templates = Template::onlyTrashed()->latest('deleted_at')->get();
$fonts = Font::onlyTrashed()->latest('deleted_at')->get();




return view('admin.recycle-bin.index', compact(
    'artworks', 'banners', 'blogs', 'testimonials',
    'flipbooks', 'products', 'deals', 'videos',
    'categories', 'navigations', 'customizerModels',
    'patterns', 'colors', 'templates', 'fonts'
));

    }

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

public function restoreDeal($id)
{
    $deal = Deal::onlyTrashed()->findOrFail($id);
    $deal->restore();

    // ✅ Images aur Banners bhi restore karo
    \App\Models\DealImage::withTrashed()->where('deal_id', $id)->restore();
    \App\Models\DealBanner::withTrashed()->where('deal_id', $id)->restore();

    return back()->with('success', 'Deal restored successfully.');
}

  public function deleteDeal($id)
{
    $deal = Deal::onlyTrashed()->findOrFail($id);

    // ✅ Images aur Banners files + records delete karo
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
public function restoreCategory($id)
{
    $category = Category::onlyTrashed()->findOrFail($id);
    $category->restore();

    // subcategories bhi restore karo
    Category::onlyTrashed()->where('parent_id', $id)->restore();

    return back()->with('success', 'Category restored successfully.');
}

public function deleteCategory($id)
{
    $category = Category::onlyTrashed()->findOrFail($id);

    // subcategories bhi permanently delete karo
    Category::onlyTrashed()->where('parent_id', $id)->forceDelete();

    $category->forceDelete();
    return back()->with('success', 'Category permanently deleted.');
}
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





public function downloadImages()
{
    $zip = new \ZipArchive();
    $zipFileName = storage_path('app/recycle-bin-images.zip');

    if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
        return back()->with('error', 'ZIP file create nahi ho saki.');
    }

    // Banners
    foreach (Banner::onlyTrashed()->get() as $item) {
        foreach (['background_image', 'mobile_background_image', 'png_image'] as $field) {
            if ($item->$field) {
                $path = storage_path('app/public/' . $item->$field);
                if (file_exists($path)) $zip->addFile($path, 'Banners/' . basename($path));
            }
        }
    }

    // Blogs
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

    // Testimonials
    foreach (Testimonial::onlyTrashed()->get() as $item) {
        if ($item->image) {
            $path = storage_path('app/public/' . $item->image);
            if (file_exists($path)) $zip->addFile($path, 'Testimonials/' . basename($path));
        }
    }

    // Products
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

    // Deals Images
    foreach (\App\Models\DealImage::withTrashed()->get() as $img) {
        $cleanPath = str_replace('/storage/', '', $img->image_path);
        $path = storage_path('app/public/' . $cleanPath);
        if (file_exists($path)) $zip->addFile($path, 'Deals/Images/' . basename($path));
    }

    // Deals Banners
    foreach (\App\Models\DealBanner::withTrashed()->get() as $banner) {
        $cleanPath = str_replace('/storage/', '', $banner->image_path);
        $path = storage_path('app/public/' . $cleanPath);
        if (file_exists($path)) $zip->addFile($path, 'Deals/Banners/' . basename($path));
    }

    // Videos
    foreach (Video::onlyTrashed()->get() as $item) {
        if ($item->thumbnail) {
            $path = storage_path('app/public/' . $item->thumbnail);
            if (file_exists($path)) $zip->addFile($path, 'Videos/' . basename($path));
        }
    }

    // Categories
    foreach (Category::onlyTrashed()->get() as $item) {
        foreach (['icon_image', 'highlight_image'] as $field) {
            if ($item->$field) {
                $cleanPath = ltrim(str_replace('/storage/', '', $item->$field), '/');
                $path = storage_path('app/public/' . $cleanPath);
                if (file_exists($path)) $zip->addFile($path, 'Categories/' . basename($path));
            }
        }
    }

    // Flipbooks
    foreach (Flipbook::onlyTrashed()->get() as $item) {
        if ($item->file_path) {
            $path = storage_path('app/public/' . $item->file_path);
            if (file_exists($path)) $zip->addFile($path, 'Flipbooks/' . basename($path));
        }
    }

    // Fonts
    foreach (Font::onlyTrashed()->get() as $item) {
        if ($item->file) {
            $path = storage_path('app/public/' . $item->file);
            if (file_exists($path)) $zip->addFile($path, 'Fonts/' . basename($path));
        }
    }

    // Patterns
    foreach (Pattern::onlyTrashed()->get() as $item) {
        if ($item->svg_path) {
            $path = storage_path('app/public/' . $item->svg_path);
            if (file_exists($path)) $zip->addFile($path, 'Patterns/' . basename($path));
        }
    }

    $zip->close();

    return response()->download($zipFileName, 'recycle-bin-images.zip')->deleteFileAfterSend(true);
}

}
