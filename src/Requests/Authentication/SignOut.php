<?php

namespace InterWorks\Tableau\Requests\Authentication;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SignOut extends Request
{
    /**
     * The HTTP method of the request
     *
     *  @var Method
     */
    protected Method $method = Method::POST;

    /**
     * The endpoint for the request
     *
     *  @return string
     */
    public function resolveEndpoint(): string
    {
        return '/auth/signout';
    }
}
