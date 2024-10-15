<?php

return [
    'asset_container' => env('AARDVARK_SEO_ASSET_CONTAINER', 'assets'),
    'asset_folder' => env('AARDVARK_SEO_ASSET_FOLDER', 'seo'),
    'custom_socials' => [],
    'excluded_collections' => [],
    'excluded_taxonomies' => [],
    'storage_driver' => env('AARDVARK_SEO_STORAGE_DRIVER', 'file'),
    'database_connection' => env('AARDVARK_SEO_DATABASE_CONNECTION', null),
];
