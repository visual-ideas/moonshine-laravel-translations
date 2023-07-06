<?php

declare(strict_types=1);

namespace VI\MoonShineLaravelTranslations\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use MoonShine\Actions\Action;
use MoonShine\MoonShineUI;
use VI\MoonShineLaravelTranslations\Models\MoonshineLaravelTranslation;

class ExportTranslationsAction extends Action
{

    protected ?string $icon = 'heroicons.outline.arrow-up-tray';


    public function handle(): RedirectResponse
    {

        $langDisk = Storage::build([
            'driver' => 'local',
            'root' => lang_path(),
        ]);

        $translations = MoonshineLaravelTranslation::toBase()->get();

        foreach ($translations->groupBy('locale') as $locale => $localeData) {
            foreach ($localeData->groupBy('group') as $group => $groupData) {

                $groupData = $groupData->sortBy('list_order')->pluck('value', 'key')->toArray();

                $groupData = Arr::undot($groupData);

                if ($group == 'json') {

                    $langDisk->put($locale.'.json',
                        json_encode($groupData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

                    continue;
                }

                $langDisk->put($locale.'/'.$group.'.php', "<?php\n\ndeclare(strict_types=1);\n\nreturn ".$this->prettyVarExport($groupData).";\n");

            }
        }

        MoonshineLaravelTranslation::query()->update(['is_changed'=>false]);

        MoonShineUI::toast(
            'Экспортировано',
            'success'
        );

        return back();

    }

    protected function prettyVarExport($expression): string
    {
        $export = var_export($expression, true);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/([\s]+)\n([\s]+)\[/ui" => ' [',
            "/\n([\s ]{8})(['|\]])/ui" => "\n                $2",
            "/\n([\s ]{6})(['|\]])/ui" => "\n            $2",
            "/\n([\s ]{4})(['|\]])/ui" => "\n        $2",
            "/\n([\s ]{2})(['|\]])/ui" => "\n    $2",
        ];
        $output = preg_replace(array_keys($patterns), array_values($patterns), $export);



        return trim($output);
    }

}
