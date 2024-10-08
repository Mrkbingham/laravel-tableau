<?php

// config for InterWorks/Tableau
return [
    'url'             => env('TABLEAU_URL', 'https://your-tableau-server.com'),
    'site_name'       => env('TABLEAU_SITE_NAME', ''),
    'product_version' => env('TABLEAU_PRODUCT_VERSION', '2021.1'),

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | The Tableau Server REST API requires that you send a credentials token
    | with each request. The credentials token lets Tableau Server or Tableau
    | Cloud verify you as a valid, signed in user. To get a credentials token,
    | you call Sign In and pass credentials of a valid user, either a Personal
    | Access Token (PAT) or a user name and password.
    |
    */
    'credentials'     => [
        // PAT auth (not required if using username and password)
        'pat_name'   => env('TABLEAU_PAT_NAME', ''),
        'pat_secret' => env('TABLEAU_PAT_SECRET', ''),
        // Username and password auth (not required if using PAT)
        'username'   => env('TABLEAU_USERNAME', ''),
        'password'   => env('TABLEAU_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Expiry (minutes)
    |--------------------------------------------------------------------------
    |
    | When you get the response, you parse the credentials token out of the
    | response and store it in your application. By default, the credentials
    | token is good for 240 minutes.  You can specify a different timeout value
    | for the token by calling the tsm configuration set command to change the
    | wgserver.session.idle_limit setting.
    |
    | @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_concepts_auth.htm#using_auth_token
    |
    */
    'token_expiry'    => env('TABLEAU_TOKEN_EXPIRY', 240),
];
