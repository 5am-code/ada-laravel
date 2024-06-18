<?php

namespace Ada\Tests;

use Ada\AdaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AdaServiceProvider::class,
        ];
    }
}
