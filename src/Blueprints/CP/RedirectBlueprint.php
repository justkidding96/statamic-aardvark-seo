<?php

namespace Justkidding96\AardvarkSeo\Blueprints\CP;

use Justkidding96\AardvarkSeo\Blueprints\Blueprint as AardvarkBlueprint;
use Statamic\Facades\Blueprint as StatamicBlueprint;

class RedirectBlueprint implements AardvarkBlueprint
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
                            'fields' => [
                                [
                                    'handle' => 'source_url',
                                    'field' => [
                                        'type' => 'text',
                                        'display' => __('aardvark-seo::redirects.redirect.source_url'),
                                    ],
                                ],
                                [
                                    'handle' => 'target_url',
                                    'field' => [
                                        'type' => 'text',
                                        'display' => __('aardvark-seo::redirects.redirect.target_url'),
                                    ],
                                ],
                                [
                                    'handle' => 'status_code',
                                    'field' => [
                                        'type' => 'radio',
                                        'inline' => true,
                                        'options' => [
                                            '301',
                                            '302',
                                        ],
                                        'display' => __('aardvark-seo::redirects.redirect.status_code'),
                                        'default' => '301',
                                    ],
                                ],
                                [
                                    'handle' => 'is_active',
                                    'field' => [
                                        'type' => 'toggle',
                                        'display' => __('aardvark-seo::redirects.redirect.is_active'),
                                        'default' => true,
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
