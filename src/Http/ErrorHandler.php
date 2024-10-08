<?php

namespace InterWorks\Tableau\Http;

use Illuminate\Http\Client\Response;
use InterWorks\Tableau\Exceptions\ApiException;

class ErrorHandler
{
    /**
     * Handle API errors and throw exceptions
     *
     * @param Response $response The response object.
     *
     * @throws ApiException If an error occurs.
     *
     * @return void
     */
    public static function handle(Response $response)
    {
        // Get the status code and body
        $statusCode = $response->status();

        // Store the path - only add it to non 401/403 errors
        $path = $response->effectiveUri()->getPath();

        // Parse the error message
        if ($statusCode === 400) {
            $errorMessage = self::parseTableauError($response);
            throw new ApiException($errorMessage, 400);
        }

        switch ($statusCode) {
            case 400:
                $errorMessage = "Bad Request to $path. Response: " . $response->body();
                throw new ApiException($errorMessage, 400);
            case 401:
                $errorMessage = self::parseTableauError($response);
                throw new ApiException('Unauthorized: ' . $errorMessage, 401);
            case 403:
                $errorMessage = self::parseTableauError($response);
                throw new ApiException('Forbidden: ' . $errorMessage, 403);
            case 404:
                $errorMessage = "Resource Not Found: $path. Response: " . $response->body();
                throw new ApiException($errorMessage, 404);
            case 500:
                $errorMessage = "Internal Server Error: $path. Response: " . $response->body();
                throw new ApiException($errorMessage, 500);
            default:
                throw new ApiException('API Error: ' . $response->body(), $statusCode);
        }
    }

    /**
     * Parse the tableau error code and details, outputting in a friendly format
     *
     * @param Response $response The response object.
     *
     * @return string
     */
    public static function parseTableauError(Response $response)
    {
        $body = $response->json();
        if (!isset($body['error'])) {
            return 'Unknown Tableau Error.';
        }

        $errorData = $body['error'];

        $errorSummary = $errorData['summary'];
        $errorCode = $errorData['code'];
        $errorDetails = $errorData['detail'];

        return "($errorCode) $errorSummary. $errorDetails.";
    }
}
