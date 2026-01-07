<?php

namespace Justkidding96\AardvarkSeo\Facades;

use Justkidding96\AardvarkSeo\Content\ContentDefaultsGetter;
use Illuminate\Support\Facades\Facade;

class ContentDefaults extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return ContentDefaultsGetter::class;
    }
}
