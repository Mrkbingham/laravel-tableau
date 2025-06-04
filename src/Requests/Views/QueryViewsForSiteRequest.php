<?php

namespace InterWorks\Tableau\Requests\Views;

use InterWorks\Tableau\Data\Site;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class QueryViewsForSiteRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(protected Site $site) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return "/sites/{$this->site->id}/views";
    }
}
