<?php

namespace App\Providers;

use App\Services\ClientsServices;
use App\Services\ItemsServices;
use App\Services\ListOfPurchaseServices;
use App\Services\MerchantsServices;
use App\Services\OperatorsServices;
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
        //Definir aqui as services
        $this->app->bind(OperatorsServices::class);
        $this->app->bind(MerchantsServices::class);
        $this->app->bind(ClientsServices::class);
        $this->app->bind(ItemsServices::class);
        $this->app->bind(ListOfPurchaseServices::class);
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
