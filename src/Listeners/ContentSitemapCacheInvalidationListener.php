<?php

namespace Justkidding96\AardvarkSeo\Listeners;

use Illuminate\Support\Facades\Cache;
use Statamic\Support\Str;
use Statamic\Facades\Site;

class ContentSitemapCacheInvalidationListener
{
    public function handle($event)
    {
        $blank_event = new \ReflectionClass($event);
        $content_type = Str::contains($blank_event->getShortName(), 'Term') ? 'term' : 'entry';

        if ($content_type === 'term') {
            $term = $event->term;
            // TODO: This will always return the origin site but this is an upstream issue
            // We need Statamic to tell us which site the term was saved in.
            $site = $term->site();
            $handle = $term->taxonomy()->handle();
        } else {
            $entry = $event->entry;
            $site = $entry->site();
            $handle = $entry->collection()->handle();
        }

        Cache::forget("aardvark-seo.sitemap-index.{$site->handle()}");
        Cache::forget("aardvark-seo.sitemap-{$handle}.{$site->handle()}");
    }
}
