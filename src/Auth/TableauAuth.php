<?php

namespace InterWorks\Tableau\Auth;

use InterWorks\Tableau\Http\HttpClient;
use Illuminate\Support\Facades\Config;
use InterWorks\Tableau\Http\ResponseParser;
use InterWorks\Tableau\Services\VersionService;

class TableauAuth
{
    protected $client;
    protected $username;
    protected $password;
    protected $site;
    protected $token;
    protected $tokenExpiration;
    protected $apiVersion;

    public function __construct($username = null, $password = null, $site = null)
    {
        // Use either the passed parameters or fall back to config values
        $this->username = $username ?? Config::get('tableau.credentials.username');
        $this->password = $password ?? Config::get('tableau.credentials.password');
        $this->site     = $site ?? Config::get('tableau.site_name');

        // Initialize the HTTP client
        $this->client = new HttpClient();

        // Immediately authenticate
        $this->authenticate();
    }

    /**
     * Authenticate with Tableau Server and get an auth token
     *
     * @return void
     */
    public function authenticate(): void
    {
        // If token is valid, reuse it
        if ($this->token && !$this->isTokenExpired()) {
            return;
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

        // Extract the token and set the token expiration
        $this->token = $response['credentials']['token'] ?? null;
        $this->setTokenExpiration();

        // Set the token on the client
        $this->client->setAuthToken($this->token);
    }

    /**
     * Returns the client object
     *
     * @return HttpClient
     */
    public function client(): HttpClient
    {
        return $this->client;
    }

    /**
     * Returns the current token
     *
     * @return string|null
     */
    public function getToken(): string|null
    {
        return $this->token;
    }

    /**
     * Sign out from Tableau Server and invalidate the token
     *
     * @return void
     */
    public function signOut(): void
    {
        if ($this->token) {
            $this->client->post('/auth/signout');
            $this->token = null;  // Invalidate the token
        }
    }

    /**
     * Sets the expiration time of the token
     *
     * @return void
     */
    public function setTokenExpiration(): void
    {
        $this->tokenExpiration = time() + Config::get('tableau.token_expiration') * 60;
    }

    /**
     * Check if the current token has expired
     *
     * @return bool
     */
    protected function isTokenExpired(): bool
    {
        return time() >= $this->getTokenExpiration();
    }
}
