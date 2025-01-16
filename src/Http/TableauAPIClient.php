<?php

namespace Vendor\TableauAPI\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TableauAPIClient
{
    /** @var Client */
    protected $client;
    /** @var string */
    protected $baseUri;
    /** @var string */
    protected $username;
    /** @var string */
    protected $password;
    /** @var string */
    protected $siteId;
    /** @var string */
    protected $token;

    /**
     * TableauAPIClient constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->baseUri = config('tableau.api_base_uri');
        $this->username = config('tableau.username');
        $this->password = config('tableau.password');
        $this->siteId = config('tableau.site_id');
    }

    /**
     * Authenticate and store the auth token.
     *
     * @return string|null
     */
    public function authenticate(): ?string
    {
        if ($this->token) {
            return $this->token; // Return the existing token if it's already set
        }

        try {
            $response = $this->client->post($this->baseUri . '/api/3.10/auth/signin', [
                'json' => [
                    'credentials' => [
                        'name'     => $this->username,
                        'password' => $this->password,
                        'site'     => [
                            'contentUrl' => $this->siteId
                        ]
                    ]
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $this->token = $body['credentials']['token'] ?? null;
            return $this->token;
        } catch (RequestException $e) {
            return null;
        }
    }

    /**
     * Sign out of Tableau and invalidate the session
     *
     * @return void
     */
    public function signOut(): void
    {
        $this->client->post($this->baseUri . '/api/3.10/auth/signout', [
            'headers' => [
                'X-Tableau-Auth' => $this->token,
            ]
        ]);

        $this->token = null; // Clear the token after signout
    }

    /**
     * Parses an XML response.
     *
     * @param string $xml The XML response.
     *
     * @throws Exception If the XML response cannot be parsed.
     *
     * @return array
     */
    public static function parseXmlResponse(string $xml): array
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
