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

// ═══════════════════════════════════════════
// SUBDOMAIN DETECT
// ═══════════════════════════════════════════
$host         = request()->getHost();
$isAdmin      = str_contains($host, 'admin.');
$isCustomizer = str_contains($host, 'customizer.');

// ═══════════════════════════════════════════
// STRIPE WEBHOOK — koi subdomain nahi chahiye
// ═══════════════════════════════════════════
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

// ═══════════════════════════════════════════════════════════
// ADMIN SUBDOMAIN
// admin.prosix.com/login       → login page
// admin.prosix.com/dashboard   → dashboard
// ═══════════════════════════════════════════════════════════
if ($isAdmin) {

    // Public routes (login, forgot password)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('admin.password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.password.send');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('admin.password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.reset');

    // Protected routes
    Route::middleware(['auth:admin'])->group(function () {

        // Root redirect → dashboard
        Route::get('/', fn() => redirect()->route('admin.dashboard'));

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Users
        Route::get('/users', [AuthController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}/login', [AuthController::class, 'loginAsUser'])->name('admin.users.loginAsUser');
        Route::patch('/users/{id}/toggle', [AuthController::class, 'toggleStatus'])->name('admin.users.toggle');
        Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp']);

        // Admins
        Route::resource('admins', \App\Http\Controllers\AdminManagerController::class);

        // Memberships
        Route::get('/memberships', [MembershipRequestController::class, 'index'])->name('admin.memberships');
        Route::get('/membership-download', [MembershipRequestController::class, 'download'])->name('membership.download');
        Route::post('/membership/download-pdf', [MembershipRequestController::class, 'downloadPdf'])->name('membership.download.pdf');

        // Orders
        Route::middleware(['admin.permission:can_orders'])
            ->get('/orders', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'adminShow'])->name('admin.orders.show');
        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
        Route::post('/orders/{id}/shipping', [OrderController::class, 'updateShipping'])->name('admin.orders.updateShipping');
        Route::post('/orders/{id}/notes', [OrderController::class, 'updateNotes'])->name('admin.orders.updateNotes');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('admin.orders.cancel');

        // Products
        Route::post('products/featured', [ProductController::class, 'featured'])->name('products.featured');
        Route::post('products/apparel', [ProductController::class, 'apparel'])->name('products.apparel');
        Route::post('products/bulk-category', [ProductController::class, 'bulkCategory'])->name('products.bulkCategory');
        Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured.list');
        Route::middleware(['admin.permission:can_products'])
            ->resource('products', ProductController::class);

        // Models / Customizer
        Route::middleware(['admin.permission:can_customizer'])
            ->resource('models', CustomizerModelController::class);
        Route::post('models/{id}/duplicate', [CustomizerModelController::class, 'duplicate'])->name('models.duplicate');
        Route::get('models/{model}/api', [CustomizerModelController::class, 'api'])->name('models.api.get');
        Route::post('models/{id}/save-design', [CustomizerModelController::class, 'saveDesign'])->name('admin.models.save-design');
        Route::post('models/{id}/save-thumbnail', [CustomizerModelController::class, 'saveThumbnail'])->name('models.save-thumbnail');
        Route::post('/models/featured', [CustomizerModelController::class, 'bulkFeatured'])->name('models.featured');
        Route::post('/models/apparel', [CustomizerModelController::class, 'bulkApparel'])->name('models.apparel');
        Route::post('/models/bulk-destroy', [CustomizerModelController::class, 'bulkDestroy'])->name('models.bulkDestroy');
        Route::post('/models/bulk-duplicate', [CustomizerModelController::class, 'bulkDuplicate'])->name('models.bulkDuplicate');
        Route::post('models/{id}/toggle-hidden', [CustomizerModelController::class, 'toggleHidden'])->name('models.toggleHidden');
        Route::post('models/bulk-toggle-hidden', [CustomizerModelController::class, 'bulkToggleHidden'])->name('models.bulkToggleHidden');
        Route::post('/models/update-order', [CustomizerModelController::class, 'updateOrder'])->name('models.updateOrder');

        // Colors, Fonts, Patterns
        Route::resource('colors', ColorController::class);
        Route::resource('fonts', FontController::class);
        Route::resource('patterns', PatternController::class);

        // Banners
        Route::resource('banners', BannerController::class);
        Route::post('/banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');

        // Socials
        Route::resource('socials', SocialMediaController::class);

        // Categories
        Route::middleware(['admin.permission:can_categories'])
            ->resource('categories', CategoryController::class)->except(['show']);
        Route::get('categories/sub-create', [CategoryController::class, 'subCreate'])->name('categories.subcreate');
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

        // Navigations
        Route::resource('navigations', NavigationController::class);
        Route::patch('navigations/{navigation}/toggle-status', [NavigationController::class, 'toggleStatus'])->name('navigations.toggle-status');

        // Videos, Deals, Blogs, Testimonials
        Route::resource('videos', VideoController::class);
        Route::resource('deals', DealController::class);
        Route::resource('blogs', BlogController::class);
        Route::resource('testimonials', TestimonialController::class);

        // Flipbooks
        Route::resource('flipbooks', FlipbookController::class);

        // Artwork
        Route::get('/artwork-requests', [ArtworkRequestController::class, 'index'])->name('admin.artwork');
        Route::post('/artwork/download-pdf', [ArtworkRequestController::class, 'downloadPdf'])->name('artwork.download.pdf');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');

        // Place Orders
        Route::get('/place-orders', [PlaceOrderController::class, 'index'])->name('admin.placeorder');
        Route::post('/place-orders-download', [PlaceOrderController::class, 'downloadPdf']);
        Route::get('/order/download/{id}', [PlaceOrderController::class, 'downloadSinglePdf'])->name('order.download.single');

        // Templates / Mascots
        Route::post('templates/bulk-destroy', [TemplateController::class, 'bulkDestroy'])->name('templates.bulkDestroy');
        Route::resource('templates', TemplateController::class);
        Route::post('/templates/save-from-customizer', [TemplateController::class, 'saveFromCustomizer']);
        Route::get('/mascots/create', fn() => view('templates.create'))->name('mascots.create');
        Route::get('/mascots/{id}/edit', function ($id) {
            $template = \App\Models\Template::findOrFail($id);
            return view('templates.edit', compact('template'));
        })->name('mascots.edit');

        // Storage
        Route::get('/storage/{path}', function ($path) {
            $fullPath = storage_path('app/public/' . $path);
            if (!file_exists($fullPath)) abort(404);
            return response()->file($fullPath);
        })->where('path', '.*');

        // API
        Route::get('/api/fonts', fn() => \App\Models\Font::select('id', 'name', 'file')->get());
        Route::get('/api/colors', fn() => \App\Models\Color::select('id', 'name', 'code')->get());
        Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'search']);
        Route::get('/api/menu-categories/{slug}', [CategoryController::class, 'apiMenuCategories']);
        Route::get('/api/mascot-templates', function () {
            return \App\Models\Template::with('category')->latest()->get()->map(fn($t) => [
                'id'          => $t->id,
                'title'       => $t->title,
                'svg_data'    => $t->svg_data,
                'image_data'  => $t->image_data,
                'category'    => $t->category?->name ?? 'Uncategorized',
                'category_id' => $t->category_id,
            ]);
        });
        Route::get('/api/categories-for-templates', fn() =>
            \App\Models\Category::whereNull('parent_id')->where('status', 1)->select('id', 'name')->orderBy('name')->get()
        );
        Route::get('/user/model-api/{id}', [CustomizerModelController::class, 'userApi']);

    }); // end auth:admin

// ═══════════════════════════════════════════════════════════
// CUSTOMIZER SUBDOMAIN
// customizer.prosix.com/customize/{id}
// ═══════════════════════════════════════════════════════════
} elseif ($isCustomizer) {

    Route::get('/', fn() => redirect('/customize/1'));

    Route::get('/customize/{id}', function ($id, Illuminate\Http\Request $request) {
        $model  = \App\Models\CustomizerModel::findOrFail($id);
        $colors = \App\Models\Color::all();
        $fonts  = \App\Models\Font::all()->map(fn($font) => [
            'id'       => $font->id,
            'name'     => $font->name,
            'file_url' => asset('storage/' . $font->file),
        ]);
        $design = $request->has('design_id')
            ? \App\Models\UserCustomization::find($request->design_id)
            : null;
        return view('admin.models.show', compact('model', 'colors', 'fonts', 'design'))
            ->with('isUserMode', true);
    });

    Route::get('/user/model-api/{id}', [CustomizerModelController::class, 'userApi']);
    Route::get('/user/categories-with-models', [CustomizerModelController::class, 'userCategoriesWithModels']);
    Route::post('/templates/save-from-customizer', [TemplateController::class, 'saveFromCustomizer']);

    Route::get('/api/fonts', fn() => \App\Models\Font::all()->map(fn($f) => [
        'id'       => $f->id,
        'name'     => $f->name,
        'file_url' => asset('storage/' . $f->file),
    ]));
    Route::get('/api/colors', fn() => \App\Models\Color::select('id', 'name', 'code')->get());
    Route::get('/api/mascot-templates', function () {
        return \App\Models\Template::with('category')->latest()->get()->map(fn($t) => [
            'id'          => $t->id,
            'title'       => $t->title,
            'svg_data'    => $t->svg_data,
            'image_data'  => $t->image_data,
            'category'    => $t->category?->name ?? 'Uncategorized',
            'category_id' => $t->category_id,
        ]);
    });
    Route::get('/storage/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);
        return response()->file($fullPath);
    })->where('path', '.*');

// ═══════════════════════════════════════════════════════════
// MAIN DOMAIN — prosix.com
// Frontend React app + public API routes
// ═══════════════════════════════════════════════════════════
} else {

    // API routes
    Route::get('/api/fonts', fn() => \App\Models\Font::select('id', 'name', 'file')->get());
    Route::get('/api/colors', fn() => \App\Models\Color::select('id', 'name', 'code')->get());
    Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'search']);
    Route::get('/api/menu-categories/{slug}', [CategoryController::class, 'apiMenuCategories']);
    Route::get('/category/{id}', [CategoryController::class, 'products'])->name('category.products');
    Route::get('/user/categories-with-models', [CustomizerModelController::class, 'userCategoriesWithModels']);
    Route::get('/user/model-api/{id}', [CustomizerModelController::class, 'userApi']);
    Route::get('/api/mascot-templates', function () {
        return \App\Models\Template::with('category')->latest()->get()->map(fn($t) => [
            'id'          => $t->id,
            'title'       => $t->title,
            'svg_data'    => $t->svg_data,
            'image_data'  => $t->image_data,
            'category'    => $t->category?->name ?? 'Uncategorized',
            'category_id' => $t->category_id,
        ]);
    });
    Route::get('/api/categories-for-templates', fn() =>
        \App\Models\Category::whereNull('parent_id')->where('status', 1)->select('id', 'name')->orderBy('name')->get()
    );
    Route::get('/storage/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);
        return response()->file($fullPath);
    })->where('path', '.*');

    Route::get('/customize/{id}', function ($id, Illuminate\Http\Request $request) {
        $model  = \App\Models\CustomizerModel::findOrFail($id);
        $colors = \App\Models\Color::all();
        $fonts  = \App\Models\Font::all()->map(fn($font) => [
            'id'       => $font->id,
            'name'     => $font->name,
            'file_url' => asset('storage/' . $font->file),
        ]);
        $design = $request->has('design_id')
            ? \App\Models\UserCustomization::find($request->design_id)
            : null;
        return view('admin.models.show', compact('model', 'colors', 'fonts', 'design'))
            ->with('isUserMode', true);
    });

    // React Frontend — sab kuch
    Route::get('/{any}', fn() => view('welcome'))->where('any', '.*');

}
