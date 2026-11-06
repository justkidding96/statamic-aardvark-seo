<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP;

use Statamic\Facades\Site;
use Justkidding96\AardvarkSeo\Blueprints\CP\SitemapSettingsBlueprint;
use Justkidding96\AardvarkSeo\Events\AardvarkGlobalsUpdated;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\Contracts\Publishable;

class SitemapController extends Controller implements Publishable
{
    public function index()
    {
        $this->authorize('view aardvark sitemap settings');

        $data = $this->getData();
        $blueprint = $this->getBlueprint();

        return \Statamic\CP\PublishForm::make($blueprint)
            ->title('Sitemap Settings')
            ->values($data)
            ->submittingTo(cp_route('aardvark-seo.sitemap.store'), 'POST');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->authorize('update aardvark sitemap settings');

        $blueprint = $this->getBlueprint();

        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        $this->putData($fields->process()->values()->toArray());

        AardvarkGlobalsUpdated::dispatch('sitemap');
    }

    /**
     * @inheritdoc
     */
    public function getBlueprint()
    {
        return SitemapSettingsBlueprint::requestBlueprint();
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return AardvarkStorage::getYaml('sitemap', Site::selected());
    }

    /**
     * @inheritdoc
     */
    public function putData($data)
    {
        return AardvarkStorage::putYaml('sitemap', Site::selected(), $data);
    }
}
