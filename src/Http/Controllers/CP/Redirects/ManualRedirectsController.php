<?php

namespace WithCandour\AardvarkSeo\Http\Controllers\CP\Redirects;

use Illuminate\Http\Request;
use Statamic\CP\Breadcrumbs;
use Statamic\CP\Column;
use Statamic\Facades\Action;
use Statamic\Facades\Site;
use WithCandour\AardvarkSeo\Actions\Redirects\DeleteManualRedirectsAction;
use WithCandour\AardvarkSeo\Events\Redirects\ManualRedirectCreated;
use WithCandour\AardvarkSeo\Events\Redirects\ManualRedirectDeleted;
use WithCandour\AardvarkSeo\Events\Redirects\ManualRedirectSaved;
use WithCandour\AardvarkSeo\Blueprints\CP\Redirects\RedirectBlueprint;
use WithCandour\AardvarkSeo\Http\Controllers\CP\Controller;
use WithCandour\AardvarkSeo\Redirects\Repositories\RedirectsRepository;

class ManualRedirectsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view aardvark redirects');

        $columns = [
            Column::make('source_url')->label(__('aardvark-seo::redirects.redirect.source_url')),
            Column::make('target_url')->label(__('aardvark-seo::redirects.redirect.target_url')),
            Column::make('status_code')->label(__('aardvark-seo::redirects.redirect.status_code')),
            Column::make('is_active')->label(__('aardvark-seo::redirects.redirect.is_active')),
        ];

        // Paginate response
        $response = $this->repository()
            ->setColumns($columns)
            ->paginate(
            perPage: (int) $request->input('perPage', 10),
            query: $request->query()
        );

        // If the request is an AJAX request, return a JSON response
        if ($request->wantsJson()) {
            return response()->json($response);
        }

        return view('aardvark-seo::cp.redirects.manual.index', [
            'title' => __('aardvark-seo::redirects.pages.manual'),
            'columns' => $columns,
            'items' => $response['data'],
            'crumbs' => $this->breadcrumbs(''),
            'meta' => $response['meta'],
        ]);
    }

    /**
     * Return the creation form
     */
    public function create()
    {
        $this->authorize('create aardvark redirects');

        $fields = $this->blueprint()->fields()->addValues([])->preProcess();

        return view('aardvark-seo::cp.redirects.manual.create', [
            'blueprint' => $this->blueprint()->toPublishArray(),
            'crumbs' => $this->breadcrumbs('Create'),
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

        if (!$this->repository()->exists($redirect_id)) {
            return redirect()->route('statamic.cp.aardvark-seo.redirects.manual-redirects.index');
        }

        $fields = $this->blueprint()->fields()
            ->addValues($this->repository()->get($redirect_id))
            ->preProcess();

        return view('aardvark-seo::cp.redirects.manual.edit', [
            'blueprint' => $this->blueprint()->toPublishArray(),
            'crumbs' => $this->breadcrumbs('Edit'),
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
     * @return Collection
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

    private function breadcrumbs(string $current)
    {
        return Breadcrumbs::make([
            ['text' => 'Aardvark SEO', 'url' => cp_route('aardvark-seo.settings')],
            ['text' => __('aardvark-seo::redirects.plural'), 'url' => cp_route('aardvark-seo.redirects.manual-redirects.index')],
            ['text' => __($current), 'url' => null],
        ]);
    }
}
