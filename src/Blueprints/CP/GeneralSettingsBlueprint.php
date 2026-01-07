<?php

namespace Justkidding96\AardvarkSeo\Blueprints\CP;

use Justkidding96\AardvarkSeo\Blueprints\Blueprint as AardvarkBlueprint;
use Statamic\Facades\Blueprint as StatamicBlueprint;

class GeneralSettingsBlueprint implements AardvarkBlueprint
{
    /**
     * @inheritDoc
     */
    public static function requestBlueprint()
    {
        return StatamicBlueprint::make()->setContents([
            'sections' => [
                'main' => [
                    'fields' => [
                        [
                            'handle' => 'titles_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::general.fields.titles_section.display'),
                                'instructions' => __('aardvark-seo::general.fields.titles_section.instruct'),
                                'listable' => 'hidden',
                            ],
                        ],
                        [
                            'handle' => 'title_separator',
                            'field' => [
                                'type' => 'select',
                                'display' => __('aardvark-seo::general.fields.title_separator.display'),
                                'instructions' => __('aardvark-seo::general.fields.title_separator.instruct'),
                                'default' => '|',
                                'options' => [
                                    '|',
                                    '-',
                                    '~',
                                    '•',
                                    '/',
                                    '//',
                                    '»',
                                    '«',
                                    '>',
                                    '<',
                                    '*',
                                    '+',
                                ],
                                'width' => 33,
                            ],
                        ],
                        [
                            'handle' => 'site_name',
                            'field' => [
                                'type' => 'text',
                                'display' => __('aardvark-seo::general.fields.site_name.display'),
                                'instructions' => __('aardvark-seo::general.fields.site_name.instruct'),
                                'width' => 66,
                            ],
                        ],
                        [
                            'handle' => 'favicon_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::general.fields.favicon_section.display'),
                                'instructions' => __('aardvark-seo::general.fields.favicon_section.instruct'),
                                'listable' => 'hidden',
                            ],
                        ],
                        [
                            'handle' => 'global_favicon',
                            'field' => [
                                'type' => 'assets',
                                'display' => __('aardvark-seo::general.fields.global_favicon.display'),
                                'max_files' => 1,
                                'restrict' => false,
                                'container' => config('aardvark-seo.asset_container'),
                                'folder' => config('aardvark-seo.asset_folder'),
                            ],
                        ],
                        [
                            'handle' => 'knowledge_graph_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::general.fields.knowledge_graph_section.display'),
                                'listable' => 'hidden',
                            ],
                        ],
                        [
                            'handle' => 'company_or_person',
                            'field' => [
                                'type' => 'radio',
                                'display' => __('aardvark-seo::general.fields.company_or_person.display'),
                                'instructions' => __('aardvark-seo::general.fields.company_or_person.instruct'),
                                'default' => 'company',
                                'inline' => true,
                                'options' => [
                                    'company' => 'Company',
                                    'person' => 'Person',
                                ],
                            ],
                        ],
                        [
                            'handle' => 'target_name',
                            'field' => [
                                'type' => 'text',
                                'display' => __('aardvark-seo::general.fields.target_name.display'),
                                'instructions' => __('aardvark-seo::general.fields.target_name.instruct'),
                                'width' => 50,
                            ],
                        ],
                        [
                            'handle' => 'company_logo',
                            'field' => [
                                'type' => 'assets',
                                'max_files' => 1,
                                'restrict' => false,
                                'width' => 50,
                                'display' => __('aardvark-seo::general.fields.company_logo.display'),
                                'container' => config('aardvark-seo.asset_container'),
                                'folder' => config('aardvark-seo.asset_folder'),
                                'if' => [
                                    'company_or_person' => 'equals company',
                                ],
                            ],
                        ],
                        [
                            'handle' => 'breadcrumbs_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::general.fields.breadcrumbs_section.display'),
                                'instructions' => __('aardvark-seo::general.fields.breadcrumbs_section.instruct'),
                                'listable' => 'hidden',
                            ],
                        ],
                        [
                            'handle' => 'enable_breadcrumbs',
                            'field' => [
                                'type' => 'toggle',
                                'display' => __('aardvark-seo::general.fields.enable_breadcrumbs.display'),
                                'default' => true,
                            ],
                        ],
                        [
                            'handle' => 'no_index_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::general.fields.no_index_section.display'),
                                'instructions' => __('aardvark-seo::general.fields.no_index_section.instruct'),
                            ],
                        ],
                        [
                            'handle' => 'no_index_site',
                            'field' => [
                                'type' => 'toggle',
                                'display' => __('aardvark-seo::general.fields.no_index_site.display'),
                                'instructions' => __('aardvark-seo::general.fields.no_index_site.instruct'),
                            ],
                        ],
                        [
                            'handle' => 'default_locale_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::general.fields.default_locale_section.display'),
                                'instructions' => __('aardvark-seo::general.fields.default_locale_section.instruct'),
                            ],
                        ],
                        [
                            'handle' => 'default_locale',
                            'field' => [
                                'type' => 'sites',
                                'max_items' => 1,
                                'display' => __('aardvark-seo::general.fields.default_locale.display'),
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
