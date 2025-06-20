<?php

namespace InterWorks\Tableau\Requests\ConnectedApps;

use InterWorks\Tableau\Data\ConnectedApps\ConnectedApp;
use InterWorks\Tableau\Requests\TableauRequest;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class GetConnectedApp extends TableauRequest
{
    /**
     * The HTTP method of the request
     *
     * @var Method
     */
    protected Method $method = Method::GET;

    /**
     * The request's constructor
     *
     * @param string $appId The ID of the connected app to retrieve.
     *
     * @return void
     */
    public function __construct(protected readonly string $appId) {
        //
    }

    /**
     * The endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return "/sites/:siteId/connected-apps/direct-trust/{$this->appId}";
    }

    /**
     * Create a DTO from the response
     *
     * @param Response $response The response from the request.
     *
     * @return mixed
     */
    public function createDtoFromResponse(Response $response): ConnectedApp
    {
        return ConnectedApp::fromArray($response->json()['connectedApplication']);
    }
}
