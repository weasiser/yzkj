<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app('Dingo\Api\Transformer\Factory')->setAdapter(function ($app) {
            $fractal = new \League\Fractal\Manager;
            $fractal->setSerializer(new \League\Fractal\Serializer\ArraySerializer);
            return new \Dingo\Api\Transformer\Adapter\Fractal($fractal);
        });
    }
}
