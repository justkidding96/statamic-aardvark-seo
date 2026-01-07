<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP;

use Illuminate\Http\Request;
use Statamic\CP\Breadcrumbs;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Facades\Taxonomy;
use Justkidding96\AardvarkSeo\Blueprints\CP\DefaultsSettingsBlueprint;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;
use Justkidding96\AardvarkSeo\Content\ContentDefaults;
use Justkidding96\AardvarkSeo\Events\AardvarkContentDefaultsSaved;

class DefaultsController extends Controller
{
    /**
     * Display a list of all collections/taxonomies
     */
    public function index()
    {
        $this->authorize('view aardvark defaults settings');

        $collections = Collection::all();
        $taxonomies = Taxonomy::all();
        $curr_site = Site::selected();

        $content_types = [
            'Collections' => $collections
                ->filter(function ($collection) use ($curr_site) {
                    return $collection->sites()->contains($curr_site);
                })
                ->map(function ($collection) {
                    return [
                    'count' => $collection->queryEntries()->count(),
                    'handle' => $collection->handle(),
                    'title' => $collection->title(),
                ];
                })->toArray(),
            'Taxonomies' => $taxonomies
                ->filter(function ($taxonomy) use ($curr_site) {
                    return $taxonomy->sites()->contains($curr_site);
                })
                ->map(function ($taxonomy) {
                    return [
                    'count' => $taxonomy->queryTerms()->count(),
                    'handle' => $taxonomy->handle(),
                    'title' => $taxonomy->title(),
                ];
                })->toArray(),
        ];

        $crumbs = Breadcrumbs::make([
            ['text' => 'Aardvark SEO', 'url' => url(config('statamic.cp.route') . '/aardvark-seo/settings')],
            ['text' => 'Content Defaults', 'url' => url(config('statamic.cp.route') . '/aardvark-seo/settings/defaults')],
        ]);

        return view('aardvark-seo::cp.settings.defaults.index', [
            'content_types' => $content_types,
            'crumbs' => $crumbs,
            'title' => 'Content Defaults | Aardvark SEO',
        ]);
    }

    /**
     * Return the view for editing individual content type's content type
     *
     * @param Illuminate\Http\Request $request
     * @param string $content_type
     */
    public function edit(Request $request, string $content_type)
    {
        $this->authorize('view aardvark defaults settings');

        $data = $this->getData($content_type);

        $blueprint = $this->getBlueprint();
        $fields = $blueprint->fields()->addValues($data)->preProcess();

        $repo = $this->getRepositoryFromHandle($content_type);

        $crumbs = Breadcrumbs::make([
            ['text' => 'Aardvark SEO', 'url' => url(config('statamic.cp.route') . '/aardvark-seo/settings')],
            ['text' => 'Content Defaults', 'url' => url(config('statamic.cp.route') . '/aardvark-seo/settings/defaults')],
            ['text' => "{$repo->title()} Defaults", 'url' => url(config('statamic.cp.route') . "/aardvark-seo/settings/defaults/{$content_type}/edit")],
        ]);

        return view('aardvark-seo::cp.settings.defaults.edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'crumbs' => $crumbs,
            'meta' => $fields->meta(),
            'title' => "{$repo->title()} Defaults | Aardvark SEO",
            'repo' => $repo,
            'content_type' => $content_type,
            'values' => $fields->values(),
        ]);
    }

    /**
     * Save the defaults data for this content type
     *
     * @param Illuminate\Http\Request $request
     * @param string $content_type
     */
    public function update(Request $request, string $content_type)
    {
        $this->authorize('update aardvark defaults settings');

        $blueprint = $this->getBlueprint();

        $fields = $blueprint->fields()->addValues($request->all());
        $fields->validate();

        $this->putData($content_type, $fields->process()->values()->toArray());

        $content_type_parts = explode('_', $content_type, 2);
        AardvarkContentDefaultsSaved::dispatch(new ContentDefaults($content_type_parts[0], $content_type_parts[1], Site::selected()));
    }

    public function getBlueprint()
    {
        return DefaultsSettingsBlueprint::requestBlueprint();
    }

    /**
     * Get the data from the relevant defaults file
     *
     * @param string $content_type
     *
     * @return array
     */
    public function getData(string $content_type)
    {
        return AardvarkStorage::getYaml("defaults/{$content_type}", Site::selected());
    }

    /**
     * Set the data for a single content type
     *
     * @param string $content_type
     * @param array $data
     */
    public function putData(string $content_type, array $data)
    {
        AardvarkStorage::putYaml("defaults/{$content_type}", Site::selected(), $data);
    }

    /**
     * Return the content repository from our generated handle
     *
     * @param string $handle
     */
    private function getRepositoryFromHandle(string $handle)
    {
        $parts = explode('_', $handle);
        $type = array_shift($parts);
        $content_handle = implode('_', $parts);

        if ($type === 'collections') {
            return Collection::findByHandle($content_handle);
        } elseif ($type === 'taxonomies') {
            return Taxonomy::findByHandle($content_handle);
        }
    }
}
