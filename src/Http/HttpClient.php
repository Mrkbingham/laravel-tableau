<?php

namespace InterWorks\Tableau\Http;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Auth\TableauAuth;
use InterWorks\Tableau\Http\ErrorHandler;
use InterWorks\Tableau\Services\VersionService;

class HttpClient
{
    /**
     * @var string|null $tableauURL The base URL for Tableau Server/Cloud
     */
    protected $tableauURL;
    /**
     * @var string|null $apiVersion The Tableau API version.
     */
    protected $apiVersion;

    /** @var TableauAuth */
    protected $auth;

    /**
     * @var array $openEndpoints Endpoints that do not require a token
     */
    protected $openEndpoints = [
        '/auth/signin',
        'serverinfo',
    ];

    /** @var array */
    protected static $allowedParameterTypes = [
        "boolean",
        "integer",
        "double", // (for historical reasons "double" is returned in case of a float, and not simply "float")
        "string",
        "array",
        "object",
        "resource",
        // "resource (closed)" as of PHP 7.2.0
        "NULL",
        "unknown type",
    ];

    /**
     * HttpClient constructor.
     *
     * @param TableauAuth|null $tableauAuth The auth instance to use for requests
     *
     * @return void
     */
    public function __construct(?TableauAuth $tableauAuth = null)
    {
        // Set the base URL and auth token
        $this->tableauUrl = Config::get('tableau.url');

        // Get the product version and set the API version
        $this->apiVersion = VersionService::getAPIVersion();

        // Set the auth if provided
        if ($tableauAuth) {
            $this->auth = $tableauAuth;
        }
    }

    /**
     * Makes a call to the Tableau API, taking in a callback so the endpoint can be re-tried on a 401002 error
     *
     * @param callable $callback The callback to execute.
     *
     * @return Response
     */
    public function callWithRetry(callable $callback)
    {
        $response = $callback();

        // If the response is not successful, check to see if it's a 401002 error
        if (!$response->successful()) {
            $errorHandler = new ErrorHandler($response);
            if ($errorHandler->errorCode() !== 401002) {
                return $response;
            }

            // Invalidate the token and re-authenticate
            $this->auth->setTokenExpiration(0);
            $this->auth->authenticate();

            // Execute the callback again
            $response = $callback();
        }

        return $response;
    }

    /**
     * Send a DELETE request
     *
     * @param string $endpoint The endpoint to send the request to.
     *
     * @return array|boolean
     */
    public function delete(string $endpoint)
    {
        // Make sure the endpoint is valid
        $this->validateEndpoint($endpoint);

        // Make the call, and retry if necessary (401002 error)
        $response = $this->callWithRetry(function () use ($endpoint) {
            return Http::withHeaders($this->getHeaders())
                ->delete($this->getBaseURL() . $endpoint);
        });


        return $this->handleResponse($response);
    }

    /**
     * Send a GET request
     *
     * @param string $endpoint    The endpoint to send the request to.
     * @param array  $queryParams The query parameters to send with the request.
     *
     * @return array|boolean
     */
    public function get(string $endpoint, array $queryParams = [])
    {
        // Make sure the endpoint is valid
        $this->validateEndpoint($endpoint);

        // Make the call, and retry if necessary (401002 error)
        $response = $this->callWithRetry(function () use ($endpoint, $queryParams) {
            return Http::withHeaders($this->getHeaders())
                ->get($this->getBaseURL() . $endpoint, $queryParams);
        });

        return $this->handleResponse($response);
    }

    /**
     * Return the base URL for the Tableau API
     *
     * @return string
     */
    public function getBaseURL()
    {
        return $this->tableauUrl . '/api/' . $this->apiVersion;
    }

    /**
     * Send a POST request
     *
     * @param string $endpoint The endpoint to send the request to.
     * @param array  $body     The body of the request.
     *
     * @throws Exception If the first key in the body array is 'tsRequest', a common error.
     *
     * @return array|boolean|Response
     */
    public function post(string $endpoint, array $body): array|bool|Response
    {
        // Make sure the endpoint is valid
        $this->validateEndpoint($endpoint);

        // Make sure the first array key is NOT 'tsRequest'
        if (array_key_first($body) === 'tsRequest') {
            throw new Exception('The first key in the body array cannot be "tsRequest"');
        }

        // Make the call, and retry if necessary (401002 error)
        $response = $this->callWithRetry(function () use ($endpoint, $body) {
            return Http::withHeaders($this->getHeaders())
                ->post($this->getBaseURL() . $endpoint, $body);
        });

        return $this->handleResponse($response);
    }

    /**
     * Send a PUT request
     *
     * @param string $endpoint The endpoint to send the request to.
     * @param array  $body     The body of the request.
     *
     * @throws Exception If the first key in the body array is 'tsRequest', a common error.
     *
     * @return array|boolean
     */
    public function put(string $endpoint, array $body = []): array|bool
    {
        // Make sure the endpoint is valid
        $this->validateEndpoint($endpoint);

        // Make the call, and retry if necessary (401002 error)
        $response = $this->callWithRetry(function () use ($endpoint, $body) {
            return Http::withHeaders($this->getHeaders())
                ->put($this->getBaseURL() . $endpoint, $body);
        });

        return $this->handleResponse($response);
    }

    /**
     * Validates the parameters to ensure only allowed parameters are passed, and the types are correct
     *
     * @param array $allowedParameters  The allowed parameters/rules for the endpoint.
     * @param array $parameters         The parameters to validate.
     * @param array $requiredParameters The required parameters for the endpoint.
     *
     * @throws Exception If an error occurs.
     *
     * @return void
     */
    public static function validateParameters(
        array $allowedParameters,
        array $parameters,
        array $requiredParameters = []
    ) {
        // Get the values from the allowed parameters array for this endpoint
        $allowedTypes = array_values($allowedParameters);

        // Make sure the values are all present in globally defined $allowedParameterTypes
        $unsupportedTypes = collect($allowedTypes)->diff(self::$allowedParameterTypes);
        if ($unsupportedTypes->isNotEmpty()) {
            throw new Exception('Unsupported parameter type: ' . $unsupportedTypes->first());
        }

        // Check for missing required parameters
        $missingParameters = collect($requiredParameters)->diff(array_keys($parameters));
        if ($missingParameters->isNotEmpty()) {
            throw new Exception('Missing required parameter(s): ' . json_encode($missingParameters->toArray()));
        }

        foreach ($parameters as $key => $value) {
            // Make sure the parameter is in the allowed list
            if (!array_key_exists($key, $allowedParameters)) {
                throw new Exception('Invalid parameter: ' . $key);
            }

            // Make sure the parameter type is correct
            if (gettype($value) !== $allowedParameters[$key]) {
                throw new Exception('Invalid type for parameter: ' . $key);
            }
        }
    }

    /**
     * Generate the necessary headers, including auth token if available
     *
     * @return array
     */
    protected function getHeaders()
    {
        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->auth && $this->auth->getToken()) {
            $headers['X-Tableau-Auth'] = $this->auth->getToken();
        }

        return $headers;
    }

    /**
     * Handle response using the ErrorHandler
     *
     * This method will return the parsed response or handle any errors
     *
     * @param Response $response The response object.
     *
     * @return array|boolean
     */
    protected function handleResponse(Response $response)
    {
        if (!$response->successful()) {
            $errorHandler = new ErrorHandler($response);
            return $errorHandler->outputMessage();
        }

        // Check the status code of the response
        $statusCode = $response->status();
        if ($statusCode === 204) {
            // Return the full response when a DELETE was successful
            return $response;
        } else {
            // Parse the response
            return ResponseParser::parse($response);
        }
    }

    /**
     * Validates the endpoint to determine if it requires an auth token
     *
     * @param string $endpoint The endpoint to validate.
     *
     * @throws Exception If an error occurs.
     *
     * @return void
     */
    protected function validateEndpoint(string $endpoint)
    {
        if (
            !in_array($endpoint, $this->openEndpoints)
            && ($this->auth && !$this->auth->getToken())
        ) {
            throw new Exception(
                'An auth token is required for the endpoint' . $endpoint . ', use the authenticate() method'
            );
        }
    }
}
