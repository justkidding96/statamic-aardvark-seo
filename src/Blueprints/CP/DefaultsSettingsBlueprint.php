<?php

namespace Justkidding96\AardvarkSeo\Blueprints\CP;

use Justkidding96\AardvarkSeo\Blueprints\Blueprint as AardvarkBlueprint;
use Statamic\Facades\Blueprint as StatamicBlueprint;

class DefaultsSettingsBlueprint implements AardvarkBlueprint
{
    /**
     * @inheritDoc
     */
    public static function requestBlueprint()
    {
        return StatamicBlueprint::make()->setContents([
            'tabs' => [
                'main' => [
                    'sections' => [
                        [
                            'display' => __('aardvark-seo::onpage.fields.meta_section.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.meta_section.instruct'),
                            'fields' => [
                                [
                                    'handle' => 'meta_title',
                                    'field' => [
                                        'type' => 'aardvark_seo_meta_title',
                                        'display' => __('aardvark-seo::onpage.fields.meta_title.display'),
                                        'localizable' => true,
                                        'antlers' => true,
                                    ],
                                ],
                                [
                                    'handle' => 'meta_description',
                                    'field' => [
                                        'type' => 'aardvark_seo_meta_description',
                                        'display' => __('aardvark-seo::onpage.fields.meta_description.display'),
                                        'localizable' => true,
                                        'antlers' => true,
                                    ],
                                ],
                                [
                                    'handle' => 'use_meta_keywords',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => __('aardvark-seo::onpage.fields.use_meta_keywords.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.use_meta_keywords.instruct'),
                                        'localizable' => true,
                                        'width' => 50,
                                    ],
                                ],
                                [
                                    'handle' => 'meta_keywords',
                                    'field' => [
                                        'type' => 'taggable',
                                        'display' => __('aardvark-seo::onpage.fields.meta_keywords.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.meta_keywords.instruct'),
                                        'localizable' => true,
                                        'width' => 50,
                                        'if' => [
                                            'use_meta_keywords' => 'equals true',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'display' => __('aardvark-seo::onpage.fields.indexing_section.display'),
                            'fields' => [
                                [
                                    'handle' => 'no_index_page',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => __('aardvark-seo::onpage.fields.no_index_page.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.no_index_page.instruct'),
                                        'width' => 50,
                                        'localizable' => true,
                                    ],
                                ],
                                [
                                    'handle' => 'no_follow_links',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => __('aardvark-seo::onpage.fields.no_follow_links.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.no_follow_links.instruct'),
                                        'width' => 50,
                                        'localizable' => true,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'display' => __('aardvark-seo::onpage.fields.share_section_og.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.share_section_og.instruct'),
                            'fields' => [
                                [
                                    'handle' => 'og_title',
                                    'field' => [
                                        'type' => 'text',
                                        'display' => __('aardvark-seo::onpage.fields.og_title.display'),
                                        'localizable' => true,
                                    ],
                                ],
                                [
                                    'handle' => 'og_description',
                                    'field' => [
                                        'type' => 'textarea',
                                        'display' => __('aardvark-seo::onpage.fields.og_description.display'),
                                        'localizable' => true,
                                        'antlers' => true,
                                    ],
                                ],
                                [
                                    'handle' => 'og_image',
                                    'field' => [
                                        'type' => 'assets',
                                        'display' => __('aardvark-seo::onpage.fields.og_image.display'),
                                        'max_files' => 1,
                                        'restrict' => false,
                                        'container' => config('aardvark-seo.asset_container'),
                                        'folder' => config('aardvark-seo.asset_folder'),
                                        'localizable' => true,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'display' => __('aardvark-seo::onpage.fields.share_section_twitter.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.share_section_twitter.instruct'),
                            'fields' => [
                                [
                                    'handle' => 'override_twitter_settings',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => 'Override the twitter share settings for this content',
                                        'localizable' => true,
                                    ],
                                ],
                                [
                                    'handle' => 'twitter_title',
                                    'field' => [
                                        'type' => 'text',
                                        'display' => __('aardvark-seo::onpage.fields.twitter_title.display'),
                                        'localizable' => true,
                                        'if' => [
                                            'override_twitter_settings' => 'equals true',
                                        ],
                                    ],
                                ],
                                [
                                    'handle' => 'twitter_description',
                                    'field' => [
                                        'type' => 'textarea',
                                        'display' => __('aardvark-seo::onpage.fields.twitter_description.display'),
                                        'antlers' => true,
                                        'if' => [
                                            'override_twitter_settings' => 'equals true',
                                        ],
                                    ],
                                ],
                                [
                                    'handle' => 'override_twitter_card_settings',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => 'Override the twitter card settings for this page',
                                        'localizable' => true,
                                        'if' => [
                                            'override_twitter_settings' => 'equals true',
                                        ],
                                    ],
                                ],
                                [
                                    'handle' => 'twitter_card_type_page',
                                    'field' => [
                                        'type' => 'select',
                                        'display' => __('aardvark-seo::onpage.fields.twitter_card_type_page.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.twitter_card_type_page.instruct'),
                                        'localizable' => true,
                                        'default' => 'summary',
                                        'width' => 50,
                                        'options' => [
                                            'summary' => 'Summary Card',
                                            'summary_large_image' => 'Summary Card with Large Image',
                                        ],
                                        'if' => [
                                            'override_twitter_settings' => 'equals true',
                                            'override_twitter_card_settings' => 'equals true',
                                        ],
                                    ],
                                ],
                                [
                                    'handle' => 'twitter_summary_image',
                                    'field' => [
                                        'type' => 'assets',
                                        'display' => __('aardvark-seo::onpage.fields.twitter_summary_image.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.twitter_summary_image.instruct'),
                                        'max_files' => 1,
                                        'restrict' => false,
                                        'container' => config('aardvark-seo.asset_container'),
                                        'folder' => config('aardvark-seo.asset_folder'),
                                        'localizable' => true,
                                        'if' => [
                                            'override_twitter_settings' => 'equals true',
                                            'override_twitter_card_settings' => 'equals true',
                                            'twitter_card_type_page' => 'equals summary',
                                        ],
                                    ],
                                ],
                                [
                                    'handle' => 'twitter_summary_large_image',
                                    'field' => [
                                        'type' => 'assets',
                                        'display' => __('aardvark-seo::onpage.fields.twitter_summary_large_image.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.twitter_summary_large_image.instruct'),
                                        'max_files' => 1,
                                        'restrict' => false,
                                        'container' => config('aardvark-seo.asset_container'),
                                        'folder' => config('aardvark-seo.asset_folder'),
                                        'localizable' => true,
                                        'if' => [
                                            'override_twitter_settings' => 'equals true',
                                            'override_twitter_card_settings' => 'equals true',
                                            'twitter_card_type_page' => 'equals summary_large_image',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'display' => __('aardvark-seo::onpage.fields.scripts_section.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.scripts_section.instruct'),
                            'fields' => [
                                [
                                    'handle' => 'head_snippets',
                                    'field' => [
                                        'type' => 'code',
                                        'display' => __('aardvark-seo::onpage.fields.head_snippets.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.head_snippets.instruct'),
                                        'mode' => 'htmlmixed',
                                        'localizable' => true,
                                    ],
                                ],
                                [
                                    'handle' => 'footer_snippets',
                                    'field' => [
                                        'type' => 'code',
                                        'display' => __('aardvark-seo::onpage.fields.footer_snippets.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.footer_snippets.instruct'),
                                        'mode' => 'htmlmixed',
                                        'localizable' => true,
                                    ],
                                ],
                                [
                                    'handle' => 'schema_objects',
                                    'field' => [
                                        'type' => 'code',
                                        'display' => __('aardvark-seo::onpage.fields.schema_objects.display'),
                                        'instructions' => __('aardvark-seo::onpage.fields.schema_objects.instruct'),
                                        'mode' => 'htmlmixed',
                                        'localizable' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
