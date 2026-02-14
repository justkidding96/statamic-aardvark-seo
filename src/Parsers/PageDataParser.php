<?php

namespace Justkidding96\AardvarkSeo\Parsers;

use Statamic\Facades\Collection;
use Statamic\Facades\Parse;
use Statamic\Facades\Site;
use Justkidding96\AardvarkSeo\Blueprints\CP\DefaultsSettingsBlueprint;
use Justkidding96\AardvarkSeo\Blueprints\CP\GeneralSettingsBlueprint;
use Justkidding96\AardvarkSeo\Blueprints\CP\MarketingSettingsBlueprint;
use Justkidding96\AardvarkSeo\Blueprints\CP\SocialSettingsBlueprint;
use Justkidding96\AardvarkSeo\Blueprints\CP\OnPageSeoBlueprint;
use Justkidding96\AardvarkSeo\Facades\AardvarkStorage;

/**
 * Helper class for parsing on-page data
 */
class PageDataParser
{
    /**
     * Return the aardvark data we'll be using on the page
     *
     * @param Illumniate\Support\Collection $ctx
     *
     * @return array
     */
    public static function getData($ctx)
    {
        $defaults = self::getDefaults($ctx);

        $bp = OnPageSeoBlueprint::requestBlueprint();
        $fields_to_map = $bp->fields()->items()->mapWithKeys(function ($field) {
            return [$field['handle'] => [
                    'handle' => $field['handle'],
                    'type' => $field['field']['type'],
                ],
            ];
        });

        $data = $ctx->map(function ($value, $field) use ($defaults, $fields_to_map) {
            if ($bp_field = $fields_to_map->get($field)) {
                switch ($bp_field['type']) {
                    case 'toggle':
                            $default_value = $defaults->get($field) && $defaults->get($field)->raw();
                            $page_value = $value && $value->raw();
                            return $default_value || $page_value;
                        return $defaults;
                    default:
                        return $value && $value->raw() ? $value : $defaults->get($field);
                }
            }
            return $value;
        });

        $data = self::populateAdditionalData($data, $ctx);

        return $data;
    }

    /**
     * Return the current site's defaults, if we're working with
     * a multisite setup then merge the default site defaults
     * into the localized ones. No variables left behind!
     *
     * @param Illumniate\Support\Collection $ctx
     *
     * @return Illuminate\Support\Collection
     */
    public static function getDefaults($ctx)
    {
        $is_term = $ctx->get('is_term');

        if ($is_term instanceof \Statamic\Fields\Value) {
            $is_term = $is_term->value();
        }

        $type = $is_term ? 'taxonomies' : 'collections';

        switch ($type) {
            case 'taxonomies':
                $repo = $ctx->get('taxonomy');
                if ($repo instanceof \Statamic\Fields\Value) {
                    $repo = $repo->value();
                }
                break;
            case 'collections':
                $repo = $ctx->get('collection');
                if ($repo instanceof \Statamic\Fields\Value) {
                    $repo = $repo->value();
                }
                break;
            default:
                $repo = null;
        }

        /**
         * Exception routes and taxonomy indexes - and anything else which
         * doesn't 'belong' to a collection
         */
        if (!$repo) {
            $repo = Collection::findByHandle('pages');
        }

        $storage_path = "defaults/{$type}_{$repo->handle()}";
        $defaults = self::getSettingsBlueprintWithValues($ctx, $storage_path, new DefaultsSettingsBlueprint());

        return $defaults;
    }

    /**
     * Populate the page data with some generated fields
     *
     * @param Illuminate\Support\Collection $data
     * @param Illuminate\Support\Collection $ctx
     *
     * @return Illuminate\Support\Collection
     */
    private static function populateAdditionalData($data, $ctx)
    {
        $populated = [
            'calculated_title' => self::generatePageTitle($data, $ctx),
            'calculated_og_image' => self::getCalculatedOgImage($data, $ctx),
            'calculated_twitter_card_type' => self::getCalculatedTwitterCardType($data, $ctx),
            'calculated_twitter_image' => self::getCalculatedTwitterImage($data, $ctx),
            'aardvark_general_settings' => self::getSettingsBlueprintWithValues($ctx, 'general', new GeneralSettingsBlueprint()),
            'aardvark_marketing_settings' => self::getSettingsBlueprintWithValues($ctx, 'marketing', new MarketingSettingsBlueprint()),
            'aardvark_social_settings' => self::getSettingsBlueprintWithValues($ctx, 'social', new SocialSettingsBlueprint()),
            'disable_favicons' => config('aardvark-seo.disable_favicons', false),
            'disable_default_schema' => config('aardvark-seo.disable_default_schema', false),
        ];

        return $data->merge($populated);
    }

    /**
     *  Return a correctly formatted set of data from addon storage
     * -- This uses the original blueprint so that we can augment the values
     * -- getting passed to the template (convert them to Statamic\Fields\Value)
     *
     * @param Illuminate\Support\Collection $ctx
     * @param string $type
     * @param mixed $blueprint_class A blank blueprint facade-y sort of thing
     *
     * @return Illuminate\Support\Collection
     */
    public static function getSettingsBlueprintWithValues($ctx, $type, $blueprint_class)
    {
        $settings = AardvarkStorage::getYaml($type, Site::current());
        $blueprint = $blueprint_class::requestBlueprint();
        return $blueprint->fields()->addValues($settings)->augment()->values();
    }

    /**
     * Return a meta title for our page
     *
     * @param Illuminate\Support\Collection $data
     * @param Illuminate\Support\Collection $ctx
     *
     * @return mixed
     */
    public static function generatePageTitle($data, $ctx)
    {
        if ($data->get('meta_title') && $data->get('meta_title')->raw()) {
            return Parse::template($data->get('meta_title'), $ctx);
        }

        if ($data->get('response_code') === 404) {
            $data->put('title', '404');
        }

        $storage = self::getSettingsBlueprintWithValues($ctx, 'general', new GeneralSettingsBlueprint());

        $titleOrder = $storage->get('title_order');
        $titleOrderValue = $titleOrder instanceof \Statamic\Fields\Value ? $titleOrder->raw() : $titleOrder;

        if ($titleOrderValue === 'site_first') {
            return implode(' ', [
                $storage->get('site_name'),
                $storage->get('title_separator'),
                $data->get('title'),
            ]);
        }

        return implode(' ', [
            $data->get('title'),
            $storage->get('title_separator'),
            $storage->get('site_name'),
        ]);
    }

    /**
     * Return an image field for use as the OG image on the page
     *
     * @param Illuminate\Support\Collection $data
     * @param Illuminate\Support\Collection $ctx
     *
     * @return Statamic\Fields\Value
     */
    private static function getCalculatedOgImage($data, $ctx)
    {
        $storage = self::getSettingsBlueprintWithValues($ctx, 'social', new SocialSettingsBlueprint());
        return $data->get('og_image') && $data->get('og_image')->raw() ? $data->get('og_image') : $storage->get('og_image_site');
    }

    /**
     * Return a calculated twitter card type
     *
     * @param Illuminate\Support\Collection $data
     * @param Illuminate\Support\Collection $ctx
     *
     * @return Statamic\Fields\Value
     */
    private static function getCalculatedTwitterCardType($data, $ctx)
    {
        $override = $data->get('override_twitter_settings') && $data->get('override_twitter_card_settings');
        if ($override) {
            if ($data->get('twitter_card_type_page')) {
                return $data->get('twitter_card_type_page');
            }
        }

        $storage = self::getSettingsBlueprintWithValues($ctx, 'social', new SocialSettingsBlueprint());
        return $storage->get('twitter_card_type_site');
    }

    /**
     * Return a calculated twitter share image
     */
    private static function getCalculatedTwitterImage($data, $ctx)
    {
        $override = $data->get('override_twitter_settings') && $data->get('override_twitter_card_settings');
        $type = self::getCalculatedTwitterCardType($data, $ctx)->raw();
        $field = $type === 'summary_large_image' ? 'twitter_summary_large_image' : 'twitter_summary_image';

        if ($override) {
            $page_value = $data->get($field) && $data->get($field)->raw() ? $data->get($field) : null;
            if ($page_value) {
                return $page_value;
            }
        }

        $storage = self::getSettingsBlueprintWithValues($ctx, 'social', new SocialSettingsBlueprint());
        return $storage->get("{$field}_site");
    }
}
