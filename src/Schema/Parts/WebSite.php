<?php

namespace Justkidding96\AardvarkSeo\Schema\Parts;

use Spatie\SchemaOrg\Schema;
use Statamic\Facades\Config;
use Statamic\Facades\URL;
use Justkidding96\AardvarkSeo\Schema\SchemaIds;
use Justkidding96\AardvarkSeo\Schema\Parts\SiteOwner;
use Justkidding96\AardvarkSeo\Schema\Parts\Contracts\SchemaPart;

class WebSite implements SchemaPart
{
    /**
     * @var array
     */
    public $context;

    public function __construct($context = [])
    {
        $this->context = $context;
    }

    public function data()
    {
        $site = Schema::webSite();
        $site->url(URL::makeAbsolute(Config::getSiteUrl()));
        $site->setProperty('publisher', ['@id' => SiteOwner::id()]);
        $site->setProperty('@id', self::id());
        $name = $this->context->get('site_name')->value() ?? config('app.name') ?? '';
        if ($name) {
            $site->name($name);
        }
        return $site;
    }

    public static function id()
    {
        return URL::makeAbsolute(Config::getSiteUrl()) . SchemaIds::WEB_SITE;
    }
}
