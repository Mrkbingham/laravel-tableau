<?php

namespace InterWorks\Tableau\Requests;

use InterWorks\Tableau\Data\Auth\AuthenticationResponse;
use InterWorks\Tableau\Data\Auth\JWTAuthentication;
use InterWorks\Tableau\Data\Auth\PATAuthentication;
use InterWorks\Tableau\Data\Auth\UsernameAuthentication;
use InvalidArgumentException;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;

class SignInRequest extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    public function __construct(protected JWTAuthentication|PATAuthentication|UsernameAuthentication $auth) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/auth/signin';
    }

    /**
     * The body of the request
     *
     * @throws InvalidArgumentException
     *
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        if ($this->auth instanceof JWTAuthentication) {
            return $this->buildJWTCredentials();
        } elseif ($this->auth instanceof PATAuthentication) {
            return $this->buildPATCredentials();
        } elseif ($this->auth instanceof UsernameAuthentication) {
            return $this->buildUsernameCredentials();
        }

        throw new InvalidArgumentException('Unsupported authentication type provided.');
    }

    /**
     * Build JWT authentication credentials
     *
     * @return array<string, mixed>
     */
    private function buildJWTCredentials(): array
    {
        return [
            'credentials' => [
                'jwt' => $this->auth->jwt,
                'site' => [
                    'contentUrl' => $this->auth->siteContentUrl,
                ],
            ],
        ];
    }

    /**
     * Build PAT authentication credentials
     *
     * @return array<string, mixed>
     */
    private function buildPATCredentials(): array
    {
        $credentials = [
            'credentials' => [
                'personalAccessTokenName' => $this->auth->personalAccessTokenName,
                'personalAccessTokenSecret' => $this->auth->personalAccessTokenSecret,
                'site' => [
                    'contentUrl' => $this->auth->siteContentUrl,
                ],
            ],
        ];

        return $this->addImpersonateUser($credentials);
    }

    /**
     * Build username authentication credentials
     *
     * @return array<string, mixed>
     */
    private function buildUsernameCredentials(): array
    {
        $credentials = [
            'credentials' => [
                'name' => $this->auth->username,
                'password' => $this->auth->password,
                'site' => [
                    'contentUrl' => $this->auth->siteContentUrl,
                ],
            ],
        ];

        return $this->addImpersonateUser($credentials);
    }

    /**
     * Add impersonate user to credentials if specified
     *
     * @param array<string, mixed> $credentials
     * @return array<string, mixed>
     */
    private function addImpersonateUser(array $credentials): array
    {
        if (!empty($this->auth->impersonateId)) {
            $credentials['credentials']['user'] = [
                'id' => $this->auth->impersonateId
            ];
        }

        return $credentials;
    }

    /**
     * Create a DTO from the response
     *
     * @param Response $response
     * @return mixed
     */
    public function createDtoFromResponse(Response $response): mixed
    {
        $data = $response->json();
        $credentials = $data['credentials'];

        return new AuthenticationResponse(
            token: $credentials['token'],
            siteId: $credentials['site']['id'],
            siteContentUrl: $credentials['site']['contentUrl'],
            userId: $credentials['user']['id'],
            timeToExpiration: $credentials['estimatedTimeToExpiration'] ?? null,
        );
    }
}
