<?php

namespace Justkidding96\AardvarkSeo\Blueprints\CP;

use Justkidding96\AardvarkSeo\Blueprints\Blueprint as AardvarkBlueprint;
use Statamic\Facades\Blueprint as StatamicBlueprint;

class OnPageSeoBlueprint implements AardvarkBlueprint
{
    /**
     * Return the SEO tab definition with sections for card panels.
     */
    public static function seoTabSections(): array
    {
        return [
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
                        'handle' => 'google_search_preview',
                        'field' => [
                            'type' => 'aardvark_seo_google_preview',
                            'display' => __('aardvark-seo::onpage.fields.google_preview.display'),
                            'listable' => 'hidden',
                        ],
                    ],
                    [
                        'handle' => 'use_meta_keywords',
                        'field' => [
                            'type' => 'toggle',
                            'display' => __('aardvark-seo::onpage.fields.use_meta_keywords.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.use_meta_keywords.instruct'),
                            'localizable' => true,
                        ],
                    ],
                    [
                        'handle' => 'meta_keywords',
                        'field' => [
                            'type' => 'taggable',
                            'display' => __('aardvark-seo::onpage.fields.meta_keywords.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.meta_keywords.instruct'),
                            'localizable' => true,
                            'if' => [
                                'use_meta_keywords' => 'equals true',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'display' => __('aardvark-seo::onpage.fields.urls_section.display'),
                'fields' => [
                    [
                        'handle' => 'canonical_url',
                        'field' => [
                            'type' => 'text',
                            'display' => __('aardvark-seo::onpage.fields.canonical_url.display'),
                            'listable' => 'hidden',
                            'localizable' => true,
                        ],
                    ],
                    [
                        'handle' => 'localized_urls',
                        'field' => [
                            'type' => 'grid',
                            'display' => __('aardvark-seo::onpage.fields.localized_urls.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.localized_urls.instruct'),
                            'listable' => 'hidden',
                            'add_row' => __('aardvark-seo::onpage.fields.localized_urls.add_row'),
                            'localizable' => true,
                            'fields' => [
                                [
                                    'handle' => 'locale',
                                    'field' => [
                                        'type' => 'text',
                                        'display' => __('aardvark-seo::onpage.fields.localized_urls.fields.locale.display'),
                                        'placeholder' => 'fr-fr',
                                    ],
                                ],
                                [
                                    'handle' => 'url',
                                    'field' => [
                                        'type' => 'text',
                                        'display' => __('aardvark-seo::onpage.fields.localized_urls.fields.url.display'),
                                        'placeholder' => 'mysite.com/fr/',
                                    ],
                                ],
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
                    [
                        'handle' => 'sitemap_priority',
                        'field' => [
                            'type' => 'select',
                            'display' => __('aardvark-seo::onpage.fields.sitemap_priority.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.sitemap_priority.instruct'),
                            'default' => '0.5',
                            'width' => 50,
                            'localizable' => true,
                            'options' => [
                                '0.0', '0.1', '0.2', '0.3', '0.4',
                                '0.5', '0.6', '0.7', '0.8', '0.9', '1.0',
                            ],
                        ],
                    ],
                    [
                        'handle' => 'sitemap_changefreq',
                        'field' => [
                            'type' => 'select',
                            'display' => __('aardvark-seo::onpage.fields.sitemap_changefreq.display'),
                            'instructions' => __('aardvark-seo::onpage.fields.sitemap_changefreq.instruct'),
                            'default' => 'daily',
                            'width' => 50,
                            'localizable' => true,
                            'options' => [
                                'always', 'hourly', 'daily', 'weekly',
                                'monthly', 'yearly', 'never',
                            ],
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
                            'display' => 'Override the twitter share settings for this page',
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
                            'localizable' => true,
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
                                'override_twitter_card_settings' => 'equals true',
                                'override_twitter_settings' => 'equals true',
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
        ];
    }

    /**
     * @inheritDoc
     */
    public static function requestBlueprint()
    {
        return StatamicBlueprint::make()->setContents([
            'tabs' => [
                'SEO' => [
                    'sections' => static::seoTabSections(),
                ],
            ],
        ]);
    }
}
