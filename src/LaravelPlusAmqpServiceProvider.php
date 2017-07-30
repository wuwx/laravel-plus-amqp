<?php

namespace Wuwx\LaravelPlusAmqp;

use Illuminate\Support\ServiceProvider;

class LaravelPlusAmqpServiceProvider extends ServiceProvider
{

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton('amqp', function ($app) {
            return new AmqpManager();
        });
    }

}
