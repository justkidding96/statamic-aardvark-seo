<?php

namespace WithCandour\AardvarkSeo\Schema\Parts;

use Spatie\SchemaOrg\Schema;
use Statamic\Facades\Asset;
use Statamic\Facades\Config;
use Statamic\Facades\Site;
use Statamic\Facades\URL;
use WithCandour\AardvarkSeo\Facades\AardvarkStorage;
use WithCandour\AardvarkSeo\Schema\SchemaIds;
use WithCandour\AardvarkSeo\Schema\Parts\Contracts\SchemaPart;

class SiteOwner implements SchemaPart
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
        $type = $this->context->get('company_or_person')->raw();
        if ($type === 'company') {
            $owner = Schema::organization();
            $owner->name($this->context->get('target_name')->raw());
            $logo = $this->context->get('company_logo')->value();
            if (!empty($logo)) {
                $logoObject = Schema::imageObject();
                $logoObject->url($logo->absoluteUrl());
                $logoObject->width($logo->width());
                $logoObject->height($logo->height());
                $owner->logo($logoObject);
            }
        } else {
            $owner = Schema::person();
            $owner->name($this->context->get('target_name')->raw());
        }
        $owner->setProperty('@id', self::id());
        $owner->url(URL::makeAbsolute(Config::getSiteUrl()));

        // Social settings
        $social_settings = AardvarkStorage::getYaml('social', Site::current(), true);

        $social_urls = \collect($social_settings->get('social_links', []))
            ->map(function ($social_item) {
                return $social_item['url'] ?? null;
            })
            ->filter();

        if ($social_urls->count() > 0) {
            $owner->sameAs(
                $social_urls->toArray()
            );
        }

        return $owner;
    }

    /**
     * Return the ID of the site owner
     */
    public static function id()
    {
        return URL::makeAbsolute(Config::getSiteUrl()) . SchemaIds::SITE_OWNER;
    }
}
