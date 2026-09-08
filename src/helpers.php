<?php

declare(strict_types=1);

use Panelis\Setting\Models\Setting;
use Spatie\Activitylog\Support\ActivityLogger;

if (! function_exists('activity_logging_enabled')) {
    function activity_logging_enabled(): bool
    {
        if (class_exists(Setting::class)) {
            return (bool) Setting::get('activity.enabled', false);
        }

        return (bool) config('activitylog.enabled', true);
    }
}

if (! function_exists('audit')) {
    function audit(BackedEnum|string|null $logName = null): ActivityLogger
    {
        $logger = activity($logName);

        return activity_logging_enabled()
            ? $logger->enableLogging()
            : $logger->disableLogging();
    }
}
