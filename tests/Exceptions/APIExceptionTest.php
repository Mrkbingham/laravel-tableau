<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Exceptions\ApiException;
use InterWorks\Tableau\Http\HttpClient;

beforeEach(function () {
    $this->baseUrl = 'https://tableau.com/api';
    $this->token = 'test-token';

    // Initialize the HttpClient with base URL and token
    $this->client = new HttpClient($this->baseUrl, $this->token);
});

describe('APIException', function () {
    it('throws an ApiException for 404 Not Found', function () {
        // Mock the API response for a 404 Not Found error
        Http::fake([
            'tableau.com/api/*' => Http::response('Resource not found', 404)
        ]);

        // Expect the ApiException to be thrown
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Resource not found');
        $this->expectExceptionCode(404);

        // Make a request that should trigger a 404 error
        $this->client->get('/non-existent-resource');
    });

    it('throws an ApiException for 401 Unauthorized', function () {
        // Mock the API response for a 401 Unauthorized error
        Http::fake([
            'tableau.com/api/*' => Http::response('Unauthorized access', 401)
        ]);

        // Expect the ApiException to be thrown
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Unauthorized access');
        $this->expectExceptionCode(401);

        // Make a request that should trigger a 401 error
        $this->client->get('/protected-resource');
    });

    it('throws an ApiException for 500 Internal Server Error', function () {
        // Mock the API response for a 500 Internal Server Error
        Http::fake([
            'tableau.com/api/*' => Http::response('Internal server error', 500)
        ]);

        // Expect the ApiException to be thrown
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Internal server error');
        $this->expectExceptionCode(500);

        // Make a request that should trigger a 500 error
        $this->client->get('/server-error-resource');
    });

    it('returns the correct error message and code', function () {
        // Mock the API response for a 403 Forbidden error
        Http::fake([
            'tableau.com/api/*' => Http::response('Access forbidden', 403)
        ]);

        // Capture the thrown ApiException
        try {
            $this->client->get('/forbidden-resource');
        } catch (ApiException $e) {
            // Assert the error message and code
            expect($e->getErrorMessage())->toBe('Forbidden: Access forbidden');
            expect($e->getStatusCode())->toBe(403);
        }
    });

    it('handles request exceptions gracefully', function () {
        // Mock a network failure or invalid request
        Http::fake([
            'tableau.com/api/*' => function ($request) {
                return Http::response('Request failed', 500);
            }
        ]);

        // Expect the ApiException to be thrown
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Request failed');

        // Make a request that will fail
        $this->client->get('/failed-request');
    });
});
