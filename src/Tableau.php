<?php

namespace InterWorks\Tableau;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class Tableau
{
    protected $baseUri;
    protected $siteId;
    protected $username;
    protected $password;

    protected $isAuthenticated = false;

    protected $authToken;

    public function __construct()
    {
        $this->baseUri   = Config::get('tableau.base_url') ?? throw new Exception('Tableau base URL is not set.');
        $this->authToken = '';
        $this->siteId    = '';

        // Setup username and password
        $this->username = Config::get('tableau.username') ?? throw new Exception('Tableau username is not set.');
        $this->password = Config::get('tableau.password') ?? throw new Exception('Tableau password is not set.');

        $this->authenticate();
    }

    /**
     * Sign in to Tableau and get the authentication token.
     *
     * @return void
     */
    public function authenticate()
    {
        // TODO: Add check for valid/invalid/expired token
        if ($this->isAuthenticated) {
            return;
        }

        $response = Http::post("{$this->baseUri}/api/3.9/auth/signin", [
            'credentials' => [
                'name'     => Config::get('tableau.username'),
                'password' => Config::get('tableau.password'),
                'site'     => [
                    'contentUrl' => Config::get('tableau.site_url')
                ],
            ],
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to authenticate with Tableau.');
        }

        $body            = self::parseXmlResponse($response->body());
        $this->authToken = $body['credentials']['@attributes']['token'];
        $this->siteId    = $body['credentials']['site']['@attributes']['id'];
    }

    /**
     * Returns the authentication token.
     *
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * Parses an XML response.
     *
     * @param string $xml
     *
     * @return array
     */
    public static function parseXmlResponse($xml)
    {
        // Load the XML file
        $data = simplexml_load_string($xml);
        if ($data === false) {
            throw new Exception('Failed to parse XML response.');
        }

        // Json encode/decode to convert to array
        $json = json_encode($data);
        return json_decode($json, true);
    }

}
