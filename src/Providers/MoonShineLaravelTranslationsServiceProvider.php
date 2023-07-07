<?php

declare(strict_types=1);

namespace VI\MoonShineLaravelTranslations\Providers;

use Illuminate\Support\ServiceProvider;

final class MoonShineLaravelTranslationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'moonshine-laravel-translations');

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/moonshine-laravel-translations.php',
            'moonshine-laravel-translations'
        );

        $this->publishes([
            __DIR__ . '/../../lang' => $this->app->langPath('vendor/moonshine-laravel-translations'),
        ], 'translations');

        $this->publishes([
            __DIR__ . '/../../config' => $this->app->configPath(),
        ], 'config');

        $this->commands([]);
    }
}
