<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/delivery/cdek'], function () {
    Route::get('regions', [\App\Http\Controllers\Api\Delivery\CDEK\CdekController::class, 'getRegions']);
    Route::get('cities', [\App\Http\Controllers\Api\Delivery\CDEK\CdekController::class, 'getLocalities']);
    Route::post('calculate-by-available-tariffs', [\App\Http\Controllers\Api\Delivery\CDEK\CdekController::class, 'calculateByAvailableTariffs']);
});

Route::group(['prefix' => '/delivery/business-lines'], function () {
    Route::get('cities', [\App\Http\Controllers\Api\Delivery\BusinessLines\BusinessLinesController::class, 'cities']);
    Route::get('cities-by-term', [\App\Http\Controllers\Api\Delivery\BusinessLines\BusinessLinesController::class, 'getCitiesByTerm']); //q
    Route::get('city-street', [\App\Http\Controllers\Api\Delivery\BusinessLines\BusinessLinesController::class, 'street']);
    Route::post('calculate', [\App\Http\Controllers\Api\Delivery\BusinessLines\BusinessLinesController::class, 'calculate']);
    Route::post('terminals', [\App\Http\Controllers\Api\Delivery\BusinessLines\BusinessLinesController::class, 'getTerminals']);
//    Route::get('cities', [\App\Http\Controllers\Api\Delivery\CDEK\CdekController::class, 'getLocalities']);
//    Route::post('calculate-by-available-tariffs', [\App\Http\Controllers\Api\Delivery\CDEK\CdekController::class, 'calculateByAvailableTariffs']);
});

Route::group(['prefix' => '/delivery/yandex'], function () {
    Route::get('search-by-term', [\App\Http\Controllers\Api\Delivery\Yandex\YandexDeliveryController::class, 'getCoordinatesByTerm']);
    Route::post('calculate', [\App\Http\Controllers\Api\Delivery\Yandex\YandexDeliveryController::class, 'calculate']);
    Route::get('pvz', [\App\Http\Controllers\Api\Delivery\Yandex\YandexDeliveryController::class, 'pvzList']);
//    Route::get('cities-by-term', [\App\Http\Controllers\Api\Delivery\BusinessLines\BusinessLinesController::class, 'getCitiesByTerm']);
//    Route::post('calculate', [\App\Http\Controllers\Api\Delivery\BusinessLines\BusinessLinesController::class, 'calculate']);
//    Route::get('cities', [\App\Http\Controllers\Api\Delivery\CDEK\CdekController::class, 'getLocalities']);
//    Route::post('calculate-by-available-tariffs', [\App\Http\Controllers\Api\Delivery\CDEK\CdekController::class, 'calculateByAvailableTariffs']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/cart', [\App\Http\Controllers\Api\Cart\CartController::class, 'getCartVariants']);
    Route::patch('/cart/toggle-variant/{variant}', [\App\Http\Controllers\Api\Cart\CartController::class, 'toggle']);
    Route::delete('/cart', [\App\Http\Controllers\Api\Cart\CartController::class, 'clear']);

    Route::get('/favorites', [\App\Http\Controllers\Api\Favorite\FavoriteController::class, 'getFavoriteVariants']);
    Route::patch('/favorites/toggle-variant/{variant}', [\App\Http\Controllers\Api\Favorite\FavoriteController::class, 'toggle']);
    Route::delete('/favorites', [\App\Http\Controllers\Api\Favorite\FavoriteController::class, 'clear']);
});


Route::get('/auth/logout', [\App\Http\Controllers\Api\Auth\AuthController::class,'logout']);
Route::post('/auth/login', [\App\Http\Controllers\Api\Auth\AuthController::class,'login']);

Route::post('/auth/register-organization', [\App\Http\Controllers\Api\Auth\AuthController::class,'registerOrganization']);
Route::post('/auth/confirm-register-organization', [\App\Http\Controllers\Api\Auth\AuthController::class,'confirmRegisterOrganization']);

Route::post('/auth/login-sms', [\App\Http\Controllers\Api\Auth\AuthController::class,'loginViaSms']);
Route::post('/auth/confirm-login-sms', [\App\Http\Controllers\Api\Auth\AuthController::class,'confirmLoginViaSms']);

Route::post('/auth/register-sms', [\App\Http\Controllers\Api\Auth\AuthController::class,'registerSingleUserViaSms']);
Route::post('/auth/confirm-register-sms', [\App\Http\Controllers\Api\Auth\AuthController::class,'confirmRegisterSingleUserViaSms']);

Route::post('/auth/reset-link', [\App\Http\Controllers\Api\Auth\AuthController::class,'sendForgotPasswordLink']);
Route::post('/auth/reset-password', [\App\Http\Controllers\Api\Auth\AuthController::class,'reset']);

Route::get('/login/{provider}', [\App\Http\Controllers\Api\Auth\SocialiteController::class,'redirectToProvider']);
Route::get('/login/{provider}/callback', [\App\Http\Controllers\Api\Auth\SocialiteController::class,'handleProviderCallback']);

Route::get('/catalog', [\App\Http\Controllers\Api\Category\CategoryController::class, 'catalog']);
Route::get('/catalog/parent-category/{category}', [\App\Http\Controllers\Api\Category\CategoryController::class, 'parentCategoryVariants']);
Route::get('/catalog/child-category/{category}', [\App\Http\Controllers\Api\Category\CategoryController::class, 'childCategoryVariants']);
Route::post('/variants/price', [\App\Http\Controllers\Api\Variant\VariantController::class, 'getPrice']);


Route::get('/variants/ids', [\App\Http\Controllers\Api\Variant\VariantController::class, 'variantIds']);
Route::get('/variants/{variant}', [\App\Http\Controllers\Api\Variant\VariantController::class, 'variant']);
Route::get('/variants', [\App\Http\Controllers\Api\Variant\VariantController::class, 'variants']);


Route::post('/orders', [\App\Http\Controllers\Api\Order\OrderController::class, 'store']);

Route::get('/variants/{variant}/reviews', [\App\Http\Controllers\Api\Review\ReviewController::class, 'reviews']);
Route::post('/variants/{variant}/reviews', [\App\Http\Controllers\Api\Review\ReviewController::class, 'storeReview']);
Route::post('/variants/{variant}/reviews/images', [\App\Http\Controllers\Api\Review\ReviewController::class, 'storeReviewImages']);

Route::get('/main-banner', [\App\Http\Controllers\Api\Banner\BannerController::class, 'mainPageData']);

Route::get('/hits', [\App\Http\Controllers\Api\Hit\HitsController::class, 'data']);

Route::get('/vk/posts', [App\Http\Controllers\Vk\PostsController::class, 'posts']);

Route::get('/recaptcha', [App\Http\Controllers\Api\RecaptchaController::class, 'getSessionData']);
Route::post('/recaptcha', [App\Http\Controllers\Api\RecaptchaController::class, 'storeToken']);

// Main Page
Route::get('/main-kits', [App\Http\Controllers\Api\Kit\KitsController::class, 'mainPageData']);
Route::get('/main-interiors', [\App\Http\Controllers\Api\Interior\InteriorController::class, 'mainPageData']);

Route::get('/test-variant/{variant}', [\App\Http\Controllers\TestController::class, 'testVariant']);
