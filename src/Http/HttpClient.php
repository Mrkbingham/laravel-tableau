<?php

namespace InterWorks\Tableau\Http;

use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Http\ErrorHandler;

class HttpClient
{
    protected $baseUrl;
    protected $authToken;

    public function __construct($baseUrl, $authToken = null)
    {
        $this->baseUrl   = $baseUrl;
        $this->authToken = $authToken;
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
            ->delete($this->baseUrl . $endpoint);

        return $this->handleResponse($response);
    }

    /**
     * Send a GET request
     */
    public function get($endpoint, $queryParams = [])
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . $endpoint, $queryParams);

        return $this->handleResponse($response);
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
            ->post($this->baseUrl . $endpoint, $body);

        return $this->handleResponse($response);
    }

    /**
     * Send a PUT request
     */
    public function put($endpoint, $body = [])
    {
        $response = Http::withHeaders($this->getHeaders())
            ->put($this->baseUrl . $endpoint, $body);

        return $this->handleResponse($response);
    }
}
