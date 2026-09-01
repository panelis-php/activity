<?php

namespace Panelis\Activity\Panel\Resources\ActivityResource\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum ActivityPermission: string implements HasLabel
{
    case Browse = 'BrowseActivity';

    public function getLabel(): string
    {
        return __(sprintf('activity::permission.name_%s', Str::snake($this->value)));
    }
}
