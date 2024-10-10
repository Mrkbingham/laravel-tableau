<?php

namespace InterWorks\Tableau\Http;

use Illuminate\Http\Client\Response;
use InterWorks\Tableau\Exceptions\APIException;

class ErrorHandler
{
    /** @var Response The response object */
    protected $response;

    /** @var object The details of the error */
    protected $errorData;

    /**
     * The constructor
     *
     * @param Response $response The response object.
     *
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->response = $response;

        // Parse the error data
        $this->errorData = $this->getErrorData();
    }
    /**
     * Handle API errors and throw exceptions
     *
     * @throws APIException If an error occurs.
     *
     * @return void
     */
    public function outputMessage()
    {
        // Get the status code
        $statusCode = $this->response->status();

        // Parse the error message based on the status code
        switch ($statusCode) {
            case 400:
                $path = $this->response->effectiveUri()->getPath();
                $errorMessage = $this->getDetailMessage();
                throw new APIException("Bad Request: $path. $errorMessage", 400);
            case 401:
                $errorMessage = $this->getDetailMessage();
                throw new APIException("Unauthorized: $errorMessage", 401);
            case 403:
                $errorMessage = $this->getDetailMessage();
                throw new APIException("Forbidden: $errorMessage", 403);
            case 404:
                $path = $this->response->effectiveUri()->getPath();
                $errorMessage = "Resource Not Found: $path. Response: " . $this->response->body();
                throw new APIException($errorMessage, 404);
            case 500:
                $path = $this->response->effectiveUri()->getPath();
                $errorMessage = "Internal Server Error: $path. Response: " . $this->response->body();
                throw new APIException($errorMessage, 500);
            default:
                $errorMessage = "API Error: " . $this->response->body();
                throw new APIException($errorMessage, $statusCode);
        }
    }

    /**
     * Gets the error message from the response
     *
     * @return string
     */
    public function errorSummary()
    {
        return $this->errorData['summary'];
    }

    /**
     * Retrieves the error data from the response
     *
     * @return array
     */
    public function getErrorData(): array
    {
        $body = $this->response->json();
        if (!isset($body['error'])) {
            // Create a response that mimics the data-structure so we can use the corresponding detail methods
            return [
                'summary' => 'Unknown Tableau Error',
                'code'    => 0,
                'detail'  => 'No error details available',
            ];
        }

        return $body['error'];
    }

    /**
     * Returns the error code from the response
     *
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_concepts_errors.htm#table-of-error-codes
     *
     * @return integer
     */
    public function errorCode(): int
    {
        return (int) $this->errorData['code'];
    }

    /**
     * Returns the error details from the response.
     *
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_concepts_errors.htm
     *
     * @return string
     */
    public function errorDetail(): string
    {
        return $this->errorData['detail'];
    }

    /**
     * Generates a user-friendly format that concatenates all the error details for easy troubleshooting.
     *
     * @return string
     */
    public function getDetailMessage()
    {
        $errorSummary = $this->errorSummary();
        $errorCode = $this->errorCode();
        $errorDetail = $this->errorDetail();

        return "($errorCode) $errorSummary. $errorDetail.";
    }
}
