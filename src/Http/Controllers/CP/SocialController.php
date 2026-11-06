<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP;

use Statamic\Facades\Site;
use Justkidding96\AardvarkSeo\Blueprints\CP\SocialSettingsBlueprint;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;
use Justkidding96\AardvarkSeo\Events\AardvarkGlobalsUpdated;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\Contracts\Publishable;

class SocialController extends Controller implements Publishable
{
    public function index()
    {
        $this->authorize('view aardvark social settings');

        $data = $this->getData();
        $blueprint = $this->getBlueprint();

        return \Statamic\CP\PublishForm::make($blueprint)
            ->title('Social Settings')
            ->values($data)
            ->submittingTo(cp_route('aardvark-seo.social.store'), 'POST');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->authorize('update aardvark social settings');

        $blueprint = $this->getBlueprint();

        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        $this->putData($fields->process()->values()->toArray());

        AardvarkGlobalsUpdated::dispatch('social');
    }

    /**
     * @inheritdoc
     */
    public function getBlueprint()
    {
        return SocialSettingsBlueprint::requestBlueprint();
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return AardvarkStorage::getYaml('social', Site::selected());
    }

    /**
     * @inheritdoc
     */
    public function putData($data)
    {
        return AardvarkStorage::putYaml('social', Site::selected(), $data);
    }
}
