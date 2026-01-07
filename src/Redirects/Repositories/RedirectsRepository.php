<?php

namespace Justkidding96\AardvarkSeo\Redirects\Repositories;

use Illuminate\Support\Collection;
use Statamic\Facades\Url;
use Statamic\Sites\Site;
use Statamic\Support\Str;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;

class RedirectsRepository
{
    /**
     * @var Collection
     */
    protected $redirects;

    /**
     * @var string
     */
    protected $storage_key;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @param string $storage_key
     * @param Site|null $site
     */
    public function __construct(string $storage_key = 'redirects/manual', $site = null)
    {
        $this->storage_key = $storage_key;
        $this->site = $site ?: Site::current();

        $this->getRedirectsFromFile();
    }

    /**
     * Return whether a redirect with a particular id exists
     *
     * @param string $id
     *
     * @return bool
     */
    public function exists(string $id)
    {
        return $this->redirects->contains('id', $id);
    }

    /**
     * Return whether a redirect with a particular source_url exists
     *
     * @param string $source_url
     *
     * @return bool
     */
    public function sourceExists(string $source_url)
    {
        return $this->redirects->contains(function ($redirect) use ($source_url) {
            $redirect_source_url = str_replace('/', '\/', $redirect['source_url']);
            return preg_match("/^{$redirect_source_url}/i", $source_url) === 1 && $source_url !== $redirect['target_url'];
        });
    }

    /**
     * Return a redirect from the file
     *
     * @param string $id
     *
     * @return array|false|null
     */
    public function get($id)
    {
        if (!$this->exists($id)) {
            return false;
        }

        return $this->redirects->where('id', $id)->first();
    }

    /**
     * Return a redirect from the file
     *
     * @param string $source_url
     *
     * @return array|false|null
     */
    public function getBySource($source_url)
    {
        if (!$this->sourceExists($source_url)) {
            return false;
        }

        return $this->redirects->first(function ($redirect) use ($source_url) {
            $redirect_source_url = str_replace('/', '\/', $redirect['source_url']);
            return preg_match("/^{$redirect_source_url}/i", $source_url) === 1 && $source_url !== $redirect['target_url'];
        });
    }

    /**
     * Return all redirects from the file
     *
     * @param bool $returnCollection
     */
    public function all($returnCollection = false)
    {
        if ($returnCollection) {
            return collect($this->redirects);
        }
        return $this->redirects;
    }

    /**
     * Update the repository (either by adding a redirect or modifying an existing one)
     *
     * @param array $data
     */
    public function update(array $data, $redirect_id = null)
    {
        $data = $this->processRaw($data);

        // If the source doesn't already exist add an ID
        if (!$redirect_id) {
            if (!$this->sourceExists($data['source_url'])) {
                $redirect_id = Str::uuid()->toString();
                $data['id'] = $redirect_id;
            } else {
                $redirect_id = $this->getBySource($data['source_url'])['id'];
            }
        }

        if ($redirect_id && $this->exists($redirect_id)) {
            $data['id'] = $redirect_id;
            $this->redirects = $this->redirects->map(function ($redirect) use ($data, $redirect_id) {
                if ($redirect['id'] === $redirect_id) {
                    $redirect = $data;
                }
                return $redirect;
            });
        } else {
            $this->redirects->push($data);
        }

        $this->writeToFile();
    }

    /**
     * Delete a redirect
     *
     * @param string $redirect_id
     */
    public function delete($redirect_id)
    {
        $exists = $this->exists($redirect_id);
        if ($exists) {
            $this->redirects = $this->redirects->reject(function ($redirect) use ($redirect_id) {
                return $redirect['id'] === $redirect_id;
            })->values();

            $this->writeToFile();
        }
    }

    /**
     * Process the raw data coming in
     *
     * @param array $raw
     */
    private function processRaw($data)
    {
        // Ensure source is a relative url
        $data['source_url'] = Str::ensureLeft(URL::makeRelative($data['source_url']), '/');

        return $data;
    }

    /**
     * Get the redirects listed in this storage file
     */
    public function getRedirectsFromFile()
    {
        $redirects = AardvarkStorage::getYaml($this->storage_key, $this->site, true);
        $this->redirects = $redirects;
    }

    /**
     * Set the data
     */
    private function writeToFile()
    {
        AardvarkStorage::putYaml($this->storage_key, $this->site, $this->redirects->toArray());
    }
}
