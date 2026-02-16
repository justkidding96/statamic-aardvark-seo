<?php

namespace Justkidding96\AardvarkSeo\Content;

use Statamic\Facades\Site;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;

class ContentDefaultsGetter
{
    /**
     * Get a key from the defaults for this content
     *
     * @param string $type
     * @param string $handle
     * @param Statamic\Sites\Site $site
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(string $type, string $handle, \Statamic\Sites\Site $site, string $key, $default)
    {
        $defaults = new ContentDefaults($type, $handle, $site);
        return $defaults->data()->get($key, $default);
    }
}
