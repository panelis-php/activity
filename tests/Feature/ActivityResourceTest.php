<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Panelis\Activity\Panel\Resources\ActivityResource;
use Panelis\Activity\Panel\Resources\ActivityResource\Enums\ActivityPermission;
use Panelis\Activity\Tests\Models\User;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('uses the configured activity model', function (): void {
    config()->set('activitylog.activity_model', Activity::class);

    expect(ActivityResource::getModel())->toBe(Activity::class);
});

it('allows authorized users to access activity logs', function (): void {
    $user = User::query()->create([
        'name' => 'Activity Admin',
        'email' => 'activity@example.com',
    ]);

    Gate::define(ActivityPermission::Browse->value, fn (): bool => true);

    $this->actingAs($user);

    expect(ActivityResource::canAccess())->toBeTrue()
        ->and(ActivityResource::shouldRegisterNavigation())->toBeTrue();
});

it('eager loads the activity causer and subject', function (): void {
    $query = ActivityResource::getEloquentQuery();

    expect($query->getEagerLoads())
        ->toHaveKeys(['causer', 'subject']);
});

it('retrieves logged activities with their causer and subject', function (): void {
    $user = User::query()->create([
        'name' => 'Activity Actor',
        'email' => 'actor@example.com',
    ]);

    $activity = activity()
        ->causedBy($user)
        ->performedOn($user)
        ->event('updated')
        ->withProperties(['attributes' => ['name' => 'Activity Actor']])
        ->log('Updated profile');

    $record = ActivityResource::getEloquentQuery()->findOrFail($activity->getKey());

    expect($record->description)->toBe('Updated profile')
        ->and($record->relationLoaded('causer'))->toBeTrue()
        ->and($record->relationLoaded('subject'))->toBeTrue()
        ->and($record->causer->is($user))->toBeTrue()
        ->and($record->subject->is($user))->toBeTrue();
});
