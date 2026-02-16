<?php

namespace Justkidding96\AardvarkSeo\Schema\Parts;

use Spatie\SchemaOrg\Schema;
use Statamic\Facades\Config;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Facades\Term;
use Statamic\Facades\URL;
use Statamic\Support\Str;
use Justkidding96\AardvarkSeo\Schema\SchemaIds;
use Justkidding96\AardvarkSeo\Schema\Parts\Contracts\SchemaPart;

class Breadcrumbs implements SchemaPart
{
    /**
     * Similar to how NavTags->breadcrumbs works
     */
    public function list()
    {
        $crumbs = [];

        $url = URL::makeAbsolute(URL::getCurrent());
        $url = Str::removeLeft($url, Site::current()->absoluteUrl());
        $url = Str::ensureLeft($url, '/');

        $segments = explode('/', $url);
        $segments[0] = '/';

        // Create crumbs from segments
        $crumbs = collect($segments)->map(function () use (&$segments) {
            $uri = URL::tidy(join('/', $segments));
            array_pop($segments);

            return $uri;
        })->mapWithKeys(function ($uri) {
            $entry = Entry::findByUri($uri, Site::current()->handle());
            if ($entry) {
                return [$uri => $entry];
            }
            $term = Term::findByUri($uri, Site::current()->handle());
            if ($term) {
                return [$uri => $term];
            }

            return [$uri => null];
        })->filter();

        return $crumbs->reverse();
    }

    public function data()
    {
        $breadcrumbs = Schema::breadcrumbList();
        $crumbs = $this->list();

        $position = 1;
        $listItems = [];
        foreach ($crumbs as $crumb) {
            $listItem = Schema::listItem();
            $listItem->position($position);
            $item = Schema::thing();

            // If we've got a title add it, else see of we've got an origin and add the title from there
            if ($title = $crumb->get('title')) {
                $item->name($title);
            } else {
                if ($crumb->origin()) {
                    $item->name($crumb->origin()->get('title'));
                }
            }

            $item->setProperty('id', $crumb->absoluteUrl());
            $listItem->item($item);
            $listItems[] = $listItem;
            $position++;
        }

        $breadcrumbs->itemListElement($listItems);
        return $breadcrumbs;
    }

    public static function id()
    {
        return URL::makeAbsolute(Config::getSiteUrl()) . SchemaIds::BREADCRUMBS;
    }
}
