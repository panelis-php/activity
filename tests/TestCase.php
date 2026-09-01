<?php

namespace Panelis\Activity\Tests;

use Filament\Facades\Filament;
use Filament\FilamentServiceProvider;
use Filament\Panel;
use Orchestra\Testbench\TestCase as Orchestra;
use Panelis\Activity\Providers\ActivityServiceProvider;
use Panelis\Activity\Tests\Models\User;
use Spatie\Activitylog\ActivitylogServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentServiceProvider::class,
            ActivitylogServiceProvider::class,
            ActivityServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('auth.defaults.guard', 'web');
        $app['config']->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);
        $app['config']->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Panel::make());
    }
}
