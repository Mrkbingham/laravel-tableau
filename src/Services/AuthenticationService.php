<?php

namespace InterWorks\Tableau\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class AuthenticationService
{
    protected $client;
    protected $baseUri;
    protected $authToken;
    protected $siteId;

    public function __construct(Client $client)
    {
        $this->client    = $client;
        $this->baseUri   = Config::get('tableau.base_url');
        $this->authToken = '';
        $this->siteId    = '';
    }

    /**
     * Sign in to Tableau and get the authentication token.
     *
     * @return void
     */
    public function signIn()
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

        $data            = json_decode($response->getBody()->getContents(), true);
        $this->authToken = $data['credentials']['token'];
        $this->siteId    = $data['credentials']['site']['id'];
    }

    /**
     * Sign out from Tableau.
     *
     * @return void
     */
    public function signOut()
    {
        $this->client->post("{$this->baseUri}/api/3.9/auth/signout", [
            'headers' => [
                'X-Tableau-Auth' => $this->authToken,
            ],
        ]);

        $this->authToken = '';
        $this->siteId    = '';
    }

    /**
     * Get the auth token.
     *
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * Get the site ID.
     *
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }
}
