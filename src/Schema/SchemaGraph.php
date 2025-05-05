<?php

namespace WithCandour\AardvarkSeo\Schema;

use Spatie\SchemaOrg\Graph;
use WithCandour\AardvarkSeo\Blueprints\CP\GeneralSettingsBlueprint;
use WithCandour\AardvarkSeo\Facades\PageDataParser;
use WithCandour\AardvarkSeo\Schema\SchemaIds;
use WithCandour\AardvarkSeo\Schema\Parts\Breadcrumbs;
use WithCandour\AardvarkSeo\Schema\Parts\SiteOwner;
use WithCandour\AardvarkSeo\Schema\Parts\WebPage;
use WithCandour\AardvarkSeo\Schema\Parts\WebSite;

class SchemaGraph
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $context;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $globals;

    /**
     * @var Graph
     */
    protected $graph;

    public function __construct($context)
    {
        $this->context = $context;
        $this->graph = new Graph();

        $this->globals = PageDataParser::getSettingsBlueprintWithValues($context, 'general', new GeneralSettingsBlueprint());

        $this->populateData();
    }

    private function populateData()
    {
        $siteOwner = new SiteOwner($this->globals);
        $webSite = new WebSite($this->globals);
        $webPage = new WebPage($this->context);
        $webPageData = $webPage->data();

        $enableBreadcrumbs = $this->globals->get('enable_breadcrumbs') ?? false;
        if ($enableBreadcrumbs instanceof \Statamic\Fields\Value) {
            $enableBreadcrumbs = (bool) $enableBreadcrumbs->value();
        }

        // If breadcrumbs are enabled - add them to the graph
        if ($enableBreadcrumbs && $this->context->get('url', '') !== '/') {
            $breadcrumbs = new Breadcrumbs();
            $webPageData->breadcrumb($breadcrumbs->data());
        }

        $this->graph->add($siteOwner->data());
        $this->graph->add($webSite->data());
        $this->graph->add($webPageData);
    }

    public function build()
    {
        return $this->graph;
    }
}
