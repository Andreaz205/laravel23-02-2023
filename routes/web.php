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
    Route::patch('/managers/{admin}/update', [\App\Http\Controllers\Manager\ManagerController::class, 'update']);
    Route::delete('/managers/{admin}', [\App\Http\Controllers\Manager\ManagerController::class, 'destroy']);


    Route::get('/products', [\App\Http\Controllers\Product\ProductController::class, 'index'])->name('product.index');
    Route::post('/products', [\App\Http\Controllers\Product\ProductController::class, 'store']);
    Route::get('/products/{product}', [\App\Http\Controllers\Product\ProductController::class, 'show'])->name('product.show');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\Product\ProductController::class, 'edit']);
    Route::patch('/products/{product}/update', [\App\Http\Controllers\Product\ProductController::class, 'update']);
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
    Route::get('/reviews/{review}', [\App\Http\Controllers\Review\ReviewController::class, 'review']);
    Route::post('/reviews/{review}/publish', [\App\Http\Controllers\Review\ReviewController::class, 'publish']);
    Route::post('/reviews/{review}/save', [\App\Http\Controllers\Review\ReviewController::class, 'save']);
    Route::patch('/reviews/{review}/unpublic', [\App\Http\Controllers\Review\ReviewController::class, 'unpublic']);
    Route::delete('/reviews/{review}/delete', [\App\Http\Controllers\Review\ReviewController::class, 'delete']);
    Route::delete('/reviews/{review}/answer', [\App\Http\Controllers\Review\ReviewController::class, 'deleteReviewAnswer']);

    Route::get('/users', [\App\Http\Controllers\User\UserController::class, 'index']);
    Route::get('/users/create', [\App\Http\Controllers\User\UserController::class, 'create']);
    Route::patch('/users/{user}/update', [\App\Http\Controllers\User\UserController::class, 'update']);
    Route::post('/users/organizations', [\App\Http\Controllers\User\UserController::class, 'storeOrganisation']);
    Route::post('/users/single', [\App\Http\Controllers\User\UserController::class, 'storeSingleUser']);
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

    Route::get('/groups', [\App\Http\Controllers\Group\GroupController::class, 'index']);
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

    Route::get('/kits', [\App\Http\Controllers\Kit\KitsController::class, 'index']);
    Route::post('/kits', [\App\Http\Controllers\Kit\KitsController::class, 'store']);
    Route::get('/kits/{kit}/products', [\App\Http\Controllers\Kit\KitsController::class, 'products']);
    Route::get('/kits/{kit}/products/{product}/toggle', [\App\Http\Controllers\Kit\KitsController::class, 'toggle']);

    Route::get('/statistics', [\App\Http\Controllers\Statistic\StatisticController::class, 'index']);

    Route::get('/discounts', [\App\Http\Controllers\Discount\DiscountController::class, 'index']);
    Route::patch('/discounts/toggle-availability', [\App\Http\Controllers\Discount\DiscountController::class, 'toggle']);
    Route::post('/discounts/accumulative', [\App\Http\Controllers\Discount\DiscountController::class, 'storeAccumulativeDiscount']);
    Route::post('/discounts/order', [\App\Http\Controllers\Discount\DiscountController::class, 'storeOrderDiscount']);
    Route::post('/discounts/coupon', [\App\Http\Controllers\Discount\DiscountController::class, 'storeCouponDiscount']);
    Route::delete('/discounts/{discount}', [\App\Http\Controllers\Discount\DiscountController::class, 'destroy']);

    Route::patch('/bonuses', [\App\Http\Controllers\Bonus\BonusController::class, 'update']);

    Route::get('/prices', [\App\Http\Controllers\Price\PriceController::class, 'index']);
    Route::post('/prices', [\App\Http\Controllers\Price\PriceController::class, 'store']);
    Route::patch('/prices/{pivot}', [\App\Http\Controllers\Price\PriceController::class, 'update']);
    Route::delete('/prices/{price}', [\App\Http\Controllers\Price\PriceController::class, 'destroy']);

    Route::get('/options', [\App\Http\Controllers\Option\OptionController::class, 'index']);
    Route::get('/options/{name}', [\App\Http\Controllers\Option\OptionController::class, 'show']);
    Route::post('/options/option-values/{value}/image', [\App\Http\Controllers\Option\OptionController::class, 'uploadImage']);
    Route::post('/options/option-values/{value}/image/update', [\App\Http\Controllers\Option\OptionController::class, 'updateImage']);
    Route::delete('/options/option-values/{value}/image', [\App\Http\Controllers\Option\OptionController::class, 'destroyImage']);
});

require __DIR__.'/auth.php';
