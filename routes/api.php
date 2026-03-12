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
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\FlipbookController;
use App\Http\Controllers\UserRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and assigned
| to the "api" middleware group. Perfect for SPA + mobile apps.
|
*/

// Public Routes
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString()
    ]);
});

// Patterns (public)
Route::get('/patterns', function () {
    return Pattern::all()->map(function ($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'svg_url' => asset('storage/' . $p->svg_path),
        ];
    });
});

// User authentication (public)
// Public


// Public user routes
Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/verify-otp', [UserController::class, 'verifyOtp']);



// Protected user routes (auth required)
Route::middleware('auth:sanctum')->prefix('user')->group(function () {

    // Default authenticated user info
    // Route::get('/', function (Request $request) {
    //     return $request->user();
    // });
});





///eamil send /////

Route::middleware('auth:sanctum')->get('/email/verify', function () {
    return response()->json([
        'message' => 'Please verify your email'
    ]);
})->name('verification.notice');

// ✅ Email verification route (link clicked in email)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // marks email as verified

    return response()->json([
        'status' => true,
        'message' => 'Email verified successfully'
    ]);
})->middleware('signed')->name('verification.verify');




// Public API to fetch banners
Route::get('/banners', [BannerController::class, 'apiIndex']);// API route in routes/api.php
Route::get('/categories', [CategoryController::class, 'apiIndex']);

// routes/api.php

Route::get('/navigations', [NavigationController::class, 'apiIndex']);
Route::get('/categories-by-navigation', [CategoryController::class, 'apiCategoriesByNavigation']);
Route::get('/api/categories-by-slug/{slug}', [CategoryController::class, 'categoriesBySlug']);
// routes/api.php
Route::get('/category-products/{slug}', [ProductController::class, 'categoryProducts']);
Route::get('/highlighted', [CategoryController::class, 'apiHighlighted']);
Route::get('/latest-deal', [DealController::class, 'apiLatestDeal'])->withoutMiddleware('auth:admin');

Route::get('/videos', [VideoController::class, 'apiIndex']);
Route::get('/blogs', [BlogController::class, 'apiIndex']);         // All blogs
Route::get('/blogs/{slug}', [BlogController::class, 'apiShow']);   // Single blog
// Featured Products API


Route::get('/category/{id}/products',  [ProductController::class, 'apiCategoryProducts']);


Route::get('/featured-products', [ProductController::class, 'apiFeaturedProducts']);
Route::get('/apparel-products', [ProductController::class, 'apiApparelProducts']);
Route::get('/products', [ProductController::class, 'indexApi']);
Route::get('/products/{id}', [ProductController::class, 'showApi']);
// -----------------------------------------------
// FRONTEND ROUTES
// -----------------------------------------------

Route::get('/flipbooks', [FlipbookController::class, 'apiIndex']);
Route::get('/flipbooks/{id}', [FlipbookController::class, 'apiShow']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/logout',           [UserController::class, 'logout']);
    Route::get('/user/profile',           [UserController::class, 'profile']);
    Route::put('/profile',                [UserController::class, 'updateProfile']);   // ✅ already tha
    Route::post('/user/change-password',  [UserController::class, 'changePassword']); // ✅ naya add karo
        Route::get('/user/my-requests', [UserRequestController::class, 'index']);

});


Route::get('/testimonials', [TestimonialController::class, 'apiIndex']);
Route::get('/menu-categories/{slug}', [CategoryController::class, 'apiMenuCategories']);

Route::prefix('categories')->group(function () {
    Route::get('/{id}/subcategories', [CategoryController::class, 'subcategories']);
});
Route::get('/category/{id}', [CategoryController::class, 'products']);




Route::get('/subcategories/{id}/models', [CustomizerModelController::class, 'modelsBySubcategory']);
Route::get('/categories/{id}/models', [CustomizerModelController::class, 'modelsByCategory']);
Route::post('/categories/{id}/verify-password',
    [CategoryController::class, 'verifyCategoryPassword']
);

Route::post('/orders', [OrderController::class, 'store']);

// Admin کے لیے (اگر auth ہے تو middleware لگاؤ)
Route::middleware('auth:sanctum')->group(function () {
// routes/api.php
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    });
    Route::post('/orders', [OrderController::class, 'store']);

    // routes/api.php






Route::get('/mascot-templates', [TemplateController::class, 'apiList']);

Route::get('/colors', [\App\Http\Controllers\ColorController::class, 'apiIndex']);

Route::post('/membership-request', [MembershipRequestController::class, 'store']);

Route::post('/artwork-request', [ArtworkRequestController::class, 'store'])
    ->middleware('auth:sanctum')->withoutMiddleware('auth:sanctum');
Route::post('/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
Route::fallback(function () {
    return response()->json(['error' => 'API endpoint not found'], 404);
});
Route::get('/models/{id}', [CustomizerModelController::class, 'show']);
Route::post('/api/templates/save', [TemplateController::class, 'saveFromCustomizer'])->name('templates.save.api');
