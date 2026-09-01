<?php

namespace Panelis\Activity\Panel\Resources;

use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Panelis\Activity\Panel\Resources\ActivityResource\Enums\ActivityPermission;
use Panelis\Activity\Panel\Resources\ActivityResource\Pages\ListActivities;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 3;

    public static function getModel(): string
    {
        return config('activitylog.activity_model', Activity::class);
    }

    public static function getLabel(): ?string
    {
        return __('activity::activity.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('activity::activity.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('ui.system');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can(ActivityPermission::Browse->value) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['causer', 'subject']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('description')
                    ->label(__('activity::activity.description'))
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->searchable()
                    ->wrap(),

                TextColumn::make('event')
                    ->label(__('activity::activity.event'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === null
                        ? __('activity::activity.unknown')
                        : Str::headline($state))
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('log_name')
                    ->label(__('activity::activity.log_name'))
                    ->badge()
                    ->placeholder(__('activity::activity.default_log_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('causer')
                    ->label(__('activity::activity.causer'))
                    ->state(function (Model $record): string {
                        $causer = $record->causer;

                        return $causer?->name ?? $causer?->getKey() ?? __('activity::activity.system');
                    }),

                TextColumn::make('subject')
                    ->label(__('activity::activity.subject'))
                    ->state(function (Model $record): string {
                        if ($record->subject === null) {
                            return __('activity::activity.system');
                        }

                        return sprintf('%s #%s', class_basename($record->subject_type), $record->subject_id);
                    }),

                TextColumn::make('properties')
                    ->label(__('activity::activity.properties'))
                    ->state(fn (Model $record): string => json_encode(
                        $record->properties,
                        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    ) ?: '')
                    ->limit(40)
                    ->tooltip(fn (Model $record): string => json_encode(
                        $record->properties,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    ) ?: '')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('activity::activity.created_at'))
                    ->dateTime(config('app.datetime_format'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label(__('activity::activity.event'))
                    ->multiple()
                    ->options(fn (): array => self::getModel()::query()
                        ->whereNotNull('event')
                        ->distinct()
                        ->orderBy('event')
                        ->pluck('event', 'event')
                        ->map(fn (string $event): string => Str::headline($event))
                        ->all()),

                SelectFilter::make('log_name')
                    ->label(__('activity::activity.log_name'))
                    ->multiple()
                    ->options(fn (): array => self::getModel()::query()
                        ->whereNotNull('log_name')
                        ->distinct()
                        ->orderBy('log_name')
                        ->pluck('log_name', 'log_name')
                        ->all()),

            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
        ];
    }
}
