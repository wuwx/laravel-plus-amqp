<?php

namespace Wuwx\LaravelPlusAmqp;

use Illuminate\Support\ServiceProvider;

class LaravelPlusAmqpServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/amqp.php' => config_path('amqp.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/amqp.php', 'amqp'
        );

        $this->app->singleton('amqp', function ($app) {
            return new AmqpManager();
        });
    }

}
