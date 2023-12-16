<?php

namespace App\Providers;

use App\Services\TokenService;
use Illuminate\Support\ServiceProvider;

class TokenFacadeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('TokenFacade',function (){
            return new TokenService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
