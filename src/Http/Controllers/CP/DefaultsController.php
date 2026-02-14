<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP;

use Illuminate\Http\Request;
use Statamic\CP\Column;
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
    public function index(Request $request)
    {
        $this->authorize('view aardvark defaults settings');

        $curr_site = Site::selected();

        $items = collect();

        Collection::all()
            ->filter(fn ($collection) => $collection->sites()->contains($curr_site))
            ->each(function ($collection) use ($items) {
                $handle = 'collections_' . $collection->handle();
                $items->push([
                    'id' => $handle,
                    'title' => $collection->title(),
                    'handle' => $collection->handle(),
                    'type' => __('Collection'),
                    'entries' => $collection->queryEntries()->count(),
                    'edit_url' => cp_route('aardvark-seo.defaults.edit', ['default' => $handle]),
                ]);
            });

        Taxonomy::all()
            ->filter(fn ($taxonomy) => $taxonomy->sites()->contains($curr_site))
            ->each(function ($taxonomy) use ($items) {
                $handle = 'taxonomies_' . $taxonomy->handle();
                $items->push([
                    'id' => $handle,
                    'title' => $taxonomy->title(),
                    'handle' => $taxonomy->handle(),
                    'type' => __('Taxonomy'),
                    'entries' => $taxonomy->queryTerms()->count(),
                    'edit_url' => cp_route('aardvark-seo.defaults.edit', ['default' => $handle]),
                ]);
            });

        // Apply search filter
        if ($search = $request->input('search')) {
            $search = strtolower($search);
            $items = $items->filter(fn ($item) =>
                str_contains(strtolower($item['title']), $search) ||
                str_contains(strtolower($item['handle']), $search) ||
                str_contains(strtolower($item['type']), $search)
            );
        }

        // Apply sorting
        if ($sortColumn = $request->input('sort')) {
            $sortDirection = $request->input('order', 'asc');
            $items = $items->sortBy($sortColumn, SORT_REGULAR, $sortDirection === 'desc');
        }

        $columns = [
            Column::make('title')->label(__('Title')),
            Column::make('handle')->label(__('Handle')),
            Column::make('type')->label(__('Type')),
            Column::make('entries')->label(__('Entries')),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $items->values(),
                'meta' => [
                    'columns' => $columns,
                    'activeFilterBadges' => [],
                ],
            ]);
        }

        return view('aardvark-seo::cp.settings.defaults.index', [
            'title' => __('Content Defaults'),
            'columns' => $columns,
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
        $repo = $this->getRepositoryFromHandle($content_type);

        return \Statamic\CP\PublishForm::make($blueprint)
            ->title("{$repo->title()} Defaults")
            ->values($data)
            ->submittingTo(cp_route('aardvark-seo.defaults.update', ['default' => $content_type]), 'PATCH');
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
