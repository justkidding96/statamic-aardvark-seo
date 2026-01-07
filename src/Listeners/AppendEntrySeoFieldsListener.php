<?php

namespace Justkidding96\AardvarkSeo\Listeners;

use Statamic\Events\EntryBlueprintFound;
use Statamic\Support\Str;
use Justkidding96\AardvarkSeo\Blueprints\CP\OnPageSeoBlueprint;
use Justkidding96\AardvarkSeo\Listeners\Contracts\SeoFieldsListener;

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
            $fields = OnPageSeoBlueprint::requestBlueprint()
                ->fields()
                ->items()
                ->mapWithKeys(fn (array $field) => [$field['handle'] => $field['field']]);

            $event->blueprint->ensureFieldsInTab(
                $fields,
                'SEO'
            );
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
