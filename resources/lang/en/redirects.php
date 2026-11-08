<?php

return [

    'singular' => 'Redirect',
    'plural' => 'Redirects',

    // Control Panel
    'index' => 'Redirects Settings',

    // Redirect attributes
    'redirect' => [
        'source_url' => 'Source URL',
        'target_url' => 'Target URL',
        'status_code' => 'Status Code',
        'is_active' => 'Is Active?',
        'is_pattern' => 'Is Pattern?',
        'is_pattern_instructions' => 'Treat the source URL as a regular expression that must match the whole URL, e.g. `/blog/.*`.',
    ],

    // Permissions
    'permissions' => [
        'view' => 'View Redirects',
        'edit' => 'Edit/Update Redirects',
        'create' => 'Create Redirects'
    ],

    // Pages
    'pages' => [
        'create' => 'Create a redirect',
        'edit' => 'Edit a redirect',
    ],

    // Actions
    'actions' => [
        'create' => 'Create a redirect',
    ],

    // Import
    'import' => [
        'success' => ':imported redirect(s) imported, :skipped skipped.',
        'error_reading' => 'Could not read the uploaded file.',
        'invalid_format' => 'Invalid CSV format. The file must have at least two columns.',
        'missing_columns' => 'CSV must contain source_url and target_url columns.',
    ],

];
