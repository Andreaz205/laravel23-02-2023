<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Роуты для авторизации клиентов
Auth::routes();

Route::get('/', [\App\Http\Controllers\Main\MainController::class, 'index'])->middleware(['auth']);
Route::get('/dashboard', [\App\Http\Controllers\Main\MainController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
//Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [\App\Http\Controllers\Main\MainController::class, 'index']);


    Route::patch('/contacts', [\App\Http\Controllers\Contact\ContactController::class, 'storeContacts']);
    Route::delete('/contacts', [\App\Http\Controllers\Contact\ContactController::class, 'clearContacts']);


    Route::get('/managers', [\App\Http\Controllers\Manager\ManagerController::class, 'index']);
    Route::post('/managers/', [\App\Http\Controllers\Manager\ManagerController::class, 'store']);
    Route::get('/managers/{admin}/edit', [\App\Http\Controllers\Manager\ManagerController::class, 'edit']);
    Route::delete('/managers/{admin}', [\App\Http\Controllers\Manager\ManagerController::class, 'destroy']);


    Route::get('/products', [\App\Http\Controllers\Product\ProductController::class, 'index']);
    Route::post('/products', [\App\Http\Controllers\Product\ProductController::class, 'store']);
    Route::get('/products/{product}', [\App\Http\Controllers\Product\ProductController::class, 'show']);
    Route::delete('/products/{product}', [\App\Http\Controllers\Product\ProductController::class, 'destroy']);

    Route::post('/products/{product}/images', [\App\Http\Controllers\Image\ProductImageController::class, 'store']);
    Route::post('/products/{product}/images/order', [\App\Http\Controllers\Image\ProductImageController::class, 'order']);

    Route::delete('/products/{product}/images/{productVariantImage}', [\App\Http\Controllers\Image\ProductImageController::class, 'destroy']);

    Route::post('/products/{product}/variants', [\App\Http\Controllers\Variant\VariantController::class, 'store']);
    Route::patch('/products/{product}/variants/{variant}/', [\App\Http\Controllers\Variant\VariantController::class, 'updateField']);
    Route::post('/products/{product}/variants/{variant}/photos/bind', [\App\Http\Controllers\Image\VariantImageController::class, 'bind']);
    Route::delete('/products/{product}/variants', [\App\Http\Controllers\Variant\VariantController::class, 'destroy']);

    Route::post('/products/{product}/variants/{variant}/options/bind', [\App\Http\Controllers\Option\OptionController::class, 'bind']);
    Route::post('/products/{product}/variants/{variant}/options/bind-with-new-value', [\App\Http\Controllers\Option\OptionController::class, 'bindWithNewValue']);

    Route::post('/products/{product}/options', [\App\Http\Controllers\Option\OptionController::class, 'store']);
    Route::delete('/products/{product}/options/{optionName}', [\App\Http\Controllers\Option\OptionController::class, 'destroy']);
    Route::get('/products/{product}/options/{optionName}/toggle-is-color', [\App\Http\Controllers\Option\OptionController::class, 'toggleIsColor']);

    Route::get('/products/{product}/categories/{category}/toggle', [\App\Http\Controllers\Category\CategoryController::class, 'toggleProduct']);

    Route::get('/categories', [\App\Http\Controllers\Category\CategoryController::class, 'index']);
    Route::post('/categories', [\App\Http\Controllers\Category\CategoryController::class, 'store']);
//    Route::get('/categories/{category}/edit', [\App\Http\Controllers\Category\CategoryController::class, 'edit']);
    Route::post('/categories/{category}/publish', [\App\Http\Controllers\Category\CategoryController::class, 'publish']);
    Route::post('/categories/{category}/image', [\App\Http\Controllers\Category\CategoryController::class, 'storeImage']);
    Route::delete('/categories/{category}/image', [\App\Http\Controllers\Category\CategoryController::class, 'deleteImage']);
    Route::post('/categories/{category}/sketch', [\App\Http\Controllers\Category\CategoryController::class, 'storeSketch']);
    Route::delete('/categories/{category}/sketch', [\App\Http\Controllers\Category\CategoryController::class, 'deleteSketch']);
    Route::post('/categories/{category}/popular', [\App\Http\Controllers\Category\CategoryController::class, 'togglePopular']);


//    Route::post('/categories/{product}/bind', [\App\Http\Controllers\Category\CategoryController::class, 'bind']);
//    Route::post('/categories/{product}/bind-delete', [\App\Http\Controllers\Category\CategoryController::class, 'destroyBind']);
    Route::post('/categories/variant/{variant}/bind', [\App\Http\Controllers\Category\CategoryController::class, 'bindVariant']);
    Route::post('/categories/variant/{variant}/bind-delete', [\App\Http\Controllers\Category\CategoryController::class, 'destroyBindVariant']);
    Route::delete('/categories/{category}', [\App\Http\Controllers\Category\CategoryController::class, 'destroy']);


    Route::get('/orders', [\App\Http\Controllers\Order\OrderController::class, 'index']);
    Route::get('/orders/data', [\App\Http\Controllers\Order\OrderController::class, 'data']);
    Route::get('/orders/{order}', [\App\Http\Controllers\Order\OrderController::class, 'show']);
    Route::post('/orders/{order}/status', [\App\Http\Controllers\Order\OrderController::class, 'changeStatus']);
    Route::post('/orders/{order}/toggle-pay', [\App\Http\Controllers\Order\OrderController::class, 'togglePay']);
    Route::post('/orders/{order}/payment', [\App\Http\Controllers\Order\OrderController::class, 'setPaymentVariant']);
    Route::post('/orders/{order}/duty', [\App\Http\Controllers\Order\OrderController::class, 'setManager']);
    Route::post('/orders/{order}/admin-comment', [\App\Http\Controllers\Order\OrderController::class, 'setAdminComment']);

    Route::get('/rooms', [\App\Http\Controllers\Room\RoomController::class, 'index']);
    Route::post('/rooms/{room}/images', [\App\Http\Controllers\Room\RoomController::class, 'updateImage']);
    Route::delete('/rooms/{room}/images', [\App\Http\Controllers\Room\RoomController::class, 'deleteImage']);
    Route::delete('/rooms/{room}', [\App\Http\Controllers\Room\RoomController::class, 'deleteRoom']);
    Route::get('/rooms/{room}/categories/{category}/toggle', [\App\Http\Controllers\Room\RoomController::class, 'toggleBind']);
    Route::get('/rooms/{room}/filtered-categories', [\App\Http\Controllers\Room\RoomController::class, 'filteredCategories']);
    Route::post('/rooms', [\App\Http\Controllers\Room\RoomController::class, 'store']);

    Route::get('/reviews', [\App\Http\Controllers\Review\ReviewController::class, 'index']);
    Route::post('/reviews/{review}/save-public', [\App\Http\Controllers\Review\ReviewController::class, 'saveAndPublic']);
    Route::post('/reviews/{review}/save', [\App\Http\Controllers\Review\ReviewController::class, 'save']);
    Route::delete('/reviews/{review}/delete', [\App\Http\Controllers\Review\ReviewController::class, 'delete']);

    Route::get('/users', [\App\Http\Controllers\User\UserController::class, 'index']);
    Route::get('/users/create', [\App\Http\Controllers\User\UserController::class, 'create']);
    Route::get('/users/by-term', [\App\Http\Controllers\User\UserController::class, 'byTerm']);
    Route::get('/users/{user}', [\App\Http\Controllers\User\UserController::class, 'show']);
    Route::patch('/users/{user}/kind', [\App\Http\Controllers\User\UserController::class, 'changeKind']);

    Route::get('/sales', [\App\Http\Controllers\Sale\SaleController::class, 'index']);
    Route::post('/sales', [\App\Http\Controllers\Sale\SaleController::class, 'create']);
    Route::get('/sales/{sale}', [\App\Http\Controllers\Sale\SaleController::class, 'show']);
    Route::get('/sales/{sale}/sale-products', [\App\Http\Controllers\Sale\SaleController::class, 'saleProducts']);
    Route::post('/sales/{sale}/public', [\App\Http\Controllers\Sale\SaleController::class, 'publish']);
    Route::post('/sales/{sale}/image', [\App\Http\Controllers\Sale\SaleController::class, 'setImage']);
    Route::delete('/sales/{sale}/image', [\App\Http\Controllers\Sale\SaleController::class, 'deleteImage']);
    Route::get('/sales/{sale}/toggle-exists-product/{product}', [\App\Http\Controllers\Sale\SaleController::class, 'toggleProductExists']);

    Route::get('/groups/{group}', [\App\Http\Controllers\Group\GroupController::class, 'show']);
    Route::post('/groups', [\App\Http\Controllers\Group\GroupController::class, 'create']);
    Route::patch('/groups/{group}', [\App\Http\Controllers\Group\GroupController::class, 'update']);

    Route::get('/roles', [\App\Http\Controllers\Role\RoleController::class, 'index']);
    Route::post('/roles', [\App\Http\Controllers\Role\RoleController::class, 'store']);
    Route::delete('/roles/{role}', [\App\Http\Controllers\Role\RoleController::class, 'destroy']);

    Route::post('/banner-items', [\App\Http\Controllers\Banner\BannerController::class, 'store']);
    Route::post('/banner-items/order', [\App\Http\Controllers\Banner\BannerController::class, 'order']);
    Route::delete('/banner-items/{item}', [\App\Http\Controllers\Banner\BannerController::class, 'destroy']);

    Route::get('/abilities', [\App\Http\Controllers\Ability\AbilityController::class, 'index']);
    Route::patch('/abilities/roles/{role}/permissions/{permission}', [\App\Http\Controllers\Ability\AbilityController::class, 'toggle']);


});

require __DIR__.'/auth.php';
