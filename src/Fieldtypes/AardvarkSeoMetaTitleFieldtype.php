<?php

namespace WithCandour\AardvarkSeo\Fieldtypes;

use Statamic\Facades\Site;
use Statamic\Fields\Fieldtype;
use WithCandour\AardvarkSeo\Facades\AardvarkStorage;

class AardvarkSeoMetaTitleFieldtype extends Fieldtype
{
    protected $selectable = false;

    /**
     * Load the global seo settings from storage
     */
    public function preload()
    {
        $site = Site::selected();
        $data = AardvarkStorage::getYaml('general', $site, true);

        return [
            'site_name' => $data->get('site_name', ''),
            'title_separator' => $data->get('title_separator', '|'),
            'title_max_length' => config('aardvark-seo.title_max_length', 70),
        ];
    }
}
