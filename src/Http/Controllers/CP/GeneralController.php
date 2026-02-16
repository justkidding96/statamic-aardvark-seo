<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP;

use Statamic\Facades\Site;
use Statamic\Facades\User;
use Statamic\CP\PublishForm;
use Justkidding96\AardvarkSeo\Blueprints\CP\GeneralSettingsBlueprint;
use Justkidding96\AardvarkSeo\Events\AardvarkGlobalsUpdated;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\Contracts\Publishable;

class GeneralController extends Controller implements Publishable
{
    public function index()
    {
        $this->authorize('view aardvark general settings');

        $data = $this->getData();
        $blueprint = $this->getBlueprint();

        return PublishForm::make($blueprint)
            ->title('General SEO Settings')
            ->values($data)
            ->submittingTo(cp_route('aardvark-seo.general.store'), 'POST');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->authorize('update aardvark general settings');

        $blueprint = $this->getBlueprint();

        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        $this->putData($fields->process()->values()->toArray());

        AardvarkGlobalsUpdated::dispatch('general');
    }

    /**
     * Redirects from the top level SEO nav item
     */
    public function settingsRedirect()
    {
        $groups = collect([
            'general',
            'marketing',
            'defaults',
            'social',
            'sitemap',
        ]);

        $first_group = $groups->filter(function ($group) {
            return User::current()->can("view aardvark {$group} settings");
        })->first();

        if (!empty($first_group)) {
            return redirect()->route("statamic.cp.aardvark-seo.{$first_group}.index");
        }

        // If no permissions are found use Statamic to inform the user
        $this->authorize('view aardvark general settings');
    }

    /**
     * @inheritdoc
     */
    public function getBlueprint()
    {
        return GeneralSettingsBlueprint::requestBlueprint();
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return AardvarkStorage::getYaml('general', Site::selected());
    }

    /**
     * @inheritdoc
     */
    public function putData($data)
    {
        return AardvarkStorage::putYaml('general', Site::selected(), $data);
    }
}
