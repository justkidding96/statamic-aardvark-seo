<?php

namespace WithCandour\AardvarkSeo\Schema\Parts;

use Carbon\Carbon;
use Spatie\SchemaOrg\Schema;
use Statamic\Facades\Config;
use Statamic\Facades\URL;
use WithCandour\AardvarkSeo\Modifiers\ParseLocaleModifier;
use WithCandour\AardvarkSeo\Schema\SchemaIds;
use WithCandour\AardvarkSeo\Schema\Parts\WebSite;
use WithCandour\AardvarkSeo\Schema\Parts\Contracts\SchemaPart;

class WebPage implements SchemaPart
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
        $page = Schema::webPage();
        $page->setProperty('@id', self::id());
        $page->url(URL::makeAbsolute(URL::getCurrent()));
        $title = $this->context->get('meta_title') ?: $this->context->get('calculated_title', '');
        $page->name($title);
        $page->isPartOf(['@id' => WebSite::id()]);
        $page->inLanguage(ParseLocaleModifier::index(Config::getFullLocale()));
        if ($this->context->get('last_modified')) {
            $lastModifiedTimestamp = Carbon::parse($this->context->get('last_modified'))->format('c');
            $page->datePublished($lastModifiedTimestamp);
            $page->dateModified($lastModifiedTimestamp);
        }
        return $page;
    }

    public static function id()
    {
        return URL::makeAbsolute(Config::getSiteUrl()) . SchemaIds::WEB_PAGE;
    }
}
