<?php

namespace Justkidding96\AardvarkSeo\Sitemaps;

use Statamic\Facades\URL;

class SitemapItem
{
    const DEFAULT_CHANGEFREQ = 'daily';
    const DEFAULT_PRIORITY = '0.5';

    /**
     * @var
     */
    private $data_object;

    /**
     * Create a new sitemap item
     */
    public function __construct($content)
    {
        $this->data_object = $content;
    }

    /**
     * Get the url of the current item
     *
     * @return string
     */
    public function getUrl()
    {
        $item = $this->data_object;
        $canonical = $item->get('canonical_url');
        return !empty($canonical) ? URL::makeAbsolute($canonical) : URL::makeAbsolute($item->absoluteUrl());
    }

    /**
     * Get a formatted version of the last modified attribute
     *
     * @return string
     */
    public function getFormattedLastMod()
    {
        return $this->data_object->lastModified()->format('Y-m-d\TH:i:sP');
    }

    /**
     * Return the changefrequency with a fallback
     *
     * @return string
     */
    public function getChangeFreq()
    {
        return $this->data_object->get('sitemap_changefreq') ?: self::DEFAULT_CHANGEFREQ;
    }

    /**
     * Return the priority with a fallback
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->data_object->get('sitemap_priority') ?: self::DEFAULT_PRIORITY;
    }
}
