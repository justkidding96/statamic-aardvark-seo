<?php

namespace Justkidding96\AardvarkSeo\Tags;

use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;
use Statamic\View\View;
use Justkidding96\AardvarkSeo\Schema\SchemaGraph;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;
use Justkidding96\AardvarkSeo\Facades\PageDataParser;

class AardvarkSeoTags extends Tags
{
    protected static $handle = 'aardvark-seo';

    // The $ctx property is used to store the context as a collection.
    // It's initialised and set on the first method call to avoid subsequent calls.
    protected $ctx = null;

    // The $ctxParsed property holds the processed data obtained from the PageDataParser.
    // It's initialised and set on the first method call to avoid subsequent calls.
    protected $ctxParsed = null;

    /**
     * Return the <head /> tag content required for on-page SEO
     *
     * @return string
     */
    public function head()
    {

        // Check if $this->ctx is not set. If not, initialise it with the current context.
        if (!$this->ctx) {
            $this->ctx = $this->context;
        }

        // Check if $this->ctxParsed is not set. If not, parse $this->ctx using PageDataParser
        // and store the result in $this->ctxParsed for reuse.
        if (!$this->ctxParsed) {
            $this->ctxParsed = PageDataParser::getData($this->ctx);
        }

        $data = $this->ctxParsed;

        // Check the version of Statamic Antlers. If it's 'regex', use the Laravel view helper,
        // else use the Statamic View facade. Also, ensure $data is an array when passing it to the view.
        if (config('statamic.antlers.version') == 'regex') {
            $view = $data instanceof \Illuminate\Support\Collection ?
                view('aardvark-seo::tags.head', $data->all()) :
                view('aardvark-seo::tags.head', $data);
        } else {
            $view = View::make('aardvark-seo::tags.head', $data->all());
        }

        if ($this->params->get('debug')) {
            return $view;
        }

        return preg_replace(
            [
                "/<!--(.|\s)*?-->/",
                "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/",
            ],
            [
                '',
                "\n",
            ],
            $view
        );
    }

    /**
     * Return the body content
     */
    public function body()
    {

        // Check if $this->ctx is not set. If not, initialise it with the current context.
        if (!$this->ctx) {
            $this->ctx = collect($this->context);
        }

        // Check if $this->ctxParsed is not set. If not, parse $this->ctx using PageDataParser
        // and store the result in $this->ctxParsed for reuse.
        if (!$this->ctxParsed) {
            $this->ctxParsed = PageDataParser::getData($this->ctx);
        }

        $data = $this->ctxParsed;

        // Check the version of Statamic Antlers. If it's 'regex', use the Laravel view helper,
        // else use the Statamic View facade. Also, ensure $data is an array when passing it to the view.
        if (config('statamic.antlers.version') == 'regex') {
            $view = $data instanceof \Illuminate\Support\Collection ?
                view('aardvark-seo::tags.body', $data->all()) :
                view('aardvark-seo::tags.body', $data);
        } else {
            $view = View::make('aardvark-seo::tags.body', $data->all());
        }

        return $view;
    }

    /**
     * Return the footer content
     */
    public function footer()
    {

        // Check if $this->ctx is not set. If not, initialise it with the current context.
        if (!$this->ctx) {
            $this->ctx = collect($this->context);
        }

        // Check if $this->ctxParsed is not set. If not, parse $this->ctx using PageDataParser
        // and store the result in $this->ctxParsed for reuse.
        if (!$this->ctxParsed) {
            $this->ctxParsed = PageDataParser::getData($this->ctx);
        }

        $data = $this->ctxParsed;

        // Check the version of Statamic Antlers. If it's 'regex', use the Laravel view helper,
        // else use the Statamic View facade. Also, ensure $data is an array when passing it to the view.
        if (config('statamic.antlers.version') == 'regex') {
            $view = $data instanceof \Illuminate\Support\Collection ?
                view('aardvark-seo::tags.footer', $data->all()) :
                view('aardvark-seo::tags.footer', $data);
        } else {
            $view = View::make('aardvark-seo::tags.footer', $data->all());
        }

        return $view;
    }

    /**
     * Return the data for hreflang tags
     */
    public function hreflang()
    {

        // Check if $this->ctx is not set. If not, initialise it with the current context.
        if (!$this->ctx) {
            $this->ctx = collect($this->context);
        }

        $ctx = $this->ctx;

        $id = $ctx->get('id');

        if ($id instanceof \Statamic\Fields\Value) {
            $id = $id->value();
        }

        if (empty($id)) {
            return null;
        }

        $data = Entry::find($id);

        if (!$data) {
            return null;
        }


        $defaultLocale = $ctx->get('aardvark_general_settings')['default_locale'];

        if ($defaultLocale instanceof \Statamic\Fields\Value) {
            $defaultLocale = $defaultLocale->value();
        }

        $sites_by_handle = Site::all()->reduce(function($sites, $site) {
            $sites[$site->handle()] = $site;
            return $sites;
        }, []);

        $alternates = $data->sites()->map(function ($handle) use ($data, $sites_by_handle, $defaultLocale) {
            $localized_data = $data->in($handle);

            if(!empty($localized_data) && $localized_data->published()) {
                $site = $sites_by_handle[$handle];
                return [
                    'url' => $localized_data->absoluteUrl(),
                    'locale' => $site->locale(),
                    'default' => !empty($defaultLocale) ? $site->handle === $defaultLocale->handle : false,
                ];
            }

            return null;
        }, [])->filter();

        if (!empty($alternates)) {
            return view('aardvark-seo::tags.hreflang', [
                'hreflang_tags' => $alternates,
            ]);
        }
    }

    /**
     * Return the schema graph object
     *
     * @return string
     */
    public function graph()
    {

        // Check if $this->ctx is not set. If not, initialise it with the current context.
        if (!$this->ctx) {
            $this->ctx = collect($this->context);
        }

        $ctx = $this->ctx;
        $graph = new SchemaGraph($ctx);
        return $graph->build();
    }

    /**
     * Return the list of social icons created in the 'Social' menu
     *
     * @return string
     */
    public function socials()
    {

        // Check if $this->ctx is not set. If not, initialise it with the current context.
        if (!$this->ctx) {
            $this->ctx = collect($this->context);
        }

        // Check if $this->ctxParsed is not set. If not, parse $this->ctx using PageDataParser
        // and store the result in $this->ctxParsed for reuse.
        if (!$this->ctxParsed) {
            $this->ctxParsed = PageDataParser::getData($this->ctx);
        }

        $data = $this->ctxParsed;
        $socials = $data->get('aardvark_social_settings')->get('social_links');
        if ($socials->raw()) {
            return $this->parseLoop($socials->raw());
        }
        return $this->parseLoop([]);
    }

    /**
     * Return the robots tag content
     * (done here to prevent a bunch of ifs and butts in the template file)
     *
     * @return string
     */
    public function robotsTag()
    {

        // Check if $this->ctx is not set. If not, initialise it with the current context.
        if (!$this->ctx) {
            $this->ctx = collect($this->context);
        }

        $ctx = $this->ctx;
        $attrs = [];

        $global_no_index = $ctx->get('aardvark_general_settings')['no_index_site'];

        if ($ctx->get('no_index_page') || $global_no_index->raw()) {
            array_push($attrs, 'noindex');
        }

        if ($ctx->get('no_follow_links')) {
            array_push($attrs, 'nofollow');
        }

        if (!empty($attrs)) {
            $attrs_string = implode(', ', $attrs);
            return "<meta name=\"robots\" content=\"{$attrs_string}\">";
        }

        return false;
    }

    /**
     * Return a generated canonical URL - this should contain pagination vars
     * if any are set
     *
     * @return string
     */
    public function generatedCanonical()
    {

        // Check if $this->ctx is not set. If not, initialise it with the current context.
        if (!$this->ctx) {
            $this->ctx = collect($this->context);
        }

        $data = $this->ctx;
        $vars = $data->get('get');
        $current_url = $data->get('permalink');
        if ($vars && $page = collect($vars)->get('page')) {
            $current_url .= '?page=' . urlencode($page);
        }
        return $current_url;
    }
}
