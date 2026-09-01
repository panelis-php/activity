<?php

declare(strict_types=1);

namespace Panelis\Activity\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Panelis\Activity\Panel\Resources\ActivityResource;

class ActivityPlugin implements Plugin
{
    public function getId(): string
    {
        return 'activity';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ActivityResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
