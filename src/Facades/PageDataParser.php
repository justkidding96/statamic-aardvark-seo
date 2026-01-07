<?php

namespace Justkidding96\AardvarkSeo\Facades;

use Justkidding96\AardvarkSeo\Parsers\PageDataParser as Parser;
use Illuminate\Support\Facades\Facade;

class PageDataParser extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return Parser::class;
    }
}
