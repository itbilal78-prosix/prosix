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
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserCustomizationController;
use App\Http\Controllers\RecycleBinController;
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
Route::get('/admin/artwork-requests', [ArtworkRequestController::class, 'index'])
    ->middleware(['auth:admin'])
    ->name('admin.artwork');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin'])
    ->group(function () {

        Route::middleware(['admin.permission:can_orders'])
            ->get('/orders', [OrderController::class, 'adminIndex'])
            ->name('orders.index');

        Route::get('/orders-unread-count', [OrderController::class, 'unreadCount'])
            ->name('orders.unreadCount');

        // ✅ bulk-status PEHLE — {id} se pehle hona zaroori hai
        Route::post('/orders/bulk-status', [OrderController::class, 'bulkUpdateStatus'])
            ->name('orders.bulkStatus');

        Route::get('/orders/{id}', [OrderController::class, 'adminShow'])
            ->name('orders.show');

        Route::post('/orders/{id}/status', [OrderController::class,'updateStatus'])
            ->name('orders.updateStatus');

        Route::post('/orders/{id}/shipping', [OrderController::class,'updateShipping'])
            ->name('orders.updateShipping');

        Route::post('/orders/{id}/notes', [OrderController::class,'updateNotes'])
            ->name('orders.updateNotes');
            Route::post('/orders/download-pdf', [OrderController::class, 'downloadPdf'])
    ->name('orders.downloadPdf');
    });


Route::get('/users/{id}/login', [AuthController::class, 'loginAsUser'])
    ->name('admin.users.loginAsUser');

// Admin protected routes
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::post('products/featured', [ProductController::class, 'featured'])->name('products.featured');
    Route::post('products/apparel', [ProductController::class, 'apparel'])->name('products.apparel');
    Route::post('products/bulk-category', [ProductController::class, 'bulkCategory'])->name('products.bulkCategory');
Route::post('products/duplicate-category', [ProductController::class, 'duplicateCategory'])
    ->name('products.duplicateCategory');
    

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



// Route::post('/models/reorder',[CustomizerModelController::class,'reorder'])->name('models.reorder');
Route::get('/user/categories-with-models', [CustomizerModelController::class, 'userCategoriesWithModels']);

Route::post('templates/bulk-destroy', [TemplateController::class, 'bulkDestroy'])
     ->name('templates.bulkDestroy');
Route::resource('templates', TemplateController::class);

Route::post('/templates/save-from-customizer',
    [TemplateController::class, 'saveFromCustomizer']
);
Route::get('/api/mascot-templates', function () {
    return \App\Models\Template::with('category')
        ->latest()
        ->get()
        ->map(function ($t) {
            return [
                'id'         => $t->id,
                'title'      => $t->title,
                'svg_data'   => $t->svg_data,
                'image_data' => $t->image_data,
                'category'   => $t->category ? $t->category->name : 'Uncategorized',
                'category_id'=> $t->category_id,
            ];
        });
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


Route::get('/admin/payments', [PaymentController::class, 'index'])
    ->name('admin.payments.index');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
Route::get('/admin/place-orders', [PlaceOrderController::class, 'index'])
    ->name('admin.placeorder');
Route::get('/order/download/{id}', [PlaceOrderController::class, 'downloadSinglePdf'])
    ->name('order.download.single');


// Route::get('/customize/{id}', function ($id) {

//     $model = \App\Models\CustomizerModel::findOrFail($id);

//     $colors = \App\Models\Color::all();

//     $fonts = \App\Models\Font::all()->map(function ($font) {
//         return [
//             'id' => $font->id,
//             'name' => $font->name,
//             'file_url' => asset('storage/' . $font->file),
//         ];
//     });

//     return view('admin.models.show', compact('model', 'colors', 'fonts'))
//         ->with('isUserMode', true);

// });
Route::get('/customize/{id}', function ($id, Illuminate\Http\Request $request) {

    $model = \App\Models\CustomizerModel::findOrFail($id);
    $colors = \App\Models\Color::all();
    $fonts = \App\Models\Font::all()->map(function ($font) {
        return [
            'id'       => $font->id,
            'name'     => $font->name,
            'file_url' => asset('storage/' . $font->file),
        ];
    });

    $design = null;
    if ($request->has('design_id')) {
        $design = \App\Models\UserCustomization::find($request->design_id);
    }

    return view('admin.models.show', compact('model', 'colors', 'fonts', 'design'))
        ->with('isUserMode', true);
});


Route::post('/models/update-order',
    [CustomizerModelController::class,'updateOrder']
)->name('models.updateOrder');





Route::get('/user/model-api/{id}', [CustomizerModelController::class, 'userApi']);



Route::get('/api/fonts', function() {
    return \App\Models\Font::all()->map(function($font) {
        return [
            'id' => $font->id,
            'name' => $font->name,
            'file_url' => asset('storage/' . $font->file)
        ];
    });
});



Route::middleware(['auth:admin'])
    ->prefix('admin')
    ->group(function () {

    // 👇 YAHAN ADD KARO
    Route::post('models/{id}/toggle-hidden', [CustomizerModelController::class, 'toggleHidden'])
        ->name('models.toggleHidden');

    Route::post('models/bulk-toggle-hidden', [CustomizerModelController::class, 'bulkToggleHidden'])
        ->name('models.bulkToggleHidden');

});
Route::delete('/admin/artwork-requests/{id}', [ArtworkRequestController::class, 'destroy'])
    ->middleware(['auth:admin'])
    ->name('admin.artwork.destroy');
Route::get('/api/categories-for-templates', function () {
    return \App\Models\Category::whereNull('parent_id')
        ->where('status', 1)
        ->select('id', 'name')
        ->orderBy('name')
        ->get();
});

Route::get('/admin/forgot-password', [AuthController::class, 'showForgotForm'])
    ->name('admin.password.request');

Route::post('/admin/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('admin.password.send');

Route::get('/admin/reset-password/{token}', [AuthController::class, 'showResetForm'])
    ->name('admin.password.reset.form');

Route::post('/admin/reset-password', [AuthController::class, 'resetPassword'])
    ->name('admin.password.reset');
Route::post('/users/{id}/pin', [AuthController::class, 'togglePin'])
    ->name('admin.users.pin');
Route::get('/admin/place-orders-unread-count', [PlaceOrderController::class, 'unreadCount'])
    ->name('placeorders.unreadCount');
Route::post('/admin/place-orders-mark-read',
    [PlaceOrderController::class, 'markAllRead']);

Route::get('/admin/customers', [AuthController::class, 'customers'])
    ->middleware(['auth:admin'])
    ->name('admin.customers');


Route::get('/admin/artwork-unread-count',
    [ArtworkRequestController::class, 'unreadCount'])
    ->name('artwork.unreadCount');

Route::post('/admin/artwork-mark-read',
    [ArtworkRequestController::class, 'markAllRead'])
    ->middleware(['auth:admin']);

// ── Membership Requests ───────────────────────────────────────
Route::get('/admin/membership-unread-count',
    [MembershipRequestController::class, 'unreadCount'])
    ->name('membership.unreadCount');

Route::post('/admin/membership-mark-read',
    [MembershipRequestController::class, 'markAllRead'])
    ->middleware(['auth:admin']);

Route::get('/recycle-bin', [RecycleBinController::class, 'index'])
    ->name('recycle-bin.index');

Route::post('/recycle-bin/artwork/{id}/restore', [RecycleBinController::class, 'restoreArtwork'])
    ->name('recycle-bin.artwork.restore');

Route::delete('/recycle-bin/artwork/{id}/delete', [RecycleBinController::class, 'deleteArtwork'])
    ->name('recycle-bin.artwork.delete');
Route::post('/recycle-bin/banner/{id}/restore', [RecycleBinController::class, 'restoreBanner'])
    ->name('recycle-bin.banner.restore');

Route::delete('/recycle-bin/banner/{id}/delete', [RecycleBinController::class, 'deleteBanner'])
    ->name('recycle-bin.banner.delete');

Route::post('/recycle-bin/blog/{id}/restore', [RecycleBinController::class, 'restoreBlog'])
    ->name('recycle-bin.blog.restore');

Route::delete('/recycle-bin/blog/{id}/delete', [RecycleBinController::class, 'deleteBlog'])
    ->name('recycle-bin.blog.delete');

Route::post('/recycle-bin/testimonial/{id}/restore', [RecycleBinController::class, 'restoreTestimonial'])
    ->name('recycle-bin.testimonial.restore');

Route::delete('/recycle-bin/testimonial/{id}/delete', [RecycleBinController::class, 'deleteTestimonial'])
    ->name('recycle-bin.testimonial.delete');


Route::post('/recycle-bin/flipbook/{id}/restore', [RecycleBinController::class, 'restoreFlipbook'])
    ->name('recycle-bin.flipbook.restore');

Route::delete('/recycle-bin/flipbook/{id}/delete', [RecycleBinController::class, 'deleteFlipbook'])
    ->name('recycle-bin.flipbook.delete');
Route::post('/recycle-bin/product/{id}/restore', [RecycleBinController::class, 'restoreProduct'])
    ->name('recycle-bin.product.restore');

Route::delete('/recycle-bin/product/{id}/delete', [RecycleBinController::class, 'deleteProduct'])
    ->name('recycle-bin.product.delete');



Route::post('/recycle-bin/deal/{id}/restore', [RecycleBinController::class, 'restoreDeal'])
    ->name('recycle-bin.deal.restore');

Route::delete('/recycle-bin/deal/{id}/delete', [RecycleBinController::class, 'deleteDeal'])
    ->name('recycle-bin.deal.delete');

Route::post('/recycle-bin/video/{id}/restore', [RecycleBinController::class, 'restoreVideo'])
    ->name('recycle-bin.video.restore');

Route::delete('/recycle-bin/video/{id}/delete', [RecycleBinController::class, 'deleteVideo'])
    ->name('recycle-bin.video.delete');

Route::post('/recycle-bin/category/{id}/restore', [RecycleBinController::class, 'restoreCategory'])
    ->name('recycle-bin.category.restore');

Route::delete('/recycle-bin/category/{id}/delete', [RecycleBinController::class, 'deleteCategory'])
    ->name('recycle-bin.category.delete');

Route::post('/recycle-bin/navigation/{id}/restore', [RecycleBinController::class, 'restoreNavigation'])
    ->name('recycle-bin.navigation.restore');

Route::delete('/recycle-bin/navigation/{id}/delete', [RecycleBinController::class, 'deleteNavigation'])
    ->name('recycle-bin.navigation.delete');
Route::post('/recycle-bin/customizer-model/{id}/restore', [RecycleBinController::class, 'restoreCustomizerModel'])
    ->name('recycle-bin.customizer-model.restore');

Route::delete('/recycle-bin/customizer-model/{id}/delete', [RecycleBinController::class, 'deleteCustomizerModel'])
    ->name('recycle-bin.customizer-model.delete');
Route::post('/recycle-bin/pattern/{id}/restore', [RecycleBinController::class, 'restorePattern'])
    ->name('recycle-bin.pattern.restore');

Route::delete('/recycle-bin/pattern/{id}/delete', [RecycleBinController::class, 'deletePattern'])
    ->name('recycle-bin.pattern.delete');
Route::post('/recycle-bin/color/{id}/restore', [RecycleBinController::class, 'restoreColor'])
    ->name('recycle-bin.color.restore');

Route::delete('/recycle-bin/color/{id}/delete', [RecycleBinController::class, 'deleteColor'])
    ->name('recycle-bin.color.delete');
Route::post('/recycle-bin/template/{id}/restore', [RecycleBinController::class, 'restoreTemplate'])
    ->name('recycle-bin.template.restore');

Route::delete('/recycle-bin/template/{id}/delete', [RecycleBinController::class, 'deleteTemplate'])
    ->name('recycle-bin.template.delete');

Route::post('/recycle-bin/font/{id}/restore', [RecycleBinController::class, 'restoreFont'])
    ->name('recycle-bin.font.restore');

Route::delete('/recycle-bin/font/{id}/delete', [RecycleBinController::class, 'deleteFont'])
    ->name('recycle-bin.font.delete');

Route::get('/recycle-bin/download-images', [RecycleBinController::class, 'downloadImages'])
    ->name('recycle-bin.download-images');



    Route::get('/recycle-bin/export-backup', [RecycleBinController::class, 'exportBackup'])
    ->name('recycle-bin.export-backup');

Route::post('/recycle-bin/import-backup', [RecycleBinController::class, 'importBackup'])
    ->name('recycle-bin.import-backup');






Route::middleware(['auth:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/website-backup', [\App\Http\Controllers\WebsiteBackupController::class, 'index'])->name('website-backup.index');
    Route::post('/website-backup/create', [\App\Http\Controllers\WebsiteBackupController::class, 'create'])->name('website-backup.create');
    Route::get('/website-backup/download', [\App\Http\Controllers\WebsiteBackupController::class, 'download'])->name('website-backup.download');
    Route::delete('/website-backup/delete', [\App\Http\Controllers\WebsiteBackupController::class, 'delete'])->name('website-backup.delete');
    Route::post('/website-backup/restore', [\App\Http\Controllers\WebsiteBackupController::class, 'restore'])->name('website-backup.restore');
    Route::post('/website-backup/restore-db', [\App\Http\Controllers\WebsiteBackupController::class, 'restoreDb'])->name('website-backup.restore-db');

});

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])
        ->name('activity-logs.index');
});


Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!admin).*$');
