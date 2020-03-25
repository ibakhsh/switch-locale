<?php

namespace ibakhsh\SwitchLocale;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class Language extends Field
{
    public $component = "switch-locale";
    public $locale;

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $prefix = optional(auth()->user())->id;
        $this->locale = cache()->has($prefix.".locale") ? cache()->get($prefix.".locale") : app()->getLocale();
        $this->withMeta([
            "locales" => SwitchLocale::getLocales(),
            "locale" => $this->locale
        ]);
    }

    public function fill(NovaRequest $request, $model)
    {
        return;
    }

    public function resolveAttribute($resource, $attribute)
    {
        return [
            "locale" => $resource->translations->pluck('locale')->toArray(),
            "isTranslated" => $resource->hasTranslation($this->locale),
            "value" => data_get($resource, str_replace('->', '.', $attribute))
        ];
    }
}
