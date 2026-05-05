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

$host         = request()->getHost();
$isAdmin      = str_contains($host, 'admin.');
$isCustomizer = str_contains($host, 'customizer.');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

if ($isAdmin) {

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('admin.password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.password.send');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('admin.password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.reset');

    Route::middleware(['auth:admin'])->group(function () {

        Route::get('/', fn() => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/users', [AuthController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{id}/login', [AuthController::class, 'loginAsUser'])->name('admin.users.loginAsUser');
        Route::patch('/users/{id}/toggle', [AuthController::class, 'toggleStatus'])->name('admin.users.toggle');
        Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp']);

        Route::resource('admins', \App\Http\Controllers\AdminManagerController::class)->names([
            'index' => 'admin.admins.index', 'create' => 'admin.admins.create',
            'store' => 'admin.admins.store', 'show' => 'admin.admins.show',
            'edit'  => 'admin.admins.edit',  'update' => 'admin.admins.update',
            'destroy' => 'admin.admins.destroy',
        ]);

        Route::get('/memberships', [MembershipRequestController::class, 'index'])->name('admin.memberships');
        Route::get('/membership-download', [MembershipRequestController::class, 'download'])->name('membership.download');
        Route::post('/membership/download-pdf', [MembershipRequestController::class, 'downloadPdf'])->name('membership.download.pdf');

        Route::middleware(['admin.permission:can_orders'])
            ->get('/orders', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'adminShow'])->name('admin.orders.show');
        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
        Route::post('/orders/{id}/shipping', [OrderController::class, 'updateShipping'])->name('admin.orders.updateShipping');
        Route::post('/orders/{id}/notes', [OrderController::class, 'updateNotes'])->name('admin.orders.updateNotes');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('admin.orders.cancel');

        Route::post('products/featured', [ProductController::class, 'featured'])->name('admin.products.featured');
        Route::post('products/apparel', [ProductController::class, 'apparel'])->name('admin.products.apparel');
        Route::post('products/bulk-category', [ProductController::class, 'bulkCategory'])->name('admin.products.bulkCategory');
        Route::get('/products/featured', [ProductController::class, 'featured'])->name('admin.products.featured.list');
        Route::middleware(['admin.permission:can_products'])
            ->resource('products', ProductController::class)->names([
                'index' => 'admin.products.index', 'create' => 'admin.products.create',
                'store' => 'admin.products.store', 'show' => 'admin.products.show',
                'edit'  => 'admin.products.edit',  'update' => 'admin.products.update',
                'destroy' => 'admin.products.destroy',
            ]);

        Route::middleware(['admin.permission:can_customizer'])
            ->resource('models', CustomizerModelController::class)->names([
                'index'   => 'customizer.models.index',
                'create'  => 'customizer.models.create',
                'store'   => 'customizer.models.store',
                'show'    => 'customizer.models.show',
                'edit'    => 'customizer.models.edit',
                'update'  => 'customizer.models.update',
                'destroy' => 'customizer.models.destroy',
            ]);
        Route::post('models/{id}/duplicate', [CustomizerModelController::class, 'duplicate'])->name('models.duplicate');
        Route::get('models/{model}/api', [CustomizerModelController::class, 'api'])->name('models.api.get');
        Route::post('models/{id}/save-design', [CustomizerModelController::class, 'saveDesign'])->name('customizer.models.save-design');
        Route::post('models/{id}/save-thumbnail', [CustomizerModelController::class, 'saveThumbnail'])->name('models.save-thumbnail');
        Route::post('/models/featured', [CustomizerModelController::class, 'bulkFeatured'])->name('models.featured');
        Route::post('/models/apparel', [CustomizerModelController::class, 'bulkApparel'])->name('models.apparel');
        Route::post('/models/bulk-destroy', [CustomizerModelController::class, 'bulkDestroy'])->name('models.bulkDestroy');
        Route::post('/models/bulk-duplicate', [CustomizerModelController::class, 'bulkDuplicate'])->name('models.bulkDuplicate');
        Route::post('models/{id}/toggle-hidden', [CustomizerModelController::class, 'toggleHidden'])->name('models.toggleHidden');
        Route::post('models/bulk-toggle-hidden', [CustomizerModelController::class, 'bulkToggleHidden'])->name('models.bulkToggleHidden');
        Route::post('/models/update-order', [CustomizerModelController::class, 'updateOrder'])->name('models.updateOrder');

        Route::resource('colors', ColorController::class)->names([
            'index' => 'admin.colors.index', 'create' => 'admin.colors.create',
            'store' => 'admin.colors.store', 'show' => 'admin.colors.show',
            'edit'  => 'admin.colors.edit',  'update' => 'admin.colors.update',
            'destroy' => 'admin.colors.destroy',
        ]);
        Route::resource('fonts', FontController::class)->names([
            'index' => 'admin.fonts.index', 'create' => 'admin.fonts.create',
            'store' => 'admin.fonts.store', 'show' => 'admin.fonts.show',
            'edit'  => 'admin.fonts.edit',  'update' => 'admin.fonts.update',
            'destroy' => 'admin.fonts.destroy',
        ]);
        Route::resource('patterns', PatternController::class)->names([
            'index' => 'admin.patterns.index', 'create' => 'admin.patterns.create',
            'store' => 'admin.patterns.store', 'show' => 'admin.patterns.show',
            'edit'  => 'admin.patterns.edit',  'update' => 'admin.patterns.update',
            'destroy' => 'admin.patterns.destroy',
        ]);
        Route::resource('banners', BannerController::class)->names([
            'index' => 'admin.banners.index', 'create' => 'admin.banners.create',
            'store' => 'admin.banners.store', 'show' => 'admin.banners.show',
            'edit'  => 'admin.banners.edit',  'update' => 'admin.banners.update',
            'destroy' => 'admin.banners.destroy',
        ]);
        Route::post('/banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
        Route::resource('socials', SocialMediaController::class)->names([
            'index' => 'admin.socials.index', 'create' => 'admin.socials.create',
            'store' => 'admin.socials.store', 'show' => 'admin.socials.show',
            'edit'  => 'admin.socials.edit',  'update' => 'admin.socials.update',
            'destroy' => 'admin.socials.destroy',
        ]);
        Route::middleware(['admin.permission:can_categories'])
            ->resource('categories', CategoryController::class)->except(['show'])->names([
                'index'   => 'admin.categories.index',
                'create'  => 'admin.categories.create',
                'store'   => 'admin.categories.store',
                'edit'    => 'admin.categories.edit',
                'update'  => 'admin.categories.update',
                'destroy' => 'admin.categories.destroy',
            ]);
        Route::get('categories/sub-create', [CategoryController::class, 'subCreate'])->name('categories.subcreate');
        Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::resource('navigations', NavigationController::class)->names([
            'index' => 'admin.navigations.index', 'create' => 'admin.navigations.create',
            'store' => 'admin.navigations.store', 'show' => 'admin.navigations.show',
            'edit'  => 'admin.navigations.edit',  'update' => 'admin.navigations.update',
            'destroy' => 'admin.navigations.destroy',
        ]);
        Route::patch('navigations/{navigation}/toggle-status', [NavigationController::class, 'toggleStatus'])->name('navigations.toggle-status');
        Route::resource('videos', VideoController::class)->names([
            'index' => 'admin.videos.index', 'create' => 'admin.videos.create',
            'store' => 'admin.videos.store', 'show' => 'admin.videos.show',
            'edit'  => 'admin.videos.edit',  'update' => 'admin.videos.update',
            'destroy' => 'admin.videos.destroy',
        ]);
        Route::resource('deals', DealController::class)->names([
            'index' => 'admin.deals.index', 'create' => 'admin.deals.create',
            'store' => 'admin.deals.store', 'show' => 'admin.deals.show',
            'edit'  => 'admin.deals.edit',  'update' => 'admin.deals.update',
            'destroy' => 'admin.deals.destroy',
        ]);
        Route::resource('blogs', BlogController::class)->names([
            'index' => 'admin.blogs.index', 'create' => 'admin.blogs.create',
            'store' => 'admin.blogs.store', 'show' => 'admin.blogs.show',
            'edit'  => 'admin.blogs.edit',  'update' => 'admin.blogs.update',
            'destroy' => 'admin.blogs.destroy',
        ]);
        Route::resource('testimonials', TestimonialController::class)->names([
            'index' => 'admin.testimonials.index', 'create' => 'admin.testimonials.create',
            'store' => 'admin.testimonials.store', 'show' => 'admin.testimonials.show',
            'edit'  => 'admin.testimonials.edit',  'update' => 'admin.testimonials.update',
            'destroy' => 'admin.testimonials.destroy',
        ]);
        Route::resource('flipbooks', FlipbookController::class)->names([
            'index' => 'admin.flipbooks.index', 'create' => 'admin.flipbooks.create',
            'store' => 'admin.flipbooks.store', 'show' => 'admin.flipbooks.show',
            'edit'  => 'admin.flipbooks.edit',  'update' => 'admin.flipbooks.update',
            'destroy' => 'admin.flipbooks.destroy',
        ]);
        Route::get('/artwork-requests', [ArtworkRequestController::class, 'index'])->name('admin.artwork');
        Route::post('/artwork/download-pdf', [ArtworkRequestController::class, 'downloadPdf'])->name('artwork.download.pdf');
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::get('/place-orders', [PlaceOrderController::class, 'index'])->name('admin.placeorder');
        Route::post('/place-orders-download', [PlaceOrderController::class, 'downloadPdf']);
        Route::get('/order/download/{id}', [PlaceOrderController::class, 'downloadSinglePdf'])->name('order.download.single');
        Route::post('templates/bulk-destroy', [TemplateController::class, 'bulkDestroy'])->name('templates.bulkDestroy');
        Route::resource('templates', TemplateController::class)->names([
            'index' => 'admin.templates.index', 'create' => 'admin.templates.create',
            'store' => 'admin.templates.store', 'show' => 'admin.templates.show',
            'edit'  => 'admin.templates.edit',  'update' => 'admin.templates.update',
            'destroy' => 'admin.templates.destroy',
        ]);
        Route::post('/templates/save-from-customizer', [TemplateController::class, 'saveFromCustomizer']);
        Route::get('/mascots/create', fn() => view('templates.create'))->name('mascots.create');
        Route::get('/mascots/{id}/edit', function ($id) {
            $template = \App\Models\Template::findOrFail($id);
            return view('templates.edit', compact('template'));
        })->name('mascots.edit');
        Route::get('/storage/{path}', function ($path) {
            $fullPath = storage_path('app/public/' . $path);
            if (!file_exists($fullPath)) abort(404);
            return response()->file($fullPath);
        })->where('path', '.*');
        Route::get('/api/fonts', fn() => \App\Models\Font::select('id', 'name', 'file')->get());
        Route::get('/api/colors', fn() => \App\Models\Color::select('id', 'name', 'code')->get());
        Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'search']);
        Route::get('/api/menu-categories/{slug}', [CategoryController::class, 'apiMenuCategories']);
        Route::get('/api/mascot-templates', function () {
            return \App\Models\Template::with('category')->latest()->get()->map(fn($t) => [
                'id' => $t->id, 'title' => $t->title, 'svg_data' => $t->svg_data,
                'image_data' => $t->image_data,
                'category' => $t->category?->name ?? 'Uncategorized',
                'category_id' => $t->category_id,
            ]);
        });
        Route::get('/api/categories-for-templates', fn() =>
            \App\Models\Category::whereNull('parent_id')->where('status', 1)->select('id', 'name')->orderBy('name')->get()
        );
        Route::get('/user/model-api/{id}', [CustomizerModelController::class, 'userApi']);

    });

// ═══════════════════════════════════════════
// CUSTOMIZER SUBDOMAIN — customizer.prosix.com
// ═══════════════════════════════════════════
} elseif ($isCustomizer) {

    Route::get('/', fn() => redirect('/models'));

    // Admin sidebar/header route fallbacks on customizer subdomain
    Route::get('/dashboard', fn() => redirect('/models'))->name('admin.dashboard');
    Route::post('/logout', fn() => redirect('https://admin.prosix.com/logout'))->name('admin.logout');

    // Admin sidebar links redirect to admin domain
    Route::get('/banners', fn() => redirect('https://admin.prosix.com/banners'))->name('admin.banners.index');
    Route::get('/flipbooks', fn() => redirect('https://admin.prosix.com/flipbooks'))->name('admin.flipbooks.index');
    Route::get('/blogs', fn() => redirect('https://admin.prosix.com/blogs'))->name('admin.blogs.index');
    Route::get('/testimonials', fn() => redirect('https://admin.prosix.com/testimonials'))->name('admin.testimonials.index');
    Route::get('/artwork-requests', fn() => redirect('https://admin.prosix.com/artwork-requests'))->name('admin.artwork');
    Route::get('/memberships', fn() => redirect('https://admin.prosix.com/memberships'))->name('admin.memberships');
    Route::get('/place-orders', fn() => redirect('https://admin.prosix.com/place-orders'))->name('admin.placeorder');
    Route::get('/products', fn() => redirect('https://admin.prosix.com/products'))->name('admin.products.index');
    Route::get('/deals', fn() => redirect('https://admin.prosix.com/deals'))->name('admin.deals.index');
    Route::get('/videos', fn() => redirect('https://admin.prosix.com/videos'))->name('admin.videos.index');
    Route::get('/categories', fn() => redirect('https://admin.prosix.com/categories'))->name('admin.categories.index');
    Route::get('/navigations', fn() => redirect('https://admin.prosix.com/navigations'))->name('admin.navigations.index');
    Route::get('/patterns', fn() => redirect('https://admin.prosix.com/patterns'))->name('admin.patterns.index');
    Route::get('/colors', fn() => redirect('https://admin.prosix.com/colors'))->name('admin.colors.index');
    Route::get('/templates', fn() => redirect('https://admin.prosix.com/templates'))->name('admin.templates.index');
    Route::get('/fonts', fn() => redirect('https://admin.prosix.com/fonts'))->name('admin.fonts.index');
    Route::get('/users', fn() => redirect('https://admin.prosix.com/users'))->name('admin.users.index');
    Route::get('/admins', fn() => redirect('https://admin.prosix.com/admins'))->name('admin.admins.index');
    Route::get('/orders', fn() => redirect('https://admin.prosix.com/orders'))->name('admin.orders.index');
    Route::get('/payments', fn() => redirect('https://admin.prosix.com/payments'))->name('admin.payments.index');

    // Models page
    Route::get('/models', function () {
        $models = \App\Models\CustomizerModel::with(['category', 'subcategory'])
            ->get();

        $categories = \App\Models\Category::whereNull('parent_id')
            ->with(['models', 'subcategories'])
            ->get();

        return view('admin.models.index', compact('models', 'categories'))
            ->with('isUserMode', false);
    })->name('customizer.models.index');

    // Model admin routes on customizer subdomain
    Route::get('/models/create', [CustomizerModelController::class, 'create'])->name('customizer.models.create');
    Route::post('/models', [CustomizerModelController::class, 'store'])->name('customizer.models.store');
    Route::get('/models/{model}', [CustomizerModelController::class, 'show'])->name('customizer.models.show');
    Route::get('/models/{model}/edit', [CustomizerModelController::class, 'edit'])->name('customizer.models.edit');
    Route::put('/models/{model}', [CustomizerModelController::class, 'update'])->name('customizer.models.update');
    Route::delete('/models/{model}', [CustomizerModelController::class, 'destroy'])->name('customizer.models.destroy');

    // Extra model actions
    Route::post('/models/{id}/duplicate', [CustomizerModelController::class, 'duplicate'])->name('models.duplicate');
    Route::get('/models/{model}/api', [CustomizerModelController::class, 'api'])->name('models.api.get');
    Route::get('/models/{model}/admin-api', [CustomizerModelController::class, 'api'])->name('admin.models.api.get');
    Route::post('/models/{id}/save-design', [CustomizerModelController::class, 'saveDesign'])->name('customizer.models.save-design');
    Route::post('/models/{id}/save-thumbnail', [CustomizerModelController::class, 'saveThumbnail'])->name('models.save-thumbnail');
    Route::post('/models/featured', [CustomizerModelController::class, 'bulkFeatured'])->name('models.featured');
    Route::post('/models/apparel', [CustomizerModelController::class, 'bulkApparel'])->name('models.apparel');
    Route::post('/models/bulk-destroy', [CustomizerModelController::class, 'bulkDestroy'])->name('models.bulkDestroy');
    Route::post('/models/bulk-duplicate', [CustomizerModelController::class, 'bulkDuplicate'])->name('models.bulkDuplicate');
    Route::post('/models/{id}/toggle-hidden', [CustomizerModelController::class, 'toggleHidden'])->name('models.toggleHidden');
    Route::post('/models/bulk-toggle-hidden', [CustomizerModelController::class, 'bulkToggleHidden'])->name('models.bulkToggleHidden');
    Route::post('/models/update-order', [CustomizerModelController::class, 'updateOrder'])->name('models.updateOrder');

    // Customize page
    Route::get('/customize/{id}', function ($id, Illuminate\Http\Request $request) {
        $model  = \App\Models\CustomizerModel::findOrFail($id);
        $colors = \App\Models\Color::all();
        $fonts  = \App\Models\Font::all()->map(fn($font) => [
            'id' => $font->id,
            'name' => $font->name,
            'file_url' => asset('storage/' . $font->file),
        ]);

        $design = $request->has('design_id')
            ? \App\Models\UserCustomization::find($request->design_id)
            : null;

        return view('customizer.index', compact('model', 'colors', 'fonts', 'design'))
            ->with('isUserMode', false);
    });

    // APIs
    Route::get('/user/model-api/{id}', [CustomizerModelController::class, 'userApi']);
    Route::get('/user/categories-with-models', [CustomizerModelController::class, 'userCategoriesWithModels']);
    Route::post('/templates/save-from-customizer', [TemplateController::class, 'saveFromCustomizer']);

    Route::get('/api/fonts', fn() => \App\Models\Font::all()->map(fn($f) => [
        'id' => $f->id,
        'name' => $f->name,
        'file_url' => asset('storage/' . $f->file),
    ]));

    Route::get('/api/colors', fn() => \App\Models\Color::select('id', 'name', 'code')->get());

    Route::get('/api/mascot-templates', function () {
        return \App\Models\Template::with('category')->latest()->get()->map(fn($t) => [
            'id' => $t->id,
            'title' => $t->title,
            'svg_data' => $t->svg_data,
            'image_data' => $t->image_data,
            'category' => $t->category?->name ?? 'Uncategorized',
            'category_id' => $t->category_id,
        ]);
    });

    Route::get('/storage/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);
        return response()->file($fullPath);
    })->where('path', '.*');
// ═══════════════════════════════════════════
// MAIN DOMAIN — prosix.com
// ═══════════════════════════════════════════
} else {

    Route::get('/api/fonts', fn() => \App\Models\Font::select('id', 'name', 'file')->get());
    Route::get('/api/colors', fn() => \App\Models\Color::select('id', 'name', 'code')->get());
    Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'search']);
    Route::get('/api/menu-categories/{slug}', [CategoryController::class, 'apiMenuCategories']);
    Route::get('/category/{id}', [CategoryController::class, 'products'])->name('category.products');
    Route::get('/user/categories-with-models', [CustomizerModelController::class, 'userCategoriesWithModels']);
    Route::get('/user/model-api/{id}', [CustomizerModelController::class, 'userApi']);
    Route::get('/api/mascot-templates', function () {
        return \App\Models\Template::with('category')->latest()->get()->map(fn($t) => [
            'id' => $t->id, 'title' => $t->title, 'svg_data' => $t->svg_data,
            'image_data' => $t->image_data,
            'category' => $t->category?->name ?? 'Uncategorized',
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
            'id' => $font->id, 'name' => $font->name,
            'file_url' => asset('storage/' . $font->file),
        ]);
        $design = $request->has('design_id')
            ? \App\Models\UserCustomization::find($request->design_id) : null;
        return view('admin.models.show', compact('model', 'colors', 'fonts', 'design'))
->with('isUserMode', false);
    });

    Route::get('/{any}', fn() => view('welcome'))->where('any', '.*');
}
