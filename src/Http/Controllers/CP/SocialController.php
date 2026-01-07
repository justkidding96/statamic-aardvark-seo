<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP;

use Statamic\CP\Breadcrumbs;
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
        $fields = $blueprint->fields()->addValues($data)->preProcess();

        $crumbs = Breadcrumbs::make([
            ['text' => 'Aardvark SEO', 'url' => url(config('statamic.cp.route') . '/aardvark-seo/settings')],
            ['text' => 'Social Settings', 'url' => url(config('statamic.cp.route') . '/aardvark-seo/settings/social')],
        ]);

        return view('aardvark-seo::cp.settings.social', [
            'blueprint' => $blueprint->toPublishArray(),
            'crumbs' => $crumbs,
            'meta' => $fields->meta(),
            'title' => 'Social Settings | Aardvark SEO',
            'values' => $fields->values(),
        ]);
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
