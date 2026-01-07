<?php

namespace Justkidding96\AardvarkSeo\Blueprints\CP;

use Justkidding96\AardvarkSeo\Blueprints\Blueprint as AardvarkBlueprint;
use Statamic\Facades\Blueprint as StatamicBlueprint;

class SocialSettingsBlueprint implements AardvarkBlueprint
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
                            'handle' => 'social_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::social.fields.social_section.display'),
                                'instructions' => __('aardvark-seo::social.fields.social_section.instruct'),
                                'listable' => 'hidden',
                            ],
                        ],
                        [
                            'handle' => 'social_links',
                            'field' => [
                                'type' => 'grid',
                                'display' => __('aardvark-seo::social.fields.social_links.display'),
                                'add_row' => __('aardvark-seo::social.fields.social_links.add_new'),
                                'fields' => [
                                    [
                                        'handle' => 'social_icon',
                                        'field' => [
                                            'type' => 'select',
                                            'display' => __('aardvark-seo::social.fields.social_links.icon'),
                                            'options' => self::getSocialOptions(),
                                        ],
                                    ],
                                    [
                                        'handle' => 'url',
                                        'field' => [
                                            'type' => 'text',
                                            'display' => __('aardvark-seo::social.fields.social_links.url'),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'opengraph' => [
                    'display' => __('aardvark-seo::social.fields.opengraph.display'),
                    'fields' => [
                        [
                            'handle' => 'og_image_site',
                            'field' => [
                                'type' => 'assets',
                                'display' => __('aardvark-seo::social.fields.og_image_site.display'),
                                'instructions' => __('aardvark-seo::social.fields.og_image_site.instruct'),
                                'max_files' => 1,
                                'restrict' => false,
                                'container' => config('aardvark-seo.asset_container'),
                                'folder' => config('aardvark-seo.asset_folder'),
                            ],
                        ],
                    ],
                ],
                'twitter' => [
                    'display' => __('aardvark-seo::social.fields.twitter.display'),
                    'fields' => [
                        [
                            'handle' => 'twitter_username',
                            'field' => [
                                'type' => 'text',
                                'display' => __('aardvark-seo::social.fields.twitter_username.display'),
                                'instructions' => __('aardvark-seo::social.fields.twitter_username.instruct'),
                            ],
                        ],
                        [
                            'handle' => 'twitter_meta_section_site',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::social.fields.twitter_meta_section_site.display'),
                                'instructions' => __('aardvark-seo::social.fields.twitter_meta_section_site.instruct'),
                                'listable' => 'hidden',
                            ],
                        ],
                        [
                            'handle' => 'twitter_card_type_site',
                            'field' => [
                                'type' => 'select',
                                'display' => __('aardvark-seo::social.fields.twitter_card_type_site.display'),
                                'instructions' => __('aardvark-seo::social.fields.twitter_card_type_site.instruct'),
                                'width' => 50,
                                'default' => 'summary',
                                'options' => [
                                    'summary' => 'Summary Card',
                                    'summary_large_image' => 'Summary card with Large Image',
                                ],
                            ],
                        ],
                        [
                            'handle' => 'twitter_default_image_section',
                            'field' => [
                                'type' => 'section',
                                'display' => __('aardvark-seo::social.fields.twitter_default_image_section.display'),
                                'instructions' => __('aardvark-seo::social.fields.twitter_default_image_section.instruct'),
                                'listable' => 'hidden',
                            ],
                        ],
                        [
                            'handle' => 'twitter_summary_image_site',
                            'field' => [
                                'type' => 'assets',
                                'display' => __('aardvark-seo::social.fields.twitter_summary_image_site.display'),
                                'instructions' => __('aardvark-seo::social.fields.twitter_summary_image_site.instruct'),
                                'max_files' => 1,
                                'restrict' => false,
                                'container' => config('aardvark-seo.asset_container'),
                                'folder' => config('aardvark-seo.asset_folder'),
                                'width' => 50,
                            ],
                        ],
                        [
                            'handle' => 'twitter_summary_large_image_site',
                            'field' => [
                                'type' => 'assets',
                                'display' => __('aardvark-seo::social.fields.twitter_summary_large_image_site.display'),
                                'instructions' => __('aardvark-seo::social.fields.twitter_summary_large_image_site.instruct'),
                                'max_files' => 1,
                                'restrict' => false,
                                'container' => config('aardvark-seo.asset_container'),
                                'folder' => config('aardvark-seo.asset_folder'),
                                'width' => 50,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private static function getSocialOptions()
    {
        $defaults = collect([
            'amazon' => 'Amazon',
            'android' => 'Android',
            'apple' => 'Apple',
            'behance' => 'Behance',
            'bitbucket' => 'Bitbucket',
            'codepen' => 'Codepen',
            'dribbble' => 'Dribbble',
            'facebook' => 'Facebook',
            'flickr' => 'Flickr',
            'foursquare' => 'Foursquare',
            'github' => 'Github',
            'google-plus' => 'Google Plus',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'medium' => 'Medium',
            'meetup' => 'Meetup',
            'pinterest' => 'Pinterest',
            'reddit' => 'Reddit',
            'skype' => 'Skype',
            'slack' => 'Slack',
            'soundcloud' => 'Soundcloud',
            'spotify' => 'Spotify',
            'twitch' => 'Twitch',
            'twitter' => 'Twitter',
            'tumblr' => 'Tumblr',
            'whatsapp' => 'WhatsApp',
            'yelp' => 'Yelp',
            'youtube' => 'YouTube',
        ]);

        $config_options = collect(config('aardvark-seo.custom_socials'));

        return $config_options->merge($defaults)->sort()->toArray();
    }
}
