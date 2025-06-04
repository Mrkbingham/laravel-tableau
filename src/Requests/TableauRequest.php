<?php

namespace InterWorks\Tableau\Requests;

use Saloon\Helpers\URLHelper;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;

class TableauRequest extends Request
{
    /**
     * Resolves the endpoint - this must be implemented by subclasses, but currently acts as a placeholder.
     *
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return '';
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        $connector = $pendingRequest->getConnector();
        $baseUrl = $connector->resolveBaseUrl();

        // Replace :siteId with the actual site ID
        $endpoint = str_replace(':siteId', $connector->getSite()->id, $this->resolveEndpoint());

        // Replace placeholders in the endpoint with actual values
        $pendingRequest->setUrl(URLHelper::join($baseUrl, $endpoint));
    }
}
