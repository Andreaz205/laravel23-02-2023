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

Route::group(['middleware' => 'auth:sanctum'], function () {
   Route::get('/cart', [\App\Http\Controllers\Api\Cart\CartController::class, 'getCartVariants']);
   Route::patch('/cart/toggle-variant/{variant}', [\App\Http\Controllers\Api\Cart\CartController::class, 'toggle']);
   Route::delete('/cart', [\App\Http\Controllers\Api\Cart\CartController::class, 'clear']);

    Route::get('/favorites', [\App\Http\Controllers\Api\Favorite\FavoriteController::class, 'getFavoriteVariants']);
    Route::patch('/favorites/toggle-variant/{variant}', [\App\Http\Controllers\Api\Favorite\FavoriteController::class, 'toggle']);
    Route::delete('/favorites', [\App\Http\Controllers\Api\Favorite\FavoriteController::class, 'clear']);
});

Route::get('/login/{provider}', [\App\Http\Controllers\Api\Auth\SocialiteController::class,'redirectToProvider']);
Route::get('/login/{provider}/callback', [\App\Http\Controllers\Api\Auth\SocialiteController::class,'handleProviderCallback']);

Route::get('/variants/{variant}', [\App\Http\Controllers\Api\Variant\VariantController::class, 'variant']);
Route::get('/variants', [\App\Http\Controllers\Api\Variant\VariantController::class, 'variants']);



Route::post('/orders', [\App\Http\Controllers\Api\Order\OrderController::class, 'store']);

Route::post('/variants/{variant}/reviews', [\App\Http\Controllers\Api\Review\ReviewController::class, 'storeReview']);
Route::post('/variants/{variant}/reviews/images', [\App\Http\Controllers\Api\Review\ReviewController::class, 'storeReviewImages']);

Route::get('/banner/data', [\App\Http\Controllers\Api\Banner\BannerController::class, 'data']);

Route::get('/hits', [\App\Http\Controllers\Api\Hit\HitsController::class, 'data']);

Route::get('/vk/posts', [App\Http\Controllers\Vk\PostsController::class, 'posts']);
