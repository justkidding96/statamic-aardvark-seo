<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\Web;

use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Support\Facades\Cache;
use Statamic\Facades\Site;
use Justkidding96\AardvarkSeo\Sitemaps\Sitemap;
use Justkidding96\AardvarkSeo\Blueprints\CP\SitemapSettingsBlueprint;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;

class SitemapController extends LaravelController
{
    public function index()
    {
        $storage = AardvarkStorage::getYaml('sitemap', Site::current(), true);

        if (!$storage->get('enable_sitemap', true)) {
            abort(404);
        }

        $site = Site::current();
        $siteUrl = $site->absoluteUrl();

        $view = Cache::remember("aardvark-seo.sitemap-index.{$site->handle()}", $this->getCacheExpiration(), function () use ($siteUrl) {
            return view('aardvark-seo::sitemaps.index', [
                'xmlDefinition' => '<?xml version="1.0" encoding="utf-8"?>',
                'xslLink' => '<?xml-stylesheet type="text/xsl" href="' . $siteUrl . '/aardvark-sitemap.xsl"?>',
                'sitemaps' => Sitemap::all(),
                'version' => $this->getAddonVersion(),
            ])->render();
        });

        return response($view)->header('Content-Type', 'text/xml');
    }

    /**
     * Render a single sitemap based on a handle.
     *
     * @param string $handle
     *
     * @return Illuminate\Http\Response
     */
    public function single($handle)
    {
        $storage = AardvarkStorage::getYaml('sitemap', Site::current(), true);

        if (!$storage->get('enable_sitemap', true)) {
            abort(404);
        }

        $sitemap = Sitemap::findByHandle($handle);

        if (!$sitemap) {
            abort(404);
        }

        $site = Site::current();
        $siteUrl = $site->absoluteUrl();

        $view = Cache::remember("aardvark-seo.sitemap-{$handle}.{$site->handle()}", $this->getCacheExpiration(), function () use ($siteUrl, $sitemap) {
            return view('aardvark-seo::sitemaps.single', [
                'xmlDefinition' => '<?xml version="1.0" encoding="utf-8"?>',
                'xslLink' => '<?xml-stylesheet type="text/xsl" href="' . $siteUrl . '/aardvark-sitemap.xsl"?>',
                'data' => $sitemap->getSitemapItems(),
                'version' => $this->getAddonVersion(),
            ])->render();
        });

        return response($view)->header('Content-Type', 'text/xml');
    }

    /**
     * Return the xsl file required for our sitemap views
     */
    public function xsl()
    {
        $path = __DIR__ . '/../../../../resources/xsl/sitemap.xsl';
        return response(file_get_contents($path))->header('Content-Type', 'text/xsl');
    }

    /**
     * Return the user-set value for the sitemap cache expiration.
     *
     * @return int
     */
    private function getCacheExpiration()
    {
        $storage = AardvarkStorage::getYaml('sitemap', Site::current(), true);
        return $storage->get('sitemap_cache_expiration', 180);
    }

    /**
     * Return the addon version from our composer file
     *
     * @return string
     */
    private function getAddonVersion()
    {
        $path = __DIR__ . '/../../../../composer.json';
        $contents = file_get_contents($path);
        return json_decode($contents, true)['version'];
    }
}
