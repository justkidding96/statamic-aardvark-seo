<?php

namespace Justkidding96\AardvarkSeo\Blueprints\CP;

use Justkidding96\AardvarkSeo\Blueprints\Blueprint as AardvarkBlueprint;
use Statamic\Facades\Blueprint as StatamicBlueprint;

class MarketingSettingsBlueprint implements AardvarkBlueprint
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
                            'handle' => 'gtm_section',
                            'field' => [
                                'type' => 'section',
                                'listable' => 'hidden',
                                'display' => __('aardvark-seo::marketing.fields.gtm_section.display'),
                                'instructions' => __('aardvark-seo::marketing.fields.gtm_section.instruct'),
                            ],
                        ],
                        [
                            'handle' => 'enable_gtm_script',
                            'field' => [
                                'type' => 'toggle',
                                'display' => __('aardvark-seo::marketing.fields.enable_gtm_script.display'),
                                'instructions' => __('aardvark-seo::marketing.fields.enable_gtm_script.instruct'),
                                'width' => 33,
                            ],
                        ],
                        [
                            'handle' => 'gtm_identifier',
                            'field' => [
                                'type' => 'text',
                                'display' => __('aardvark-seo::marketing.fields.gtm_identifier.display'),
                                'instructions' => __('aardvark-seo::marketing.fields.gtm_identifier.instruct'),
                                'width' => 66,
                                'if' => [
                                    'enable_gtm_script' => 'equals true',
                                ],
                            ],
                        ],
                        [
                            'handle' => 'site_verification_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::marketing.fields.site_verification_section.display'),
                                'listable' => 'hidden',
                            ],
                        ],
                        [
                            'handle' => 'google_verification_code',
                            'field' => [
                                'type' => 'text',
                                'display' => __('aardvark-seo::marketing.fields.google_verification_code.display'),
                                'width' => 50,
                            ],
                        ],
                        [
                            'handle' => 'bing_verification_code',
                            'field' => [
                                'type' => 'text',
                                'display' => __('aardvark-seo::marketing.fields.bing_verification_code.display'),
                                'width' => 50,
                            ],
                        ],
                        [
                            'handle' => 'yandex_verification_code',
                            'field' => [
                                'type' => 'text',
                                'display' => __('aardvark-seo::marketing.fields.yandex_verification_code.display'),
                                'width' => 50,
                            ],
                        ],
                        [
                            'handle' => 'baidu_verification_code',
                            'field' => [
                                'type' => 'text',
                                'display' => __('aardvark-seo::marketing.fields.baidu_verification_code.display'),
                                'width' => 50,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
