<?php

namespace WithCandour\AardvarkSeo\Fieldtypes;

use Statamic\Facades\Site;
use Statamic\Fields\Fieldtype;
use WithCandour\AardvarkSeo\Facades\AardvarkStorage;

class AardvarkSeoMetaDescriptionFieldtype extends Fieldtype
{
    protected $selectable = false;

    /**
     * Load the global seo settings from storage
     */
    public function preload()
    {
        return [
            'description_max_length' => config('aardvark-seo.description_max_length', 300),
        ];
    }
}
