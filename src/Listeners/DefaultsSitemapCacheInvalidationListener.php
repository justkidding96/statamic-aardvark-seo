<?php

namespace Justkidding96\AardvarkSeo\Listeners;

use Illuminate\Support\Facades\Cache;

class DefaultsSitemapCacheInvalidationListener
{
    public function handle(\Justkidding96\AardvarkSeo\Events\AardvarkContentDefaultsSaved $event)
    {
        $defaults = $event->defaults;
        $site = $defaults->site->handle();
        $handle = $defaults->handle;

        Cache::forget("aardvark-seo.sitemap-index.{$site}");
        Cache::forget("aardvark-seo.sitemap-{$handle}.{$site}");
    }
}
