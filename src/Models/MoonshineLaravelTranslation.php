<?php

namespace VI\MoonShineLaravelTranslations\Models;

use Illuminate\Database\Eloquent\Model;

class MoonshineLaravelTranslation extends Model
{

    public static ?array $localesList = null;
    public static ?array $groupsList = null;

    protected $fillable = [
        'group',
        'list_order',
        'key',
        'locale',
        'value',
        'is_changed',
    ];

    protected $casts = [
        'is_changed' => 'boolean',
    ];

    public static function getCountChanged(): int
    {
        return static::where('is_changed', true)->count();
    }

    public static function getLocalesList(): array
    {

        if (is_null(static::$localesList)) {
            static::$localesList = static::groupBy('locale')->orderBy('locale')->pluck('locale')->toArray();
        }

        return static::$localesList;
    }

    public static function getGroupsList(): array
    {

        if (is_null(static::$groupsList)) {
            static::$groupsList = static::groupBy('group')->orderBy('group')->pluck('group')->toArray();
        }

        return static::$groupsList;
    }
}
