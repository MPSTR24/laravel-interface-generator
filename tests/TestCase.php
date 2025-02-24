<?php

namespace Mpstr24\InterfaceTyper\Tests;

use Mpstr24\InterfaceTyper\InterfaceTyperServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            InterfaceTyperServiceProvider::class
        ];
    }
}
