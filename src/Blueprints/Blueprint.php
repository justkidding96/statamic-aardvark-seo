<?php

namespace Justkidding96\AardvarkSeo\Blueprints;

interface Blueprint
{
    /**
     * Return an instance of a blueprint, populated with fields
     *
     * @return Statamic\Fields\Blueprint
     */
    public static function requestBlueprint();
}
