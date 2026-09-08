<?php

use Spatie\Activitylog\Support\ActivityLogger;

it('returns an enabled activity logger by default', function (): void {
    config()->set('activitylog.enabled', true);

    expect(audit())->toBeInstanceOf(ActivityLogger::class)
        ->and(activity_logging_enabled())->toBeTrue();
});

it('returns a disabled activity logger when activity logging is disabled', function (): void {
    config()->set('activitylog.enabled', false);

    expect(audit()->log('Should not be stored'))->toBeNull()
        ->and(activity_logging_enabled())->toBeFalse();
});
