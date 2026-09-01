<?php

namespace Panelis\Activity\Providers;

use Illuminate\Support\ServiceProvider;

class ActivityServiceProvider extends ServiceProvider
{
    private const string NAMESPACE = 'activity';

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../../lang', self::NAMESPACE);

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    public function register(): void {}
}
