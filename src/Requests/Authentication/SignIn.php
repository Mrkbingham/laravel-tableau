<?php

namespace InterWorks\Tableau\Requests\Authentication;

use InterWorks\Tableau\Data\Authentication\AuthenticationResponse;
use InterWorks\Tableau\Data\Authentication\JWTAuthentication;
use InterWorks\Tableau\Data\Authentication\PATAuthentication;
use InterWorks\Tableau\Data\Authentication\UsernameAuthentication;
use InvalidArgumentException;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class SignIn extends Request implements HasBody
{
    use HasJsonBody;

    /**
     * The HTTP method of the request
     *
     * @var Method
     */
    protected Method $method = Method::POST;

    /**
     * The request's constructor
     *
     * @param JWTAuthentication|PATAuthentication|UsernameAuthentication $auth The authentication method to use.
     *
     * @return void
     */
    public function __construct(protected readonly JWTAuthentication|PATAuthentication|UsernameAuthentication $auth) {
        //
    }

    /**
     * The endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return '/auth/signin';
    }

    /**
     * Create a DTO from the response
     *
     * @param Response $response The response from the request.
     *
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

    /**
     * The body of the request
     *
     * @throws InvalidArgumentException When an unsupported authentication type is provided.
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
     * @param array<string, mixed> $credentials The credentials array to modify.
     *
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
}
