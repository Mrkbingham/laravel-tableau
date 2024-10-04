<?php

namespace InterWorks\Tableau;

use Config;
use Dotenv\Dotenv;
use Exception;
use GuzzleHttp\Client;

class Tableau
{
    protected $client;
    protected $baseUri;
    protected $authToken;
    protected $siteId;

    public function __construct()
    {
        dd(env('TABLEAU_BASE_URL'));
        $this->client    = new Client();
        $this->baseUri   = Config::get('tableau.base_url') ?? throw new Exception('Tableau base URL is not set.');
        $this->authToken = '';
        $this->siteId    = '';
    }

    /**
     * Sign in to Tableau and get the authentication token.
     *
     * @return void
     */
    public function authenticate()
    {
        $response = $this->client->post("{$this->baseUri}/api/3.9/auth/signin", [
            'json' => [
                'credentials' => [
                    'name' => Config::get('tableau.username'),
                    'password' => Config::get('tableau.password'),
                    'site' => ['contentUrl' => Config::get('tableau.site_url')],
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $this->authToken = $data['credentials']['token'];
        $this->siteId = $data['credentials']['site']['id'];
    }
}
