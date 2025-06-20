<?php

namespace InterWorks\Tableau\Requests\Views;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class QueryViewsForSiteRequest extends Request
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
        return '/sites/:siteId/views';
    }
}
