<?php

namespace Wuwx\LaravelPlusAmqp\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Wuwx\LaravelPlusAmqp\LaravelPlusAmqpServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelPlusAmqpServiceProvider::class,
        ];
    }
}