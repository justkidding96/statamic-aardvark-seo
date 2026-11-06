<?php

namespace Justkidding96\AardvarkSeo\Facades;

use Justkidding96\AardvarkSeo\Storage\GlobalsStorage;
use Illuminate\Support\Facades\Facade;

class AardvarkStorage extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return GlobalsStorage::class;
    }
}
