<?php

namespace InterWorks\Tableau\Http;

use InterWorks\Tableau\Exceptions\ApiException;

class ErrorHandler
{
    /**
     * Handle API errors and throw exceptions
     */
    public static function handle($response)
    {
        $statusCode = $response->status();

        switch ($statusCode) {
            case 400:
                throw new ApiException('Bad Request: ' . $response->body(), 400);
            case 401:
                throw new ApiException('Unauthorized: ' . $response->body(), 401);
            case 403:
                throw new ApiException('Forbidden: ' . $response->body(), 403);
            case 404:
                throw new ApiException('Resource Not Found: ' . $response->body(), 404);
            case 500:
                throw new ApiException('Internal Server Error: ' . $response->body(), 500);
            default:
                throw new ApiException('API Error: ' . $response->body(), $statusCode);
        }
    }
}
