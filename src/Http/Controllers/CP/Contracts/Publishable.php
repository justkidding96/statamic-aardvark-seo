<?php

namespace Justkidding96\AardvarkSeo\Http\Controllers\CP\Contracts;

interface Publishable
{
    /**
     * Return the view for the publish form
     */
    public function index();

    /**
     * Store the data from the publish form
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(\Illuminate\Http\Request $request);

    /**
     * Return the Statamic blueprint which this form uses
     *
     * @return Statamic\Fields\Blueprint
     */
    public function getBlueprint();

    /**
     *  Return the data from this page's storage file
     *
     * @return array
     */
    public function getData();

    /**
     * Store the data using our Storage API
     *
     * @param array $data
     */
    public function putData(array $data);
}
