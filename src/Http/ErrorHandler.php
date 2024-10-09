<?php

namespace InterWorks\Tableau\Http;

use Illuminate\Http\Client\Response;
use InterWorks\Tableau\Exceptions\APIException;

class ErrorHandler
{
    /**
     * Handle API errors and throw exceptions
     *
     * @param Response $response The response object.
     *
     * @throws APIException If an error occurs.
     *
     * @return void
     */
    public static function handle(Response $response)
    {
        // Get the status code
        $statusCode = $response->status();

        // Parse the error message based on the status code
        switch ($statusCode) {
            case 400:
                $errorMessage = self::parseTableauError($response);
                throw new APIException("Bad Request: $errorMessage", 400);
            case 401:
                $errorMessage = self::parseTableauError($response);
                throw new APIException("Unauthorized: $errorMessage", 401);
            case 403:
                $errorMessage = self::parseTableauError($response);
                throw new APIException("Forbidden: $errorMessage", 403);
            case 404:
                $path = $response->effectiveUri()->getPath();
                $errorMessage = "Resource Not Found: $path. Response: " . $response->body();
                throw new APIException($errorMessage, 404);
            case 500:
                $path = $response->effectiveUri()->getPath();
                $errorMessage = "Internal Server Error: $path. Response: " . $response->body();
                throw new APIException($errorMessage, 500);
            default:
                $errorMessage = "API Error: " . $response->body();
                throw new APIException($errorMessage, $statusCode);
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
            return 'Unknown Tableau Error';
        }

        $errorData = $body['error'];

        $errorSummary = $errorData['summary'];
        $errorCode = $errorData['code'];
        $errorDetails = $errorData['detail'];

        return "($errorCode) $errorSummary. $errorDetails.";
    }
}
