<?php

namespace App\Providers;

use App\Http\Contracts\OptionServiceInterface;
use App\Http\Contracts\OrderServiceInterface;
use App\Http\Contracts\ProductServiceInterface;
use App\Http\Contracts\VariantServiceInterface;
use App\Http\Services\Option\OptionService;
use App\Http\Services\Order\OrderService;
use App\Http\Services\Product\ProductService;
use App\Http\Services\Variant\VariantService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(VariantServiceInterface::class, VariantService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(OptionServiceInterface::class, OptionService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
