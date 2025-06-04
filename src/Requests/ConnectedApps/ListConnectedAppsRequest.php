<?php

namespace InterWorks\Tableau\Requests\ConnectedApps;

use Illuminate\Support\Collection;
use InterWorks\Tableau\Data\ConnectedApps\ConnectedAppsCollection;
use InterWorks\Tableau\Requests\TableauRequest;
use Saloon\Enums\Method;
use Saloon\Http\Response;

class ListConnectedAppsRequest extends TableauRequest
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
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * The endpoint for the request
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return "/sites/:siteId/connected-apps/direct-trust";
    }

    /**
     * Create a DTO from the response
     *
     * @param Response $response The response from the request.
     *
     * @return mixed
     */
    public function createDtoFromResponse(Response $response): ConnectedAppsCollection
    {
        return ConnectedAppsCollection::fromArray($response->json());
    }
}
