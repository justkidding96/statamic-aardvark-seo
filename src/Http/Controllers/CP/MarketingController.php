<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP;

use Statamic\Facades\Site;
use Justkidding96\AardvarkSeo\Blueprints\CP\MarketingSettingsBlueprint;
use Justkidding96\AardvarkSeo\Events\AardvarkGlobalsUpdated;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\Contracts\Publishable;

class MarketingController extends Controller implements Publishable
{
    public function index()
    {
        $this->authorize('view aardvark marketing settings');

        $data = $this->getData();
        $blueprint = $this->getBlueprint();

        return \Statamic\CP\PublishForm::make($blueprint)
            ->title('Marketing Settings')
            ->values($data)
            ->submittingTo(cp_route('aardvark-seo.marketing.store'), 'POST');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->authorize('update aardvark marketing settings');

        $blueprint = $this->getBlueprint();

        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        $this->putData($fields->process()->values()->toArray());

        AardvarkGlobalsUpdated::dispatch('marketing');
    }

    /**
     * @inheritdoc
     */
    public function getBlueprint()
    {
        return MarketingSettingsBlueprint::requestBlueprint();
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return AardvarkStorage::getYaml('marketing', Site::selected());
    }

    /**
     * @inheritdoc
     */
    public function putData($data)
    {
        return AardvarkStorage::putYaml('marketing', Site::selected(), $data);
    }
}
