<?php

namespace InterWorks\Tableau\Requests\ConnectedApps;

use InterWorks\Tableau\Requests\TableauRequest;
use Saloon\Enums\Method;

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
}
