# Panelis Activity

Panelis Activity adds a read-only Filament resource for [Spatie Laravel Activitylog](https://spatie.be/docs/laravel-activitylog). It displays application activity records with their event, log name, actor, subject, properties, and timestamp.

## Requirements

- PHP 8.4 or later
- Laravel 13
- Filament 5

## Installation

```bash
composer require panelis-php/activity
php artisan migrate
php artisan panelis:sync-permissions
```

The package provides the `activity_log` migration. Grant the generated `BrowseActivity` permission to a role before users can access the Activity log resource.

## Logging activity

Use Spatie's API directly from the module that owns the activity:

```php
activity('auth')
    ->causedBy($user)
    ->performedOn($user)
    ->event('login')
    ->log('Logged in');
```

Activity is polymorphic and does not depend on `panelis-php/user`; any Eloquent model can be an actor or subject.

## CMS integration

Panelis CMS logs successful Laravel `Login` events automatically. The record uses the `auth` log name and `login` event.
