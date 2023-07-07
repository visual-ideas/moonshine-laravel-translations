<?php

declare(strict_types=1);

namespace VI\MoonShineLaravelTranslations\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use MoonShine\Actions\Action;
use MoonShine\MoonShineUI;
use MoonShine\Traits\WithConfirmation;
use VI\MoonShineLaravelTranslations\Models\MoonshineLaravelTranslation;

class ImportTranslationsAction extends Action
{
    use WithConfirmation;

    protected ?string $icon = 'heroicons.outline.arrow-down-tray';


    public function handle(): RedirectResponse
    {

        $langDisk = Storage::build([
            'driver' => 'local',
            'root' => lang_path(),
        ]);

        MoonshineLaravelTranslation::truncate();

        collect($langDisk->allFiles())->each(function (string $fileName) use ($langDisk) {

            $fileName = str($fileName);

            if ($fileName->startsWith('vendor')) {
                return;
            }

            if ($fileName->endsWith('.json')) {
                $locale = $fileName->replaceLast('.json', '')->toString();
                $groupName = 'json';
                $arrayTranslations = Arr::dot(json_decode($langDisk->get($fileName->toString()), true));
                $i = 0;
                foreach ($arrayTranslations as $key => $value) {
                    $this->updateOrCreateTranslation([
                        'group' => $groupName,
                        'key' => $key,
                        'locale' => $locale,
                        'value' => $value,
                    ]);
                    $i++;
                }

                return;
            }

            if ($fileName->endsWith('.php')) {
                $locale = $fileName->substr(0, 2)->toString();
                $groupName = $fileName->replaceFirst($locale.'/', '')->replaceLast('.php', '');
                $arrayTranslations = include lang_path($fileName->toString());
                $arrayTranslations = Arr::dot($arrayTranslations);
                foreach ($arrayTranslations as $key => $value) {
                    $this->updateOrCreateTranslation([
                        'group' => $groupName,
                        'key' => $key,
                        'locale' => $locale,
                        'value' => $value,
                    ]);
                }

                return;
            }

            //dd($fileName);
        });

        MoonShineUI::toast(
            'Импортировано',
            'success'
        );

        return back();

    }

    protected function updateOrCreateTranslation(array $data)
    {

        if (! empty(config('moonshine-laravel-translations.locales')) && ! in_array(
            $data['locale'],
            config('moonshine-laravel-translations.locales')
        )) {
            return;
        }

        if (! empty(config('moonshine-laravel-translations.ignored')) && in_array(
            $data['group'],
            config('moonshine-laravel-translations.ignored')
        )) {
            return;
        }

        $moonshineLaravelTranslation = MoonshineLaravelTranslation::updateOrCreate([
            'group' => $data['group'],
            'key' => $data['key'],
        ], [
            'is_changed' => false,
        ]);


        $moonshineLaravelTranslation->setTranslation('value', $data['locale'], $data['value'])->save();
    }

}
