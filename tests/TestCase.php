<?php

namespace Mpstr24\InterfaceTyper\Tests;

use Mpstr24\InterfaceTyper\InterfaceTyperServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            InterfaceTyperServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app->setBasePath(__DIR__.'/fake');

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/fake/database/migrations');
    }
}
