<?php

namespace Justkidding96\AardvarkSeo\Fieldtypes;

use Statamic\Facades\Site;
use Statamic\Fields\Fieldtype;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;

class AardvarkSeoGooglePreviewFieldtype extends Fieldtype
{
    protected $selectable = false;

    /**
     * Load the global seo settings from storage
     */
    public function preload()
    {
        $site = Site::selected();
        $data = AardvarkStorage::getYaml('general', $site, true);

        $faviconUrl = null;
        $faviconValue = $data->get('global_favicon');
        if ($faviconValue) {
            $container = config('aardvark-seo.asset_container', 'assets');
            $path = is_array($faviconValue) ? ($faviconValue[0] ?? null) : $faviconValue;
            if ($path) {
                $asset = \Statamic\Facades\Asset::find("{$container}::{$path}");
                $faviconUrl = $asset?->absoluteUrl();
            }
        }

        return [
            'site_name' => $data->get('site_name', ''),
            'site_url' => $site->absoluteUrl(),
            'title_separator' => $data->get('title_separator', '|'),
            'title_order' => $data->get('title_order', 'title_first'),
            'favicon_url' => $faviconUrl,
        ];
    }
}
