<?php

namespace App\Providers;

use App\Http\Contracts\OptionServiceInterface;
use App\Http\Contracts\OrderServiceInterface;
use App\Http\Contracts\ProductServiceInterface;
use App\Http\Contracts\VariantServiceInterface;
use App\Http\Services\Material\MaterialService;
use App\Http\Services\Option\OptionService;
use App\Http\Services\Order\OrderService;
use App\Http\Services\Payment\YooKassaService;
use App\Http\Services\Product\ProductService;
use App\Http\Services\User\UserService;
use App\Http\Services\Variant\VariantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        $this->app->bind(MaterialService::class, fn () => new MaterialService);
        $this->app->bind(YooKassaService::class, fn () => new YooKassaService);
        $this->app->bind(UserService::class, fn () => new UserService);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(env('FORCE_HTTPS',false)) {
//            URL::forceScheme('https');
        }
//        Inertia::share('user', Auth('admin')->user());
    }
}
