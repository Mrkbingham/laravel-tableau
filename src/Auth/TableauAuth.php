<?php

namespace InterWorks\Tableau\Auth;

use Illuminate\Support\Facades\Config;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Http\HttpClient;

class TableauAuth
{
    /** @var HttpClient */
    protected $client;
    /** @var string */
    protected $username;
    /** @var string */
    protected $password;
    /** @var string */
    protected $siteContentURL;
    /** @var string */
    protected $token;
    /** @var integer */
    protected $tokenExpiration;
    /** @var string */
    protected $apiVersion;

    /**
     * TableauAuth constructor.
     *
     * @param AuthType    $authType       The type of authentication to use (e.g., 'pat', 'username').
     * @param string|null $siteContentURL The Tableau site name.
     *
     * @return void
     */
    public function __construct(AuthType $authType = AuthType::USERNAME, ?string $siteContentURL = null)
    {
        if ($authType === AuthType::USERNAME) {
            // Username and password auth
            $this->username = Config::get('tableau.credentials.username');
            $this->password = Config::get('tableau.credentials.password');
        } else {
            // PAT
            $this->patName = Config::get('tableau.credentials.pat_name');
            $this->patSecret = Config::get('tableau.credentials.pat_secret');
        }
        $this->siteContentURL = $siteContentURL ?? Config::get('tableau.site_name');

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
                'name'     => $this->username,
                'password' => $this->password,
                'site'     => [
                    'contentUrl' => $this->siteContentURL
                ]
            ]
        ];

        $response = $this->client->post('/auth/signin', $payload);

        // Extract the token and set the token expiration
        $this->token = $response['credentials']['token'] ?? null;
        $this->setTokenExpiration();

        // Set the token on the client
        $this->client->setAuthToken($this->token);

        // Set the site ID
        $this->siteID = $response['credentials']['site']['id'];
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
     * Returns the current site (Tableau site name, NOT id)
     *
     * @return string
     */
    public function getSiteContentURL(): string
    {
        return $this->siteContentURL;
    }

    /**
     * Returns the Site ID (the Tableau site ID, NOT the site name)
     *
     * @return string
     */
    public function getSiteID(): string
    {
        return $this->siteID;
    }

    /**
     * Sign out from Tableau Server and invalidate the token
     *
     * @return void
     */
    public function signOut(): void
    {
        if ($this->token) {
            $this->client->post('/auth/signout', []);
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
     * @return boolean
     */
    protected function isTokenExpired(): bool
    {
        return time() >= $this->getTokenExpiration();
    }
}
