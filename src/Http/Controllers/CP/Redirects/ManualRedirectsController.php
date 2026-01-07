<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP\Redirects;

use Illuminate\Http\Request;
use Statamic\CP\Breadcrumbs;
use Statamic\CP\Column;
use Statamic\Facades\Action;
use Statamic\Facades\Site;
use Justkidding96\AardvarkSeo\Actions\Redirects\DeleteManualRedirectsAction;
use Justkidding96\AardvarkSeo\Events\Redirects\ManualRedirectCreated;
use Justkidding96\AardvarkSeo\Events\Redirects\ManualRedirectDeleted;
use Justkidding96\AardvarkSeo\Events\Redirects\ManualRedirectSaved;
use Justkidding96\AardvarkSeo\Blueprints\CP\Redirects\RedirectBlueprint;
use Justkidding96\AardvarkSeo\Http\Controllers\CP\Controller;
use Justkidding96\AardvarkSeo\Redirects\Repositories\RedirectsRepository;

class ManualRedirectsController extends Controller
{
    /**
     * Display a list of manual redirects in a table with actions
     */
    public function index()
    {
        $this->authorize('view aardvark redirects');

        $crumbs = Breadcrumbs::make([
            ['text' => 'Aardvark SEO', 'url' => cp_route('aardvark-seo.settings')],
            ['text' => __('aardvark-seo::redirects.plural'), 'url' => cp_route('aardvark-seo.redirects.manual-redirects.index')],
        ]);

        // Generate columns
        $columns = [
            Column::make('source_url')->label(__('aardvark-seo::redirects.redirect.source_url')),
            Column::make('target_url')->label(__('aardvark-seo::redirects.redirect.target_url')),
            Column::make('status_code')->label(__('aardvark-seo::redirects.redirect.status_code')),
            Column::make('is_active')->label(__('aardvark-seo::redirects.redirect.is_active')),
        ];

        $redirects = $this->repository()->all()->map(function ($redirect) {
            $delete_url = cp_route('aardvark-seo.redirects.manual-redirects.destroy', [
                'manual_redirect' => $redirect['id'],
            ]);

            $edit_url = cp_route('aardvark-seo.redirects.manual-redirects.edit', [
                'manual_redirect' => $redirect['id'],
            ]);

            $redirect['delete_url'] = $delete_url;
            $redirect['edit_url'] = $edit_url;
            $redirect['title'] = $redirect['source_url'];

            return $redirect;
        });

        return view('aardvark-seo::cp.redirects.manual.index', [
            'title' => __('aardvark-seo::redirects.pages.manual'),
            'columns' => $columns,
            'redirects' => $redirects,
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Return the creation form
     */
    public function create()
    {
        $this->authorize('create aardvark redirects');

        $fields = $this->blueprint()->fields()->addValues([])->preProcess();

        $crumbs = Breadcrumbs::make([
            ['text' => 'Aardvark SEO', 'url' => cp_route('aardvark-seo.settings')],
            ['text' => __('aardvark-seo::redirects.plural'), 'url' => cp_route('aardvark-seo.redirects.manual-redirects.index')],
            ['text' => __('Create'), 'url' => null],
        ]);

        return view('aardvark-seo::cp.redirects.manual.create', [
            'blueprint' => $this->blueprint()->toPublishArray(),
            'crumbs' => $crumbs,
            'meta' => $fields->meta(),
            'title' => __('aardvark-seo::redirects.pages.create'),
            'values' => $fields->values(),
        ]);
    }

    /**
     * Store the newly created redirect
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $this->authorize('create aardvark redirects');

        $fields = $this->blueprint()->fields()->addValues($request->all());
        $fields->validate();
        $values = $fields->process()->values()->toArray();
        $this->repository()->update($values);

        ManualRedirectSaved::dispatch($values);
    }

    /**
     * Return the editing form
     *
     * @param Request $request
     * @param string $redirect_id
     */
    public function edit(Request $request, string $redirect_id)
    {
        $this->authorize('edit aardvark redirects');

        $crumbs = Breadcrumbs::make([
            ['text' => 'Aardvark SEO', 'url' => cp_route('aardvark-seo.settings')],
            ['text' => __('aardvark-seo::redirects.plural'), 'url' => cp_route('aardvark-seo.redirects.manual-redirects.index')],
            ['text' => __('Edit'), 'url' => null],
        ]);

        $exists = $this->repository()->exists($redirect_id);

        if (!$exists) {
            return redirect()->route('statamic.cp.aardvark-seo.redirects.manual-redirects.index');
        }

        $existing = $this->repository()->get($redirect_id);

        $fields = $this->blueprint()->fields()->addValues($existing)->preProcess();

        return view('aardvark-seo::cp.redirects.manual.edit', [
            'blueprint' => $this->blueprint()->toPublishArray(),
            'crumbs' => $crumbs,
            'meta' => $fields->meta(),
            'title' => __('aardvark-seo::redirects.pages.edit'),
            'values' => $fields->values(),
            'redirect_id' => $redirect_id,
        ]);
    }

    /**
     * Update an existing redirect
     *
     * @param Request $request
     * @param string $redirect_id
     */
    public function update(Request $request, string $redirect_id)
    {
        $this->authorize('edit aardvark redirects');

        $fields = $this->blueprint()->fields()->addValues($request->all());
        $fields->validate();
        $values = $fields->process()->values()->toArray();
        $this->repository()->update($values, $redirect_id);

        ManualRedirectSaved::dispatch($values);
    }

    /**
     * Delete an existing redirect
     *
     * @param Request $request
     * @param string $redirect_id
     */
    public function destroy(Request $request, string $redirect_id)
    {
        $this->authorize('edit aardvark redirects');

        $this->repository()->delete($redirect_id);

        ManualRedirectDeleted::dispatch();
    }

    /**
     * Return the bulk actions for the redirects table
     *
     * @param Request $request
     */
    public function bulkActions(Request $request)
    {
        return collect([new DeleteManualRedirectsAction()]);
    }

    /**
     * Run actions from request
     *
     * @param Request $request
     */
    public function runActions(Request $request)
    {
        $this->authorize('edit aardvark redirects');

        $data = $request->validate([
            'action' => 'required',
            'selections' => 'required|array',
            'context' => 'sometimes',
        ]);

        $context = $data['context'] ?? [];

        $action = new DeleteManualRedirectsAction();
        $action->context($context);

        $redirects = collect($data['selections']);

        $action->run($redirects, $request->all());

        ManualRedirectDeleted::dispatch();
    }

    /**
     * Return the blueprint
     */
    private function blueprint()
    {
        return RedirectBlueprint::requestBlueprint();
    }

    /**
     * Return a redirects repository
     */
    private function repository()
    {
        return new RedirectsRepository('redirects/manual', Site::selected());
    }
}
