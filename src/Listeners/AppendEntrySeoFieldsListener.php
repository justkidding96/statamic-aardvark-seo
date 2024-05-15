<?php

namespace WithCandour\AardvarkSeo\Listeners;

use Statamic\Events\EntryBlueprintFound;
use Statamic\Support\Str;
use WithCandour\AardvarkSeo\Blueprints\CP\OnPageSeoBlueprint;
use WithCandour\AardvarkSeo\Listeners\Contracts\SeoFieldsListener;

class AppendEntrySeoFieldsListener implements SeoFieldsListener
{
    /**
     * @param \Statamic\Events\EntryBlueprintFound $event
     *
     * @return void
     */
    public function handle(EntryBlueprintFound $event)
    {
        // We don't want the SEO fields to get added to the blueprint editor
        if (Str::contains(request()->url(), '/blueprints/')) {
            return;
        }

        $handle = $event->blueprint->namespace();
        if ($this->check_content_type($handle)) {
            $bp = $event->blueprint;
            $contents = $bp->contents();

            $on_page_bp = OnPageSeoBlueprint::requestBlueprint();
            $contents['tabs']['SEO'] = $on_page_bp->contents()['tabs']['main'];

            // Setting the parent to null fixes several issues caused by https://github.com/statamic/cms/pull/9585
            $bp->setParent(null);
            $bp->setContents($contents);
        }
    }

    public function check_content_type($blueprint_namespace)
    {
        $ns_parts = explode('.', $blueprint_namespace);
        $collection_handle = !empty($ns_parts[1]) ? $ns_parts[1] : null;
        $excluded_collections = config('aardvark-seo.excluded_collections', []);
        if (\in_array($collection_handle, $excluded_collections)) {
            return false;
        }
        return true;
    }
}
