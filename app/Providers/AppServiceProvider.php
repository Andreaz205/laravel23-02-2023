<?php

namespace App\Providers;

use App\Http\Contracts\ProductServiceInterface;
use App\Http\Contracts\VariantServiceInterface;
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
