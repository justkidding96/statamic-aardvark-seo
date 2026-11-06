<?php

namespace Justkidding96\AardvarkSeo\Modifiers;

use Statamic\Modifiers\Modifier;
use WhiteCube\Lingua\Service as Lingua;

class ParseLocaleModifier extends Modifier
{
    protected static $handle = 'aardvark_parse_locale';

    public static function index($value)
    {
        if($value === 'x-default') {
            return $value;
        }
        
        $parsed = preg_replace('/\.utf8/i', '', $value);

        // Convert to W3C
        $lang = Lingua::create($parsed);
        $code = $lang->toW3C();

        return $code;
    }
}
