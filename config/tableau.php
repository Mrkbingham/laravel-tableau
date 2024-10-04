<?php

// config for InterWorks/Tableau
return [
    'base_url' => env('TABLEAU_BASE_URL'),
    'username' => env('TABLEAU_USERNAME'),
    'password' => env('TABLEAU_PASSWORD'),
    'site_url' => env('TABLEAU_SITE_URL'),
    'version'  => env('TABLEAU_PRODUCT_VERSION', '2020.1'),
];
