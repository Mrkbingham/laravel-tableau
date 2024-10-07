<?php

namespace InterWorks\Tableau\Http;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Http\ErrorHandler;
use InterWorks\Tableau\Services\VersionService;

class HttpClient
{
    protected $tableauUrl;
    protected $apiVersion;
    protected $authToken;

    public function __construct()
    {
        // Set the base URL and auth token
        $this->tableauUrl = Config::get('tableau.url');

        // Get the product version and set the API version
        $productVersion = Config::get('tableau.product_version');
        $this->apiVersion = VersionService::getApiVersion($productVersion);
    }

    /**
     * Send a DELETE request
     *
     *
     * @param string $endpoint
     *
     * @return \Illuminate\Http\Client\Response $response
     */
    public function delete($endpoint)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->delete($this->getBaseURL() . $endpoint);

        return $this->handleResponse($response);
    }

    /**
     * Send a GET request
     */
    public function get($endpoint, $queryParams = [])
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->getBaseURL() . $endpoint, $queryParams);

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
     * Generate the necessary headers, including auth token if available
     */
    protected function getHeaders()
    {
        $headers = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->authToken) {
            $headers['X-Tableau-Auth'] = $this->authToken;
        }

        return $headers;
    }

    /**
     * Handle response using the ErrorHandler
     *
     * This method will return the parsed response or handle any errors
     *
     * @param \Illuminate\Http\Client\Response $response
     *
     * @return array|boolean
     */
    protected function handleResponse($response)
    {
        if (!$response->successful()) {
            return ErrorHandler::handle($response);
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
     * Send a POST request
     */
    public function post($endpoint, $body = [])
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->getBaseURL() . $endpoint, $body);

        return $this->handleResponse($response);
    }

    /**
     * Send a PUT request
     */
    public function put($endpoint, $body = [])
    {
        $response = Http::withHeaders($this->getHeaders())
            ->put($this->getBaseURL() . $endpoint, $body);

        return $this->handleResponse($response);
    }
}