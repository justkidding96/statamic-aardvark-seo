<?php

namespace WithCandour\AardvarkSeo\Facades;

use Illuminate\Support\Facades\Config;
use WithCandour\AardvarkSeo\Storage\GlobalsStorage;
use Illuminate\Support\Facades\Facade;
use WithCandour\AardvarkSeo\Storage\DatabaseStorage;
use WithCandour\AardvarkSeo\Storage\FileStorage;

class AardvarkStorage extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        $driver = Config::get('aardvark-seo.storage_driver', 'file');

        switch ($driver) {
            case 'database':
                return DatabaseStorage::class;
            default:
                return FileStorage::class;
        }
    }
}
