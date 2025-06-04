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
use Saloon\Contracts\Authenticator;
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

    public function __construct(protected AuthType $authType) {}

    /**
     * Authenticate the request with an authenticator.
     *
     * @return $this
     */
    public function authenticate(Authenticator $authenticator): static
    {
        // If we're not authenticated (and not _trying_ to authenticate), we need to authenticate first.
        if (empty($this->token) ) {
            // Make a request to the Authentication endpoint
            $signInResponse = $this->send(new SignInRequest($this->getAuth()))->dto();
            $this->site = new Site($signInResponse->siteContentUrl, $signInResponse->siteId);
            $this->token = $signInResponse->token;
        }

        $this->authenticator = $authenticator;

        return $this;
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

    /**
     * The Base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return config('tableau.url') . '/api/' . VersionService::getAPIVersion();
    }

    /**
     * Returns the site connected to.
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
}
