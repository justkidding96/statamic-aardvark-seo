<?php

namespace Justkidding96\AardvarkSeo\Content;

use Statamic\Facades\Site;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;

class ContentDefaults
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var \Statamic\Sites\Site
     */
    public $site;

    /**
     * @return void
     */
    public function __construct(string $type, string $handle, \Statamic\Sites\Site $site)
    {
        $this->type = $type;
        $this->handle = $handle;
        $this->site = $site ?: Site::current();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function data()
    {
        return AardvarkStorage::getYaml("defaults/{$this->type}_{$this->handle}", $this->site, true);
    }
}
