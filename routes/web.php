<?php

use App\Http\Controllers\ArtworkRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomizerModelController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\FlipbookController;
use App\Http\Controllers\FontController;
use App\Http\Controllers\Frontend\CustomizerModelController as FrontModel;
use App\Http\Controllers\MembershipRequestController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PatternController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlaceOrderController;

// Login routes
Route::prefix('admin')->name('admin.')->group(function () {

    // Login/logout
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin dashboard (protected)
    Route::middleware(['auth:admin'])->group(function () {

        // User management
        Route::get('/users', [AuthController::class, 'index'])->name('users.index');
        Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp']);
    });
});

Route::middleware(['auth:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ✅ SAHI JAGAH — PREFIX + NAME ALREADY SET HAI
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('admins', \App\Http\Controllers\AdminManagerController::class);
        Route::get('/memberships', [MembershipRequestController::class, 'index'])->name('memberships');
        Route::resource('flipbooks', FlipbookController::class);
    });
Route::get('/artwork-requests', [ArtworkRequestController::class, 'index'])
    ->name('admin.artwork');
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin'])
    ->group(function () {

        // ✅ FIX
        Route::middleware(['auth:admin', 'admin.permission:can_orders'])
            ->get('/orders', [OrderController::class, 'adminIndex'])
            ->name('orders.index');  // ← yeh add karo

        Route::get('/orders/{id}', [OrderController::class, 'adminShow'])
            ->name('orders.show');
    });
// Admin protected routes
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::post('products/featured', [ProductController::class, 'featured'])->name('products.featured');
    Route::post('products/apparel', [ProductController::class, 'apparel'])->name('products.apparel');
    Route::post('products/bulk-category', [ProductController::class, 'bulkCategory'])->name('products.bulkCategory');

    Route::middleware(['auth:admin', 'admin.permission:can_products'])
        ->resource('products', ProductController::class);
    // Models
    Route::middleware(['auth:admin', 'admin.permission:can_customizer'])
        ->resource('models', CustomizerModelController::class)->names([
            'index' => 'models.index',
            'create' => 'models.create',
            'store' => 'models.store',
            'show' => 'models.show',
            'edit' => 'models.edit',
            'update' => 'models.update',
            'destroy' => 'models.destroy',
        ]);

    Route::post('models/{id}/duplicate', [CustomizerModelController::class, 'duplicate'])->name('models.duplicate');
    Route::get('models/{model}/api', [CustomizerModelController::class, 'api'])->name('models.api.get');
    Route::post('models/{id}/save-design', [CustomizerModelController::class, 'saveDesign'])->name('admin.models.save-design');
    Route::post('models/{id}/save-thumbnail', [CustomizerModelController::class, 'saveThumbnail'])->name('models.save-thumbnail');

    Route::post('/models/featured', [CustomizerModelController::class, 'bulkFeatured'])
        ->name('models.featured');

    Route::post('/models/apparel', [CustomizerModelController::class, 'bulkApparel'])
        ->name('models.apparel');

    Route::patch('/users/{id}/toggle', [AuthController::class, 'toggleStatus'])
        ->name('admin.users.toggle');

    // Other resources
    Route::resource('colors', ColorController::class);
    Route::resource('fonts', FontController::class);
    Route::resource('patterns', PatternController::class);
    Route::resource('banners', BannerController::class);
    Route::resource('socials', SocialMediaController::class);
    Route::middleware(['auth:admin', 'admin.permission:can_categories'])
        ->resource('categories', CategoryController::class)->except(['show']);
    Route::get('categories/sub-create', [CategoryController::class, 'subCreate'])->name('categories.subcreate');

    Route::resource('navigations', NavigationController::class);
    Route::resource('videos', VideoController::class);

    Route::patch(
        'navigations/{navigation}/toggle-status',
        [NavigationController::class, 'toggleStatus']
    )->name('navigations.toggle-status');

    Route::resource('deals', DealController::class);

    Route::resource('blogs', BlogController::class);

    Route::resource('testimonials', TestimonialController::class);

});

Route::patch('categories/{category}/toggle-status',
    [App\Http\Controllers\CategoryController::class, 'toggleStatus']
)->name('categories.toggle-status');



Route::post('/banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');

Route::get('/api/menu-categories/{slug}', [CategoryController::class, 'apiMenuCategories']);
Route::get('/category/{id}', [CategoryController::class, 'products'])
    ->name('category.products');
// web.php
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/membership-download', [MembershipRequestController::class, 'download'])->name('membership.download');
});

Route::get('/api/fonts', function () {
    return \App\Models\Font::select('id', 'name', 'file')->get();
});
Route::get('/api/colors', function () {
    return \App\Models\Color::select('id', 'name', 'code')->get();
});
// Frontend models (ye already hai aapke web.php mein)
Route::get('/models', [FrontModel::class, 'index'])->name('frontend.models');
Route::get('/models/{id}', [FrontModel::class, 'show'])->name('frontend.models.show');
Route::get('/models/{id}/api', [FrontModel::class, 'api'])->name('frontend.models.api');
Route::post('/models/{id}/save-design', [FrontModel::class, 'saveDesign'])->name('frontend.models.save-design');

// Route::post('/models/reorder',[CustomizerModelController::class,'reorder'])->name('models.reorder');
Route::get('/user/categories-with-models', [CustomizerModelController::class, 'userCategoriesWithModels']);

// Template routes
Route::resource('templates', TemplateController::class);

Route::post('/templates/save-from-customizer',
    [TemplateController::class, 'saveFromCustomizer']
);
Route::get('/api/mascot-templates', function () {
    return \App\Models\Template::latest()->get();
});
Route::get('/products/featured', [ProductController::class, 'featured'])
    ->name('products.featured.list');

Route::post('/categories/reorder', [CategoryController::class, 'reorder'])
    ->name('categories.reorder');

Route::post('admin/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('admin.orders.cancel');
// Storage files directly serve karo
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) abort(404);
    return response()->file($fullPath);
})->where('path', '.*');
Route::post('/admin/artwork/download-pdf',    [ArtworkRequestController::class,    'downloadPdf'])->name('artwork.download.pdf');
Route::post('/admin/membership/download-pdf', [MembershipRequestController::class, 'downloadPdf'])->name('membership.download.pdf');
Route::post('/admin/place-orders-download', [PlaceOrderController::class, 'downloadPdf']);
Route::post('/models/bulk-destroy',   [CustomizerModelController::class, 'bulkDestroy'])->name('models.bulkDestroy');
Route::post('/models/bulk-duplicate', [CustomizerModelController::class, 'bulkDuplicate'])->name('models.bulkDuplicate');
Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'search']);

// Mascot editor - edit existing template
// ✅ CREATE pehle hona chahiye
Route::middleware(['auth:admin'])
    ->get('/admin/mascots/create', function () {
        return view('templates.create');
    })->name('mascots.create');

// EDIT baad mein
Route::middleware(['auth:admin'])
    ->get('/admin/mascots/{id}/edit', function ($id) {
        $template = \App\Models\Template::findOrFail($id);
        return view('templates.edit', compact('template'));
    })->name('mascots.edit');



Route::get('/admin/place-orders', [PlaceOrderController::class, 'index'])
    ->name('admin.placeorder');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!admin).*$');
