<?php

namespace InterWorks\Tableau\Auth;

use InterWorks\Tableau\Http\HttpClient;
use Illuminate\Support\Facades\Config;

class TableauAuth
{
    protected $client;
    protected $username;
    protected $password;
    protected $site;
    protected $token;
    protected $tokenExpiration;

    public function __construct($username = null, $password = null, $site = null)
    {
        // Use either the passed parameters or fall back to config values
        $this->username = $username ?? Config::get('tableau.username');
        $this->password = $password ?? Config::get('tableau.password');
        $this->site = $site ?? Config::get('tableau.site_name');

        // Base URL from config
        $baseUrl = Config::get('tableau.url') . '/api/' . Config::get('tableau.api_version');

        // Initialize the HTTP client
        $this->client = new HttpClient($baseUrl);
    }

    /**
     * Authenticate with Tableau Server and get an auth token
     */
    public function authenticate()
    {
        // If token is valid, reuse it
        if ($this->token && !$this->isTokenExpired()) {
            return $this->token;
        }

        // XML payload for the authentication request
        $payload = [
            'credentials' => [
                'name' => $this->username,
                'password' => $this->password,
                'site' => [
                    'contentUrl' => $this->site
                ]
            ]
        ];

        $response = $this->client->post('/auth/signin', $payload);

        $data = ResponseParser::parse($response);

        // Extract the token and token expiration
        $this->token = $data['credentials']['token'] ?? null;
        $this->tokenExpiration = time() + 120 * 60; // Assume token is valid for 120 minutes

        return $this->token;
    }

    /**
     * Sign out from Tableau Server and invalidate the token
     */
    public function signOut()
    {
        if ($this->token) {
            $this->client->post('/auth/signout');
            $this->token = null;  // Invalidate the token
        }
    }

    /**
     * Check if the current token has expired
     */
    protected function isTokenExpired()
    {
        return time() >= $this->tokenExpiration;
    }
}
