<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Exceptions\APIException;
use InterWorks\Tableau\Http\HttpClient;

beforeEach(function () {
    // This test mocks all responses, so set the config to a fake URL
    Config::set('tableau.url', 'https://tableau.com/api');

    // Initialize the HttpClient (uses the config to set the base URL)
    $this->client = new HttpClient();
});

describe('APIException', function () {
    it('throws an APIException for 404 Not Found', function () {
        // Mock the API response for a 404 Not Found error
        Http::fake([
            $this->baseUrl . '/*' => Http::response('Resource not found', 404)
        ]);

        // Expect the APIException to be thrown
        $this->expectException(APIException::class);
        $this->expectExceptionMessage('Resource not found');
        $this->expectExceptionCode(404);

        // Make a request that should trigger a 404 error
        $this->client->get($this->baseUrl . '/non-existent-resource');
    });

    it('throws an APIException for 401 Unauthorized', function () {
        // Mock the API response for a 401 Unauthorized error
        Http::fake([
            $this->baseUrl . '/*' => Http::response('Unauthorized access', 401)
        ]);

        // Expect the APIException to be thrown
        $this->expectException(APIException::class);
        $this->expectExceptionMessage('Unauthorized: (0) Unknown Tableau Error. No error details available.');
        $this->expectExceptionCode(401);

        // Make a request that should trigger a 401 error
        $this->client->get($this->baseUrl . '/protected-resource');
    });

    it('throws an APIException for 500 Internal Server Error', function () {
        // Mock the API response for a 500 Internal Server Error
        Http::fake([
            $this->baseUrl . '/*' => Http::response('Internal server error', 500)
        ]);

        // Expect the APIException to be thrown
        $this->expectException(APIException::class);
        $this->expectExceptionMessage('Internal server error');
        $this->expectExceptionCode(500);

        // Make a request that should trigger a 500 error
        $this->client->get($this->baseUrl . '/server-error-resource');
    });

    it('returns the correct error message and code', function () {
        // Mock the API response for a 403 Forbidden error
        Http::fake([
            $this->baseUrl . '/*' => Http::response('Access forbidden', 403)
        ]);

        // Capture the thrown APIException
        try {
            $this->client->get($this->baseUrl . '/forbidden-resource');
        } catch (APIException $e) {
            // Assert the error message and code
            expect($e->getErrorMessage())->toBe('Forbidden: (0) Unknown Tableau Error. No error details available.');
            expect($e->getStatusCode())->toBe(403);
        }
    });

    it('handles request exceptions gracefully', function () {
        // Mock a network failure or invalid request
        Http::fake([
            $this->baseUrl . '/*' => function ($request) {
                return Http::response('Request failed', 500);
            }
        ]);

        // Expect the APIException to be thrown
        $this->expectException(APIException::class);
        $this->expectExceptionMessage('Request failed');

        // Make a request that will fail
        $this->client->get($this->baseUrl . '/failed-request');
    });
});
