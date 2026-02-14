<?php

return [

    'singular' => 'General',
    'plural' => 'Generals',

    // Control Panel
    'index' => 'General Settings',

    'fields' => [
        'titles_section' => [
            'display' => 'Title',
            'instruct' => 'Control how your site titles appear',
        ],
        'title_order' => [
            'display' => 'Order',
            'instruct' => 'Choose the order of the page title and site name in the meta title.',
            'options' => [
                'title_first' => 'Page Title | Site Name',
                'site_first' => 'Site Name | Page Title',
            ],
        ],
        'title_separator' => [
            'display' => 'Separator',
            'instruct' => 'Set the character to separate the site and page names in the meta title'
        ],
        'site_name' => [
            'display' => 'Name',
            'instruct' => 'This will be used in generated meta titles as well as the OpenGraph site name property'
        ],
        'favicon_section' => [
            'display' => 'Favicon',
            'instruct' => 'Upload a favicon to show in search results and the browser.'
        ],
        'global_favicon' => [
            'display' => 'Global Favicon',
            'instruct' => 'Use a supported favicon file format (`.png`) in a square size that’s a multiple of 48px.',
        ],
        'knowledge_graph_section' => [
            'display' => 'Structured Data',
        ],
        'company_or_person' => [
            'display' => 'Company or Person?',
            'instruct' => 'Select whether the content on this website represents a company or a person'
        ],
        'target_name' => [
            'display' => 'Target Name',
            'instruct' => 'Enter the person/company name here'
        ],
        'company_logo' => [
            'display' => 'Company Logo',
        ],
        'breadcrumbs_section' => [
            'display' => 'Breadcrumbs',
            'instruct' => 'See [https://developers.google.com/search/docs/data-types/breadcrumb](https://developers.google.com/search/docs/data-types/breadcrumb) for more information.',
        ],
        'enable_breadcrumbs' => [
            'display' => 'Enabled',
        ],
        'no_index_section' => [
            'display' => 'Indexing',
            'instruct' => 'Prevent indexing across the entire site.',
        ],
        'no_index_site' => [
            'display' => 'No Index',
            'instruct' => 'Set to `true` to exclude the **whole site** from search engine indexing - this can also be configured on a per-page basis.',
        ],
        'default_locale_section' => [
            'display' => 'Locale',
            'instruct' => 'Select a locale to use as a default hreflang tag (see [https://developers.google.com/search/blog/2013/04/x-default-hreflang-for-international-pages](https://developers.google.com/search/blog/2013/04/x-default-hreflang-for-international-pages)).',
        ],
        'default_locale' => [
            'display' => 'Default Locale',
        ],
    ]

];
