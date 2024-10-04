<?php

// config for InterWorks/Tableau
return [
    'base_url'    => env('TABLEAU_URL', 'https://your-tableau-server.com'),
    'product'     => env('TABLEAU_PRODUCT_VERSION', '2021.1'),
    'credentials' => [
        // Username and password auth (not required if using PAT)
        'username'   => env('TABLEAU_USERNAME', ''),
        'password'   => env('TABLEAU_PASSWORD', ''),
        // PAT auth (not required if using username and password)
        'pat_name'   => env('TABLEAU_PAT_NAME', ''),
        'pat_secret' => env('TABLEAU_PAT_SECRET', ''),
        // Site name
        'site_name'  => env('TABLEAU_SITE_NAME', '')
    ],
];
