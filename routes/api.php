<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Pattern;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\CustomizerModelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MembershipRequestController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\ArtworkRequestController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\FlipbookController;
use App\Http\Controllers\UserRequestController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\PlaceOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// -----------------------------------------------
// HEALTH CHECK
// -----------------------------------------------
Route::get('/health', function () {
    return response()->json([
        'status'    => 'ok',
        'timestamp' => now()->toISOString()
    ]);
});

// -----------------------------------------------
// PATTERNS (Public)
// -----------------------------------------------
Route::get('/patterns', function () {
    return Pattern::all()->map(function ($p) {
        return [
            'id'      => $p->id,
            'name'    => $p->name,
            'svg_url' => asset('storage/' . $p->svg_path),
        ];
    });
});

// -----------------------------------------------
// USER AUTH (Public)
// -----------------------------------------------
Route::post('/user/register',   [UserController::class, 'register']);
Route::post('/user/login',      [UserController::class, 'login']);
Route::post('/user/verify-otp', [UserController::class, 'verifyOtp']);
Route::post('/user/resend-otp', [UserController::class, 'resendOtp']);
// -----------------------------------------------
// EMAIL VERIFICATION
// -----------------------------------------------
Route::middleware('auth:sanctum')->get('/email/verify', function () {
    return response()->json(['message' => 'Please verify your email']);
})->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json([
        'status'  => true,
        'message' => 'Email verified successfully'
    ]);
})->middleware('signed')->name('verification.verify');

// -----------------------------------------------
// BANNERS, CATEGORIES, NAVIGATIONS (Public)
// -----------------------------------------------
Route::get('/banners',                    [BannerController::class,    'apiIndex']);
Route::get('/categories',                 [CategoryController::class,  'apiIndex']);
Route::get('/navigations',                [NavigationController::class,'apiIndex']);
Route::get('/categories-by-navigation',   [CategoryController::class,  'apiCategoriesByNavigation']);
Route::get('/highlighted',                [CategoryController::class,  'apiHighlighted']);
Route::get('/menu-categories/{slug}',     [CategoryController::class,  'apiMenuCategories']);
Route::get('/category-products/{slug}',   [ProductController::class,   'categoryProducts']);
Route::get('/category/{id}/products',     [ProductController::class,   'apiCategoryProducts']);
Route::get('/category/{id}',              [CategoryController::class,  'products']);

Route::prefix('categories')->group(function () {
    Route::get('/{id}/subcategories', [CategoryController::class, 'subcategories']);
});

Route::post('/categories/{id}/verify-password', [CategoryController::class, 'verifyCategoryPassword']);

// -----------------------------------------------
// PRODUCTS (Public)
// -----------------------------------------------
Route::get('/featured-products', [ProductController::class, 'apiFeaturedProducts']);
Route::get('/apparel-products',  [ProductController::class, 'apiApparelProducts']);
Route::get('/products',          [ProductController::class, 'indexApi']);
Route::get('/products/{id}',     [ProductController::class, 'showApi']);

// -----------------------------------------------
// DEALS, VIDEOS, BLOGS (Public)
// -----------------------------------------------
Route::get('/latest-deal', [DealController::class, 'apiLatestDeal']);
Route::get('/videos',      [VideoController::class, 'apiIndex']);
Route::get('/blogs',       [BlogController::class,  'apiIndex']);
Route::get('/blogs/{slug}',[BlogController::class,  'apiShow']);

// -----------------------------------------------
// TESTIMONIALS (Public)
// -----------------------------------------------
Route::get('/testimonials', [TestimonialController::class, 'apiIndex']);

// -----------------------------------------------
// FLIPBOOKS (Public)
// -----------------------------------------------
Route::get('/flipbooks',      [FlipbookController::class, 'apiIndex']);
Route::get('/flipbooks/{id}', [FlipbookController::class, 'apiShow']);

// -----------------------------------------------
// COLORS (Public)
// -----------------------------------------------
Route::get('/colors', [ColorController::class, 'apiIndex']);

// -----------------------------------------------
// CUSTOMIZER MODELS (Public)
// -----------------------------------------------
Route::get('/subcategories/{id}/models', [CustomizerModelController::class, 'modelsBySubcategory']);
Route::get('/categories/{id}/models',    [CustomizerModelController::class, 'modelsByCategory']);
Route::get('/models/{id}',               [CustomizerModelController::class, 'show']);

// -----------------------------------------------
// TEMPLATES (Public)
// -----------------------------------------------
Route::post('/mascot-templates', [TemplateController::class, 'saveFromCustomizer']);

// -----------------------------------------------
// ORDERS (Public store + Auth index/show)
// -----------------------------------------------
Route::post('/orders', [OrderController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders',      [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});


// -----------------------------------------------
// PLACE ORDER
// -----------------------------------------------
Route::post('/place-order', [PlaceOrderController::class, 'store'])
     ->middleware('auth:sanctum')->withoutMiddleware('auth:sanctum');
// -----------------------------------------------
// MEMBERSHIP & ARTWORK (Public)
// -----------------------------------------------
Route::post('/membership-request', [MembershipRequestController::class, 'store']);
Route::post('/artwork-request',    [ArtworkRequestController::class,    'store']);

// -----------------------------------------------
// STRIPE (Public)
// -----------------------------------------------
Route::post('/create-payment-intent', [StripeController::class, 'createPaymentIntent']);

// -----------------------------------------------
// USER DASHBOARD (Protected - Auth Required)
// -----------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/logout',          [UserController::class,        'logout']);
    Route::get('/user/profile',          [UserController::class,        'profile']);
    Route::put('/profile',               [UserController::class,        'updateProfile']);
    Route::post('/user/change-password', [UserController::class,        'changePassword']);
    Route::get('/user/my-requests',      [UserRequestController::class, 'index']);

    // ✅ Place Orders — logged in user ke orders
    Route::get('/place-order/my-orders', [PlaceOrderController::class, 'myOrders']);
});

// -----------------------------------------------
// FALLBACK
// -----------------------------------------------
Route::fallback(function () {
    return response()->json(['error' => 'API endpoint not found'], 404);
});
