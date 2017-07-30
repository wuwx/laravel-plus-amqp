<?php

namespace Wuwx\LaravelPlusAmqp;

use Illuminate\Support\Facades\Facade;

class AmqpFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'amqp';
    }
}
