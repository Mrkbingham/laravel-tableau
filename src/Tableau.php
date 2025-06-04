<?php

namespace InterWorks\Tableau;

use InterWorks\Tableau\Data\Authentication\JWTAuthentication;
use InterWorks\Tableau\Data\Authentication\PATAuthentication;
use InterWorks\Tableau\Data\Authentication\UsernameAuthentication;
use InterWorks\Tableau\Data\Site;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Requests\Authentication\SignInRequest;
use InterWorks\Tableau\Services\VersionService;
use RuntimeException;
use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\HasTimeout;

class Tableau extends Connector
{
    use AcceptsJson;
    use AuthorizationCodeGrant;
    use HasTimeout;

    /** @var integer */
    protected int $connectTimeout = 60;
    /** @var integer */
    protected int $requestTimeout = 120;

    /** @var string The auth token for the connector. */
    protected ?string $token = null;

    /** @var Site The site connected to. */
    protected ?Site $site = null;

    /**
     * The constructor for the Tableau connector.
     *
     * @param AuthType $authType The type of authentication to use.
     *
     * @return void
     */
    public function __construct(protected AuthType $authType) {
        //
    }

    /**
     * This method is called when the request is being prepared, and handles the global authentication for the connector.
     *
     * @param PendingRequest $pendingRequest The pending request that is being prepared.
     *
     * @return void
     */
    public function boot(PendingRequest $pendingRequest): void
    {
        // If we've already authenticated, or are authenticating, we can skip this
        if ($this->getToken() || $pendingRequest->getRequest() instanceof SignInRequest) {
            return;
        }

        // Authenticate the request by sending a SignInRequest.
        $signInResponse = $this->send(new SignInRequest($this->getAuth()))->dto();
        $this->site = new Site($signInResponse->siteContentUrl, $signInResponse->siteId);
        $this->token = $signInResponse->token;

        // Add the token to the header
        $pendingRequest->authenticate(new HeaderAuthenticator($this->token, 'X-Tableau-Auth'));
    }

    /**
     * The Base URL of the API.
     *
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return config('tableau.url') . '/api/' . VersionService::getAPIVersion();
    }

    /**
     * Gets the authentication type for the connector.
     *
     * @return JWTAuthentication|PATAuthentication|UsernameAuthentication
     */
    public function getAuth(): JWTAuthentication|PATAuthentication|UsernameAuthentication
    {
        return match ($this->getAuthType()) {
            AuthType::JWT => new JWTAuthentication(),
            AuthType::PAT => new PATAuthentication(
                personalAccessTokenName: config('tableau.credentials.pat_name'),
                personalAccessTokenSecret: config('tableau.credentials.pat_secret')
            ),
            AuthType::USERNAME => new UsernameAuthentication(
                username: config('tableau.credentials.username'),
                password: config('tableau.credentials.password')
            ),
        };
    }

    /**
     * Gets the authentication type for the connector.
     *
     * @return AuthType
     */
    public function getAuthType(): AuthType
    {
        return $this->authType;
    }

    /**
     * Gets a token for the connector.
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Returns the site connected to.
     *
     * @throws RuntimeException If the site is not set.
     *
     * @return Site|null
     */
    public function getSite(): ?Site
    {
        if (is_null($this->site)) {
            throw new RuntimeException('Site is not set. Please authenticate first.');
        }
        return $this->site;
    }

    /**
     * The default headers to send with each request.
     * This is used by the AcceptsJson trait.
     *
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
