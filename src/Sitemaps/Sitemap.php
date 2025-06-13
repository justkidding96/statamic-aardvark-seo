<?php

namespace WithCandour\AardvarkSeo\Sitemaps;

use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Facades\Taxonomy;
use Statamic\Facades\Term;
use Statamic\Support\Str;
use WithCandour\AardvarkSeo\Facades\AardvarkStorage;
use WithCandour\AardvarkSeo\Facades\ContentDefaults as Defaults;
use WithCandour\AardvarkSeo\Blueprints\CP\SitemapSettingsBlueprint;

class Sitemap
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var Statamic\Sites\Site
     */
    public $site;

    /**
     * Create a new sitemap.
     *
     * @param string $type
     * @param string $handle
     * @param Statamic\Sites\Site $site
     */
    public function __construct($type, $handle, $site)
    {
        $this->type = $type;
        $this->handle = $handle;
        $this->site = $site;
        $this->generateSitemapURL();
    }

    /**
     * Generate a list of all possible sitemaps.
     *
     * @return array
     */
    public static function all()
    {
        $collections = collect(Collection::all())
            ->filter(function ($collection) {
                return !is_null($collection->route(Site::current()->handle()));
            })
            ->map(function ($collection) {
                $indexable = self::getIndexStatusForContent('collections', $collection->handle(), Site::current());
                $excluded = self::getExcludedStatusForContent('collections', $collection->handle(), Site::current());
                return [
                    'type' => 'collection',
                    'handle' => $collection->handle(),
                    'indexable' => $indexable,
                    'excluded' => $excluded,
                ];
            });
        $taxonomies = collect(Taxonomy::all())
            ->map(function ($taxonomy) {
                $indexable = self::getIndexStatusForContent('taxonomies', $taxonomy->handle(), Site::current());
                $excluded = self::getExcludedStatusForContent('taxonomies', $taxonomy->handle(), Site::current());
                return [
                    'type' => 'taxonomy',
                    'handle' => $taxonomy->handle(),
                    'indexable' => $indexable,
                    'excluded' => $excluded,
                ];
            });

        $sitemaps = collect([$collections, $taxonomies])->collapse();

        $filtered_sitemaps = $sitemaps->filter(function ($content_type) {
            $indexable = $content_type['indexable'];
            $excluded = $content_type['excluded'];
            return $indexable && !$excluded;
        });

        $sitemap_objects = $filtered_sitemaps->map(function ($sitemap) {
            $data = collect($sitemap);
            return new Sitemap($data->get('type'), $data->get('handle'), Site::current());
        });

        return $sitemap_objects->all();
    }

    /**
     * Return a list of entries for the sitemap to display.
     *
     * @return array
     */
    public function getSitemapItems()
    {
        switch ($this->type) {
            case 'collection':
                $items = Entry::query()
                    ->where('collection', $this->handle)
                    ->where('site', Site::current()->handle())
                    ->where('date', '<=', now())
                    ->get()
                    ->filter(function ($entry) {
                        if ($entry->blueprint()->handle() === 'link') {
                            return false;
                        }
                        $redirect = $entry->get('redirect');
                        if (is_string($redirect)) {
                            return empty($redirect);
                        }
                        return true;
                    });
                break;
            case 'taxonomy':
                $items = Term::query()
                    ->where('taxonomy', $this->handle)
                    ->where('site', Site::current()->handle())
                    ->where('redirect', '=', null)
                    ->get();

                // If a collection has been set for this taxonomy - use it to generate a more relevant sitemap URL
                $settings = self::getSitemapSettings();

                $mapping = collect($settings->get('taxonomy_collection_map')->value())
                    ->filter(function ($row) {
                        $taxonomy = $row['taxonomy'] ?? null;

                        if ($taxonomy instanceof \Statamic\Fields\Value) {
                            $taxonomy = $taxonomy->value();
                        }

                        if (!empty($taxonomy)) {
                            return $taxonomy->handle() === $this->handle;
                        }
                    })
                    ->first();

                $collection = $mapping['collection'] ?? null;

                if (!empty($collection)) {
                    if ($collection instanceof \Statamic\Fields\Value) {
                        $collection = $collection->value();
                    }

                    $items->each->collection($collection);
                }

                break;
            default:
                $items = Entry::query()
                    ->where('collection', 'pages')
                    ->where('site', Site::current()->handle())
                    ->where('redirect', '=', null)
                    ->get();
        }

        $items = $items->filter(function ($item) {
            if ($item->published()) {

                // Handle entries where we have an explicit value
                if ($item->data()->has('no_index_page')) {
                    if ($item->data()->get('no_index_page')) {
                        return false;
                    }

                    return true;
                }

                // Handle entries where the `no_index_page` value is inherited from an origin
                if ($item->hasOrigin()) {
                    if ($item->origin()->data()->has('no_index_page')) {
                        if($item->origin()->data()->get('no_index_page')) {
                            return false;
                        }

                        return true;
                    }

                    return true;
                }

                // Published entries which don't have an explicit value or an origin value should be returned
                return true;
            }

            return false;
        });

        $sitemap_items = collect($items)->map(function ($item) {
            return new SitemapItem($item);
        });

        $sitemapData = $sitemap_items
            ->unique(function ($item) {
                return $item->getUrl();
            })
            ->map(function ($item) {
                $data = [
                    'url' => $item->getUrl(),
                    'changefreq' => $item->getChangeFreq(),
                    'priority' => $item->getPriority(),
                    'lastmod' => $item->getFormattedLastMod(),
                ];

                return $data;
            });

        return $sitemapData->all();
    }

    /**
     * Get the sitemap settings from global settings
     */
    public static function getSitemapSettings()
    {
        $settings = AardvarkStorage::getYaml('sitemap', Site::current());
        $blueprint = SitemapSettingsBlueprint::requestBlueprint();
        return $blueprint->fields()->addValues($settings)->augment()->values();
    }

    /**
     * Get the index status for a given content type on a site
     *
     * @param string $type
     * @param string $handle
     * @param Statamic\Sites\Site $site
     */
    public static function getIndexStatusForContent($type = 'collections', $handle = 'pages', $site = null)
    {
        $no_indexed = Defaults::get($type, $handle, $site, 'no_index_page', 0);
        return !$no_indexed;
    }

    /**
     * Detect whether this content type is excluded
     *
     * @param string $type
     * @param string $handle
     * @param Statamic\Sites\Site $site
     */
    public static function getExcludedStatusForContent($type = 'collections', $handle = 'pages', $site = null)
    {
        $settings = AardvarkStorage::getYaml('sitemap', $site, true);
        $excluded_array = $settings->get("exclude_{$type}");

        if (empty($excluded_array)) {
            return false;
        }

        $excluded = in_array($handle, $settings->get("exclude_{$type}"));
        return $excluded;
    }

    /**
     * Automatically generates some meta data about this sitemap.
     *
     * @return string
     */
    public function generateSitemapURL()
    {
        $handle = $this->handle ?: 'pages';
        $this->route = sprintf('sitemap_%s.xml', $handle);
        $this->url = Str::ensureRight($this->site->absoluteUrl(), '/') . $this->route;
        return $this->url;
    }

    /**
     * Get the date of the most recently edited entry in the sitemap.
     *
     * @return string
     */
    public function getLastMod()
    {
        $items = collect($this->getSitemapItems())->sortByDesc('lastmod');
        return $items->first()['lastmod'];
    }

    /**
     * Get a list of sitemaps matching a specific handle.
     *
     * @param string $handle
     *
     * @return WithCandour\AardvarkSeo\Sitemaps\Sitemap
     */
    public static function findByHandle($handle = '')
    {
        return collect(self::all())->filter(function ($sitemap) use ($handle) {
            return $sitemap->handle === $handle;
        })->first();
    }
}
