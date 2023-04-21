<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
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

Route::get('/test', [\App\Http\Controllers\TestController::class, 'index'])->middleware(['guest']);

Route::post('/recaptcha', [\App\Http\Controllers\TestController::class, 'initToken'])->middleware(['guest']);
Route::get('/recaptcha', [\App\Http\Controllers\TestController::class, 'getRecaptchaToken'])->middleware(['guest']);

Route::get('/put', [\App\Http\Controllers\TestController::class, 'putSomeDataToSession'])->middleware(['guest']);
Route::get('/get', [\App\Http\Controllers\TestController::class, 'getDataFromSession'])->middleware(['guest']);


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

    Route::get('/products/paginated-data', [\App\Http\Controllers\Product\ProductController::class, 'paginatedData']);

    Route::get('/products', [\App\Http\Controllers\Product\ProductController::class, 'index'])->name('product.index');
    Route::get('/products/by-term', [\App\Http\Controllers\Product\ProductController::class, 'byTerm']);
    Route::post('/products', [\App\Http\Controllers\Product\ProductController::class, 'store']);
    Route::get('/products/{product}', [\App\Http\Controllers\Product\ProductController::class, 'show'])->name('product.show');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\Product\ProductController::class, 'edit']);
    Route::patch('/products/{product}/update', [\App\Http\Controllers\Product\ProductController::class, 'update']);
    Route::delete('/products/{product}', [\App\Http\Controllers\Product\ProductController::class, 'destroy']);

    Route::post('/products/{product}/sizes', [\App\Http\Controllers\Product\ProductController::class, 'appendSize']);
    Route::delete('/products/sizes/{size}', [\App\Http\Controllers\Product\ProductController::class, 'deleteSize']);

    Route::get('/products/{product}/toggle-publish', [\App\Http\Controllers\Product\ProductController::class, 'togglePublish']);

    Route::post('/products/{product}/images', [\App\Http\Controllers\Image\ProductImageController::class, 'store']);
    Route::post('/products/{product}/images/order', [\App\Http\Controllers\Image\ProductImageController::class, 'order']);
    Route::delete('/products/{product}/images/{productVariantImage}', [\App\Http\Controllers\Image\ProductImageController::class, 'destroy']);

    Route::post('/products/{product}/variants', [\App\Http\Controllers\Variant\VariantController::class, 'store']);
    Route::patch('/products/{product}/variants/{variant}/', [\App\Http\Controllers\Variant\VariantController::class, 'updateField']);
    Route::delete('/products/{product}/variants', [\App\Http\Controllers\Variant\VariantController::class, 'destroy']);

    Route::post('/products/{product}/variants/{variant}/photos', [\App\Http\Controllers\Image\VariantImageController::class, 'storeImage']);
    Route::post('/products/{product}/variants/{variant}/photos/bind', [\App\Http\Controllers\Image\VariantImageController::class, 'bind']);

    Route::post('/products/{product}/variants/{variant}/options/bind', [\App\Http\Controllers\Option\OptionController::class, 'bind']);
    Route::post('/products/{product}/variants/{variant}/options/bind-with-new-value', [\App\Http\Controllers\Option\OptionController::class, 'bindWithNewValue']);
    Route::post('/products/{product}/options', [\App\Http\Controllers\Option\OptionController::class, 'store']);
    Route::delete('/products/{product}/options/{optionName}', [\App\Http\Controllers\Option\OptionController::class, 'destroy']);
    Route::get('/products/{product}/options/{optionName}/toggle-is-color', [\App\Http\Controllers\Option\OptionController::class, 'toggleIsColor']);

    Route::patch('/products/{product}/categories', [\App\Http\Controllers\Category\CategoryController::class, 'appendCategory']);
    Route::delete('/products/{product}/categories', [\App\Http\Controllers\Category\CategoryController::class, 'clearCategory']);

    Route::post('/products/{product}/accent-properties', [\App\Http\Controllers\AccentProperty\AccentPropertyController::class, 'bind']);

    Route::get('/products/{product}/delivery-settings/yandex', [\App\Http\Controllers\Product\DeliverySettings\YandexController::class, 'index']);
    Route::get('/products/{product}/delivery-settings/business-lines', [\App\Http\Controllers\Product\DeliverySettings\BusinessLinesController::class, 'index']);

    Route::get('/materials/variants-content', [\App\Http\Controllers\Variant\VariantContentController::class, 'index']);
    Route::get('/materials/variants-content/create', [\App\Http\Controllers\Variant\VariantContentController::class, 'create']);
    Route::post('/materials/variants-content', [\App\Http\Controllers\Variant\VariantContentController::class, 'store']);
    Route::get('/materials/variants-content/{variantContent}/edit', [\App\Http\Controllers\Variant\VariantContentController::class, 'edit']);

    Route::post('/materials/variants-content/{variantContent}/append-item', [\App\Http\Controllers\Variant\VariantContentController::class, 'appendItem']);
    Route::post('/materials/variants-content-items/{variantContentItem}/append-image', [\App\Http\Controllers\Variant\VariantContentController::class, 'appendImage']);
    Route::delete('/materials/variants-content-items/{variantContentItem}', [\App\Http\Controllers\Variant\VariantContentController::class, 'deleteItem']);

    Route::post('/materials/variants-content/{variantContent}/append-text-item', [\App\Http\Controllers\Variant\VariantContentController::class, 'appendTextItem']);
    Route::delete('/materials/variants-content-text-items/{variantContentTextItem}', [\App\Http\Controllers\Variant\VariantContentController::class, 'deleteTextItem']);

    Route::delete('/materials/variants-content/{contentItem}', [\App\Http\Controllers\Variant\VariantContentController::class, 'destroy']);

    Route::get('/products/{product}/models/edit', [\App\Http\Controllers\Product\ProductModelController::class, 'edit']);
    Route::post('/products/{product}/models', [\App\Http\Controllers\Product\ProductModelController::class, 'store']);
    Route::post('/products/{product}/models/{model}/images', [\App\Http\Controllers\Product\ProductModelController::class, 'addImage']);
    Route::delete('/products/{product}/models/{model}/images/{image}', [\App\Http\Controllers\Product\ProductModelController::class, 'deleteImage']);
    Route::delete('/products/{product}/models/{productModel}', [\App\Http\Controllers\Product\ProductModelController::class, 'destroy']);

    Route::patch('/variants/{variant}/materials/{material}', [\App\Http\Controllers\Variant\VariantController::class, 'bindMaterials']);

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
    Route::get('/orders/settings', [\App\Http\Controllers\Order\OrderSettingsController::class, 'index']);
    Route::get('/orders/{order}', [\App\Http\Controllers\Order\OrderController::class, 'show']);
    Route::post('/orders/{order}/status', [\App\Http\Controllers\Order\OrderController::class, 'changeStatus']);
    Route::post('/orders/{order}/toggle-pay', [\App\Http\Controllers\Order\OrderController::class, 'togglePay']);
    Route::post('/orders/{order}/payment', [\App\Http\Controllers\Order\OrderController::class, 'setPaymentVariant']);
    Route::post('/orders/{order}/duty', [\App\Http\Controllers\Order\OrderController::class, 'setManager']);
    Route::post('/orders/{order}/admin-comment', [\App\Http\Controllers\Order\OrderController::class, 'setAdminComment']);

    Route::post('/orders/fields', [\App\Http\Controllers\Order\OrderFieldController::class, 'store']);
    Route::delete('/orders/fields/{field}', [\App\Http\Controllers\Order\OrderFieldController::class, 'destroy']);

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


    Route::get('/user-settings', [\App\Http\Controllers\User\UserSettingsController::class, 'index']);
    Route::post('/user-settings/fields', [\App\Http\Controllers\User\UserFieldController::class, 'store']);
    Route::delete('/user-settings/fields/{field}', [\App\Http\Controllers\User\UserFieldController::class, 'destroy']);

    Route::get('/users/{user}/transactions', [\App\Http\Controllers\User\TransactionsController::class, 'index']);

    Route::get('/users', [\App\Http\Controllers\User\UserController::class, 'index']);
    Route::get('/users/create', [\App\Http\Controllers\User\UserController::class, 'create']);
    Route::patch('/users/{user}/update', [\App\Http\Controllers\User\UserController::class, 'update']);
    Route::post('/users/organizations', [\App\Http\Controllers\User\UserController::class, 'storeOrganisation']);
    Route::post('/users/single', [\App\Http\Controllers\User\UserController::class, 'storeSingleUser']);
    Route::get('/users/by-term', [\App\Http\Controllers\User\UserController::class, 'byTerm']);
    Route::get('/users/{user}', [\App\Http\Controllers\User\UserController::class, 'show']);
    Route::patch('/users/{user}/kind', [\App\Http\Controllers\User\UserController::class, 'changeKind']);
    Route::delete('/users/{user}', [\App\Http\Controllers\User\UserController::class, 'destroy']);

//    Route::get('/sales', [\App\Http\Controllers\Sale\SaleController::class, 'index']);
//    Route::post('/sales', [\App\Http\Controllers\Sale\SaleController::class, 'store']);
//    Route::get('/sales/{sale}', [\App\Http\Controllers\Sale\SaleController::class, 'show']);
//    Route::get('/sales/{sale}/sale-products', [\App\Http\Controllers\Sale\SaleController::class, 'saleProducts']);
//    Route::post('/sales/{sale}/public', [\App\Http\Controllers\Sale\SaleController::class, 'publish']);
//    Route::post('/sales/{sale}/image', [\App\Http\Controllers\Sale\SaleController::class, 'setImage']);
//    Route::delete('/sales/{sale}/image', [\App\Http\Controllers\Sale\SaleController::class, 'deleteImage']);
//    Route::get('/sales/{sale}/toggle-exists-product/{product}', [\App\Http\Controllers\Sale\SaleController::class, 'toggleProductExists']);
//    Route::delete('/sales/{sale}', [\App\Http\Controllers\Sale\SaleController::class, 'destroy']);

    Route::get('/groups', [\App\Http\Controllers\Group\GroupController::class, 'index']);
    Route::get('/groups/{group}', [\App\Http\Controllers\Group\GroupController::class, 'show']);
    Route::post('/groups', [\App\Http\Controllers\Group\GroupController::class, 'create']);
    Route::patch('/groups/{group}', [\App\Http\Controllers\Group\GroupController::class, 'update']);

    Route::get('/roles', [\App\Http\Controllers\Role\RoleController::class, 'index']);
    Route::post('/roles', [\App\Http\Controllers\Role\RoleController::class, 'store']);
    Route::delete('/roles/{role}', [\App\Http\Controllers\Role\RoleController::class, 'destroy']);

    Route::post('/banner-items', [\App\Http\Controllers\Banner\BannerController::class, 'store']);
    Route::post('/banner-items/{item}/link', [\App\Http\Controllers\Banner\BannerController::class, 'attachLink']);
    Route::delete('/banner-items/{item}/link', [\App\Http\Controllers\Banner\BannerController::class, 'removeLink']);
    Route::post('/banner-items/order', [\App\Http\Controllers\Banner\BannerController::class, 'order']);
    Route::delete('/banner-items/{item}', [\App\Http\Controllers\Banner\BannerController::class, 'destroy']);

    Route::get('/abilities', [\App\Http\Controllers\Ability\AbilityController::class, 'index']);
    Route::patch('/abilities/roles/{role}/permissions/{permission}', [\App\Http\Controllers\Ability\AbilityController::class, 'toggle']);

    Route::get('/kits', [\App\Http\Controllers\Kit\KitsController::class, 'index']);
    Route::post('/kits', [\App\Http\Controllers\Kit\KitsController::class, 'store']);
    Route::get('/kits/{kit}/edit', [\App\Http\Controllers\Kit\KitsController::class, 'edit']);
    Route::get('/kits/{kit}/products', [\App\Http\Controllers\Kit\KitsController::class, 'products']);
    Route::get('/kits/{kit}/products/bind-variants/{variant}', [\App\Http\Controllers\Kit\KitsController::class, 'bindVariant']);
    Route::get('/kits/{kit}/products/{product}/toggle', [\App\Http\Controllers\Kit\KitsController::class, 'toggle']);

    Route::get('/statistics', [\App\Http\Controllers\Statistic\StatisticController::class, 'index']);
    Route::post('/statistics/calculate-orders-count-period', [\App\Http\Controllers\Statistic\StatisticController::class, 'calculateOrdersCountPeriod']);
    Route::post('/statistics/calculate-orders-profit-period', [\App\Http\Controllers\Statistic\StatisticController::class, 'calculateOrdersProfitPeriod']);

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

    Route::post('/accent-properties', [\App\Http\Controllers\AccentProperty\AccentPropertyController::class, 'store']);
    Route::delete('/accent-properties/{accentProperty}', [\App\Http\Controllers\AccentProperty\AccentPropertyController::class, 'destroy']);

    Route::get('/interiors', [\App\Http\Controllers\Interior\InteriorController::class, 'index']);
    Route::post('/interiors/{interior}', [\App\Http\Controllers\Interior\InteriorController::class, 'store']);
    Route::patch('/interiors/{interior}/update', [\App\Http\Controllers\Interior\InteriorController::class, 'update']);
//    Route::post('/interiors/{interior}/variant/{variant}', [\App\Http\Controllers\Interior\InteriorController::class, 'appendVariant']);
    Route::delete('/interiors/{interior}', [\App\Http\Controllers\Interior\InteriorController::class, 'destroy']);
//    Route::post('/interiors/{interior}/image', [\App\Http\Controllers\Interior\InteriorController::class, 'storeImage']);
//    Route::delete('/interiors/{interior}/image', [\App\Http\Controllers\Interior\InteriorController::class, 'deleteImage']);


    Route::get('/variants/search', [\App\Http\Controllers\Variant\VariantController::class, 'search']);

    Route::get('/materials', [\App\Http\Controllers\Material\MaterialController::class, 'index']);
    Route::post('/materials', [\App\Http\Controllers\Material\MaterialController::class, 'storeMaterial']);
    Route::post('/materials/{material}/units', [\App\Http\Controllers\Material\MaterialController::class, 'storeUnit']);
    Route::get('/materials/units/{unit}', [\App\Http\Controllers\Material\MaterialController::class, 'unit']);
    Route::delete('/materials/units/{unit}', [\App\Http\Controllers\Material\MaterialController::class, 'destroyUnit']);
    Route::get('/materials/categories/{category}', [\App\Http\Controllers\Material\MaterialController::class, 'edit']);
    Route::patch('/materials/{category}', [\App\Http\Controllers\Material\MaterialController::class, 'bind']);
    Route::get('/materials/{material}', [\App\Http\Controllers\Material\MaterialController::class, 'material']);
    Route::post('/materials/units/{unit}/values', [\App\Http\Controllers\Material\MaterialController::class, 'storeValue']);
    Route::delete('/materials/values/{value}', [\App\Http\Controllers\Material\MaterialController::class, 'destroyValue']);
    Route::delete('/materials/{material}', [\App\Http\Controllers\Material\MaterialController::class, 'destroy']);

    Route::get('/materials/{material}/colors', [\App\Http\Controllers\Material\MaterialColorController::class, 'index']);
    Route::get('/materials/{material}/search', [\App\Http\Controllers\Material\MaterialController::class, 'search']);
    Route::post('/materials/{material}/colors/add', [\App\Http\Controllers\Material\MaterialColorController::class, 'addColor']);
    Route::patch('/materials/{material}/toggle-color', [\App\Http\Controllers\Material\MaterialColorController::class, 'toggleColor']);


    Route::get('/main-page-sales', [\App\Http\Controllers\Sale\MainPageSalesController::class, 'index']);
    Route::post('/main-page-sales/{sale}/image', [\App\Http\Controllers\Sale\MainPageSalesController::class, 'loadImage']);
    Route::post('/main-page-sales/update', [\App\Http\Controllers\Sale\MainPageSalesController::class, 'update']);

    Route::get('/delivery', [\App\Http\Controllers\Delivery\DeliveryController::class, 'index']);
    Route::get('/delivery/yandex', [\App\Http\Controllers\Delivery\YandexController::class, 'index']);
    Route::patch('/delivery/yandex', [\App\Http\Controllers\Delivery\YandexController::class, 'update']);
    Route::get('/delivery/business-lines', [\App\Http\Controllers\Delivery\BusinessLinesController::class, 'index']);
    Route::patch('/delivery/business-lines', [\App\Http\Controllers\Delivery\BusinessLinesController::class, 'update']);

    Route::get('/transactions', [\App\Http\Controllers\Transaction\TransactionsController::class, 'index']);
});

Route::post('/payment', [\App\Http\Controllers\Payment\PaymentController::class, 'store']);
Route::match(['GET', 'POST'], '/payment/callback', [\App\Http\Controllers\Payment\PaymentController::class, 'callback']);
Route::match(['GET', 'POST'], '/payment/tinkoff/installment/callback', function () {
    $source = file_get_contents('php://input');
    $requestBody = json_decode($source, true);
    Log::info($requestBody);
});

require __DIR__.'/auth.php';
