<?php

namespace InterWorks\Tableau;

use GuzzleHttp\Client;

class TableauService
{
    protected $client;
    protected $baseUri;
    protected $authToken;
    protected $siteId;

    public function __construct()
    {
        $this->client    = new Client();
        $this->baseUri   = config('tableau.base_url');
        $this->authToken = '';
        $this->siteId    = '';
    }

    /**
     * Authenticate with Tableau server.
     *
     * @return void
     */
    public function authenticate()
    {
        $response = $this->client->post("{$this->baseUri}/api/3.9/auth/signin", [
            'json' => [
                'credentials' => [
                    'name' => config('tableau.username'),
                    'password' => config('tableau.password'),
                    'site' => ['contentUrl' => config('tableau.site_url')],
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $this->authToken = $data['credentials']['token'];
        $this->siteId = $data['credentials']['site']['id'];
    }

    /**
     * Get the list of workbooks.
     *
     * @return array
     */
    public function getWorkbooks()
    {
        $response = $this->client->get("{$this->baseUri}/api/3.9/sites/{$this->siteId}/workbooks", [
            'headers' => [
                'X-Tableau-Auth' => $this->authToken,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
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
    }
}
