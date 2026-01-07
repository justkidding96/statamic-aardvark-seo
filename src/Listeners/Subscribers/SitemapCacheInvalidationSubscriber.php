<?php

namespace Justkidding96\AardvarkSeo\Listeners\Subscribers;

use Justkidding96\AardvarkSeo\Listeners\ContentSitemapCacheInvalidationListener;

class SitemapCacheInvalidationSubscriber
{
    /**
     * Subscribe to content change events to
     * clear the sitemap caches
     *
     * @var array
     */
    protected $events =
    [
        \Statamic\Events\EntrySaved::class,
        \Statamic\Events\EntryDeleted::class,
        \Statamic\Events\TermSaved::class,
        \Statamic\Events\TermDeleted::class,
    ];

    /**
     * Register the invalidation listener for the events
     */
    public function subscribe($events)
    {
        foreach ($this->events as $event) {
            $events->listen($event, ContentSitemapCacheInvalidationListener::class . '@handle');
        }
    }
}
