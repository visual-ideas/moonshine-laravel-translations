<?php

declare(strict_types=1);

namespace VI\MoonShineLaravelTranslations\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use MoonShine\Fields\ID;
use MoonShine\Fields\NoInput;
use MoonShine\Fields\StackFields;
use MoonShine\Filters\SelectFilter;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\Resource;
use VI\MoonShineLaravelTranslations\Actions\ExportTranslationsAction;
use VI\MoonShineLaravelTranslations\Actions\ImportTranslationsAction;
use VI\MoonShineLaravelTranslations\Models\MoonshineLaravelTranslation;
use VI\MoonShineSpatieTranslatable\Fields\Translatable;

class MoonShineLaravelTranslationResource extends Resource
{
    public static string $model = MoonshineLaravelTranslation::class;

    public string $titleField = 'key';

    protected bool $createInModal = true;

    protected bool $editInModal = true;

    public static string $title = 'Переводы';

    public static array $activeActions = [/*'create', */
        'show', 'edit', /*'delete'*/
    ];

    public static string $orderField = 'group';

    public function trClass(Model $item, int $index): string
    {
        if ($item->is_changed) {
            return 'yellow';
        }

        return parent::trClass($item, $index);
    }

    public function fields(): array
    {
        return [
            ID::make()
                ->sortable()
                ->hideOnDetail(),

            NoInput::make('Изменено', 'is_changed')
                ->boolean(false, true)
                ->sortable()
                ->hideOnForm(),

            /*
            NoInput::make('Локаль', 'locale', fn (
                MoonshineLaravelTranslation $moonshineLaravelTranslation
            ) => Str::upper($moonshineLaravelTranslation->locale))
                ->badge('pink')
                ->sortable(),
            */

            StackFields::make('Группа/Ключ', 'group')
                ->fields([

                    NoInput::make('Группа', 'group')
                        ->badge('blue'),

                    NoInput::make(
                        'Ключ',
                        'key',
                        fn (
                            MoonshineLaravelTranslation $moonshineLaravelTranslation
                        ) => str($moonshineLaravelTranslation->key)
                            ->replaceMatches(
                                '/:([a-z\_]+)/ui',
                                '<b style="background-color: rgba(100,255,100, 0.3);">$0</b>'
                            )
                            ->toString()
                    ),

                ])
                ->sortable()
                ->hideOnForm()
                ->hideOnDetail(),


            NoInput::make('Группа', 'group')
                ->badge('blue')
                ->hideOnIndex(),

            NoInput::make(
                'Ключ',
                'key',
                fn (
                    MoonshineLaravelTranslation $moonshineLaravelTranslation
                ) => str($moonshineLaravelTranslation->key)
                    ->replaceMatches(
                        '/:([a-z\_]+)/ui',
                        '<b style="background-color: rgba(100,255,100, 0.3);">$0</b>'
                    )
                    ->toString()
            )
                ->hideOnIndex(),


            NoInput::make(
                'Значение',
                'value',
                function (MoonshineLaravelTranslation $moonshineLaravelTranslation) {
                    $html = '<table>';
                    foreach ($moonshineLaravelTranslation->getTranslations('value') as $code => $value) {
                        $html .= sprintf(
                            '<tr><td><b>%s</b></td><td width="100%%">%s</td><tr>',
                            str($code)->upper(),
                            str($value)->replaceMatches(
                                '/:([a-z\_]+)/ui',
                                '<b style="background-color: rgba(100,255,100, 0.3);">$0</b>'
                            )
                        );
                    }

                    return $html.'</table>';
                }
            )
                ->sortable(),


            Translatable::make(
                'Значение',
                'value'
            )->languages(config('moonshine-laravel-translations.locales'))
                ->hideOnIndex()
                ->hideOnDetail(),
        ];
    }

    /**
     * @return array{name: string[]}
     */
    public function rules($item): array
    {
        return [
            //'value' => ['nullable', 'string', ''],
        ];
    }

    public function search(): array
    {
        return ['id', 'group', 'key', 'value'];
    }

    public function filters(): array
    {
        return [

            /*
            SelectFilter::make('Локали', 'locale')
                ->nullable()
                ->options(array_combine(
                    MoonshineLaravelTranslation::getLocalesList(),
                    MoonshineLaravelTranslation::getLocalesList()
                )),
            */

            SelectFilter::make('Группы', 'group')
                ->nullable()
                ->options(array_combine(
                    MoonshineLaravelTranslation::getGroupsList(),
                    MoonshineLaravelTranslation::getGroupsList()
                )),

        ];
    }

    public function actions(): array
    {
        return [

            ImportTranslationsAction::make('Импортировать переводы')
                ->showInLine(),

            ExportTranslationsAction::make('Экспортировать переводы')->showInLine(),

        ];
    }

    public function queryTags(): array
    {

        $tags = [];

        foreach (MoonshineLaravelTranslation::getGroupsList() as $groupList) {
            $tags[] = QueryTag::make(
                $groupList, // Tag Title
                fn (Builder $query) => $query->where('group', $groupList) // Query builder
            );
        }

        return $tags;
    }

    public function afterUpdated(MoonshineLaravelTranslation $moonshineLaravelTranslation): void
    {

        if ($moonshineLaravelTranslation->wasChanged('value')) {
            $moonshineLaravelTranslation->update(['is_changed' => true]);
        }

    }
}
