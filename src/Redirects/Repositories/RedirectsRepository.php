<?php

namespace Justkidding96\AardvarkSeo\Redirects\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Statamic\Facades\Url;
use Statamic\Sites\Site;
use Statamic\Support\Str;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;

class RedirectsRepository
{
    protected Collection $redirects;
    protected string $storage_key;
    protected Site $site;
    protected array $columns = [];
    protected array $filterCallbacks = [];

    /**
     * Initialize the repository with the storage key and site.
     */
    public function __construct(string $storage_key = 'redirects/manual', ?Site $site = null)
    {
        $this->storage_key = $storage_key;
        $this->site = $site ?: Site::current();
        $this->getRedirectsFromFile();
    }

    /**
     * Apply accumulated filter callbacks to the collection.
     */
    protected function applyFilters(): static
    {
        if (empty($this->filterCallbacks)) {
            return $this;
        }

        $filtered = $this->redirects->filter(function ($item) {
            foreach ($this->filterCallbacks as $callback) {
                if (! $callback($item)) {
                    return false;
                }
            }
            return true;
        });

        $clone = clone $this;
        $clone->redirects = $filtered->values();
        $clone->filterCallbacks = [];
        return $clone;
    }

    /**
     * Compare two values based on a given operator.
     */
    protected function compare(mixed $actual, string $operator, mixed $value): bool
    {
        return match (strtolower($operator)) {
            '=', '=='   => $actual == $value,
            '!=', '<>'  => $actual != $value,
            '>'         => $actual > $value,
            '<'         => $actual < $value,
            '>='        => $actual >= $value,
            '<='        => $actual <= $value,
            '==='       => $actual === $value,
            '!=='       => $actual !== $value,
            'like'      => stripos((string) $actual, str_replace('%', '', $value)) !== false,
            'not like'  => stripos((string) $actual, str_replace('%', '', $value)) === false,
            default     => false,
        };
    }

    /**
     * Delete a redirect by its ID.
     */
    public function delete(string $redirect_id): void
    {
        if (! $this->exists($redirect_id)) {
            return;
        }

        $this->redirects = $this->redirects
            ->reject(fn($redirect) => $redirect['id'] === $redirect_id)
            ->values();

        $this->writeToFile();
    }

    /**
     * Return all redirects.
     */
    public function all(): Collection
    {
        return $this->redirects;
    }

    /**
     * Check if a redirect with the given ID exists.
     */
    public function exists(string $id): bool
    {
        return $this->redirects->contains('id', $id);
    }

    /**
     * Retrieve a redirect by its ID.
     */
    public function get(string $id): array|false|null
    {
        return $this->exists($id)
            ? $this->redirects->where('id', $id)->first()
            : false;
    }

    /**
     * Retrieve a redirect by its source URL.
     */
    public function getBySource(string $source_url): array|false|null
    {
        return $this->sourceExists($source_url)
            ? $this->redirects->where('source_url', $source_url)->first()
            : false;
    }

    /**
     * Load redirect data from YAML file.
     */
    public function getRedirectsFromFile(): void
    {
        $this->redirects = collect(AardvarkStorage::getYaml($this->storage_key, $this->site, true));
    }

    /**
     * Paginate the redirect collection with optional search query.
     */
    public function paginate(int $perPage = 15, array $query = []): array
    {
        $page = $query['page'] ?? request()->input('page', 1);
        $search = $query['search'] ?? request()->input('search');

        // Apply search query
        if ($search) {
            $this->where('source_url', 'like', "%{$search}%")
                ->orWhere('target_url', 'like', "%{$search}%");
        }

        $filtered = $this->applyFilters()->redirects;
        $total = $filtered->count();
        $pageItems = $filtered->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $pageItems, $total, $perPage, $page, ['path' => request()->url(), 'query' => request()->query()]
        );

        // Format data
        $redirects = array_map(function ($redirect) {
            $delete_url = cp_route('aardvark-seo.redirects.destroy', [
                'redirect' => $redirect['id'],
            ]);

            $edit_url = cp_route('aardvark-seo.redirects.edit', [
                'redirect' => $redirect['id'],
            ]);

            $redirect['delete_url'] = $delete_url;
            $redirect['edit_url'] = $edit_url;
            $redirect['title'] = $redirect['source_url'];
            return $redirect;
        }, $paginator->items());

        return [
            'data' => $redirects,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'path' => $paginator->path(),
                'links' => Arr::get($paginator->toArray(), 'links', []),
                'activeFilterBadges' => [],
                'columns' => $this->columns,
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl(),
            ],
        ];
    }

    /**
     * Process incoming data before saving.
     */
    private function processRaw(array $data): array
    {
        $data['source_url'] = Str::ensureLeft(Url::makeRelative($data['source_url']), '/');
        return $data;
    }

    /**
     * Check if a redirect with a given source URL exists.
     */
    public function sourceExists(string $source_url): bool
    {
        return $this->redirects->contains('source_url', $source_url);
    }

    /**
     * Set columns metadata for frontend table rendering.
     */
    public function setColumns(array $columns = []): static
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add a filter condition using AND logic.
     */
    public function where(string $key, mixed $operator = null, mixed $value = null): static
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }

        $this->filterCallbacks[] = fn($item) =>
        $this->compare(data_get($item, $key), $operator, $value);

        return $this;
    }

    /**
     * Add a filter condition using OR logic.
     */
    public function orWhere(string $key, mixed $operator = null, mixed $value = null): static
    {
        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }

        $last = array_pop($this->filterCallbacks);

        $this->filterCallbacks[] = fn($item) =>
            $last($item) || $this->compare(data_get($item, $key), $operator, $value);

        return $this;
    }

    /**
     * Create or update a redirect entry.
     */
    public function update(array $data, ?string $redirect_id = null): void
    {
        $data = $this->processRaw($data);

        if (! $redirect_id) {
            if (! $this->sourceExists($data['source_url'])) {
                $redirect_id = (string) Str::uuid();
                $data['id'] = $redirect_id;
            } else {
                $redirect_id = $this->getBySource($data['source_url'])['id'];
                $data['id'] = $redirect_id;
            }
        } else {
            $data['id'] = $redirect_id;
        }

        $this->redirects = $this->redirects->map(fn($redirect) =>
        $redirect['id'] === $redirect_id ? $data : $redirect
        );

        if (! $this->exists($redirect_id)) {
            $this->redirects->push($data);
        }

        $this->writeToFile();
    }

    /**
     * Save current redirect data back to YAML.
     */
    private function writeToFile(): void
    {
        AardvarkStorage::putYaml($this->storage_key, $this->site, $this->redirects->toArray());
    }
}