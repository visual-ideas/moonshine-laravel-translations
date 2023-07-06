# Laravel translations manager for [MoonShine admin panel](https://moonshine.cutcode.dev)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/visual-ideas/moonshine-laravel-translations.svg?style=flat-square)](https://packagist.org/packages/visual-ideas/laravel-site-settings)
[![Total Downloads](https://img.shields.io/packagist/dt/visual-ideas/moonshine-laravel-translations.svg?style=flat-square)](https://packagist.org/packages/visual-ideas/laravel-site-settings)

## Installation

You can install the package via composer:

```bash
composer require visual-ideas/moonshine-laravel-translations
```

You must run the migrations with:

```bash
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="VI\MoonShineLaravelTranslations\Providers\MoonShineLaravelTranslationsServiceProvider" --tag="config"
```


This is the contents of the published config file:

```php
TODO Config info
```

Add new MoonShine resource to your MoonShineServiceProvider file, like this:

```php
MenuItem::make('Переводы', new \VI\MoonShineLaravelTranslations\Resources\MoonShineLaravelTranslationResource())
    ->translatable()
    ->badge(fn()=>cache()->remember('moonshine_laravel_translations_changed_count',
        now()->addMinute(),
        fn()=>\VI\MoonShineLaravelTranslations\Models\MoonshineLaravelTranslation::getCountChanged()))
    ->icon('heroicons.outline.language'),
```

## Credits

- [Alex](https://github.com/alexvenga)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
