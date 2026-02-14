<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP;

use Illuminate\Http\Request;
use Justkidding96\AardvarkSeo\Actions\Redirects\DeleteRedirectsAction;
use Justkidding96\AardvarkSeo\Blueprints\CP\RedirectBlueprint;
use Justkidding96\AardvarkSeo\Events\Redirects\RedirectCreated;
use Justkidding96\AardvarkSeo\Events\Redirects\RedirectDeleted;
use Justkidding96\AardvarkSeo\Events\Redirects\RedirectSaved;
use Justkidding96\AardvarkSeo\Redirects\Repositories\RedirectsRepository;
use Statamic\CP\Column;
use Statamic\Facades\Site;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RedirectsController extends Controller
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

        return view('aardvark-seo::cp.redirects.index', [
            'title' => __('aardvark-seo::redirects.plural'),
            'columns' => $columns,
            'items' => $response['data'],
            'meta' => $response['meta'],
        ]);
    }

    /**
     * Return the creation form
     */
    public function create(Request $request)
    {
        $this->authorize('create aardvark redirects');

        // Pre-fill values from query parameters (for automatic redirect prompts)
        $initialValues = [];
        if ($request->has('source')) {
            $initialValues['source_url'] = $request->input('source');
        }
        if ($request->has('target')) {
            $initialValues['target_url'] = $request->input('target');
        }

        return PublishForm::make($this->blueprint())
            ->title(__('aardvark-seo::redirects.pages.create'))
            ->values($initialValues)
            ->submittingTo(cp_route('aardvark-seo.redirects.store'), 'POST');
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

        RedirectCreated::dispatch($values);
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
            return redirect()->route('statamic.cp.aardvark-seo.redirects.index');
        }

        $data = $this->repository()->get($redirect_id);

        return \Statamic\CP\PublishForm::make($this->blueprint())
            ->title(__('aardvark-seo::redirects.pages.edit'))
            ->values($data)
            ->submittingTo(cp_route('aardvark-seo.redirects.update', ['redirect' => $redirect_id]), 'PATCH');
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

        RedirectSaved::dispatch($values);
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

        RedirectDeleted::dispatch();
    }

    /**
     * Return the bulk actions for the redirects table
     *
     * @param Request $request
     * @return Collection
     */
    public function bulkActions(Request $request)
    {
        return collect([new DeleteRedirectsAction()]);
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

        $action = new DeleteRedirectsAction();
        $action->context($context);

        $redirects = collect($data['selections']);

        $action->run($redirects, $request->all());

        RedirectDeleted::dispatch();
    }

    /**
     * Export all redirects as a CSV file.
     */
    public function export(Request $request)
    {
        $this->authorize('view aardvark redirects');

        $repository = $this->repository();
        $redirects = $repository->all();

        return new StreamedResponse(function () use ($redirects) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['source_url', 'target_url', 'status_code']);

            foreach ($redirects as $redirect) {
                fputcsv($handle, [
                    $redirect['source_url'] ?? '',
                    $redirect['target_url'] ?? '',
                    $redirect['status_code'] ?? '302',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="redirects.csv"',
        ]);
    }

    /**
     * Import redirects from a CSV file.
     */
    public function import(Request $request)
    {
        $this->authorize('create aardvark redirects');

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return back()->with('error', __('aardvark-seo::redirects.import.error_reading'));
        }

        $header = fgetcsv($handle);

        if (! $header || count($header) < 2) {
            fclose($handle);
            return back()->with('error', __('aardvark-seo::redirects.import.invalid_format'));
        }

        // Normalize header names
        $header = array_map(fn ($col) => strtolower(trim($col)), $header);

        // Map columns to expected fields
        $sourceIndex = array_search('source_url', $header);
        $targetIndex = array_search('target_url', $header);
        $statusIndex = array_search('status_code', $header);

        if ($sourceIndex === false || $targetIndex === false) {
            fclose($handle);
            return back()->with('error', __('aardvark-seo::redirects.import.missing_columns'));
        }

        $repository = $this->repository();
        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $sourceUrl = trim($row[$sourceIndex] ?? '');
            $targetUrl = trim($row[$targetIndex] ?? '');
            $statusCode = trim($row[$statusIndex] ?? '302');

            if (empty($sourceUrl) || empty($targetUrl)) {
                $skipped++;
                continue;
            }

            if (! in_array($statusCode, ['301', '302'])) {
                $statusCode = '302';
            }

            $repository->update([
                'source_url' => $sourceUrl,
                'target_url' => $targetUrl,
                'status_code' => $statusCode,
                'is_active' => true,
            ]);

            $imported++;
        }

        fclose($handle);

        return back()->with('success', __('aardvark-seo::redirects.import.success', [
            'imported' => $imported,
            'skipped' => $skipped,
        ]));
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
