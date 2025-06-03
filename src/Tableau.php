<?php

namespace InterWorks\Tableau;

use InterWorks\Tableau\Services\VersionService;
use Saloon\Contracts\Authenticator;
use Saloon\Helpers\OAuth2\OAuthConfig;
use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\OAuth2\AuthorizationCodeGrant;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\HasTimeout;
use InterWorks\Tableau\TableauAuthenticator;

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
    protected string $token = '';

    public function __construct(
        protected readonly string $siteContentUrl = '',
        protected readonly string $username = '',
        protected readonly string $password = '',
    ) {}

    protected function defaultAuth(): ?Authenticator
    {
        return new TableauAuthenticator();
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
}
