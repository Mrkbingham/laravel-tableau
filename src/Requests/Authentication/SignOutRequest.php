<?php

namespace InterWorks\Tableau\Requests\Authentication;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SignOutRequest extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/auth/signout';
    }
}
