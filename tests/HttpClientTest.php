<?php

use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Http\HttpClient;
use InterWorks\Tableau\Services\VersionService;

beforeEach(function () {
    $this->tableauURL = Config::get('tableau.url');
    dump('TEST Tableau URL: ' . env('TABLEAU_URL'));

    // Initialize the HttpClient (uses the config to set the base URL)
    $this->client = new HttpClient();
    $this->client->setAuthToken('fake-token');
});

describe('HttpClientTest', function () {
    it('can get the base URL', function () {
        $expectedURL = $this->tableauURL . '/api/' . VersionService::getAPIVersion(env('TABLEAU_PRODUCT_VERSION'));


        // Assert the base URL is set correctly
        expect($this->client->getBaseURL())->toBe($expectedURL);
    });

    it('can send a GET request', function () {
        // Mock the GET request
        Http::fake([
            $this->tableauURL . '/api/*' => Http::response([
                'data' => ['item1', 'item2']
            ], 200)
        ]);

        // Send a GET request
        $response = $this->client->get('/test-endpoint');

        // Assert that the response contains the expected data
        expect($response)->toHaveKey('data');
        expect($response['data'])->toHaveCount(2);
    });

    it('can send a POST request', function () {
        // Mock the POST request
        Http::fake([
            $this->tableauURL . '/api/*' => Http::response([
                'success' => true
            ], 201)
        ]);

        // Send a POST request with data
        $response = $this->client->post('/test-endpoint', [
            'name' => 'Test Item'
        ]);

        // Assert the response contains the success message
        expect($response)->toHaveKey('success');
        expect($response['success'])->toBeTrue();
    });

    it('can send a PUT request', function () {
        // Mock the PUT request
        Http::fake([
            $this->tableauURL . '/api/*' => Http::response([
                'updated' => true
            ], 200)
        ]);

        // Send a PUT request with data
        $response = $this->client->put('/test-endpoint', [
            'name' => 'Updated Item'
        ]);

        // Assert the response confirms the update
        expect($response)->toHaveKey('updated');
        expect($response['updated'])->toBeTrue();
    });

    it('can send a DELETE request', function () {
        // Mock the DELETE request
        Http::fake([
            $this->tableauURL . '/api/*' => Http::response('', 204) // No content on success
        ]);

        // Send a DELETE request
        $response = $this->client->delete('/test-endpoint');

        // Assert the request was successful
        expect($response->successful())->toBeTrue();
    });

    it('can validate parameters', function () {
        // Define the expected parameters
        $allowedParameters = [
            'includeExtract' => 'boolean',
        ];

        // Define the valid parameters
        $validParams = [
            'includeExtract' => true,
        ];

        // Define the invalid parameters
        $requiredParams = ['includeExtract'];

        // Assert that the valid parameters pass validation (no exceptions are thrown)
        HttpClient::validateParameters($allowedParameters, $validParams);

        // Throws an exception when passing in required parameters
        expect(function () use ($allowedParameters, $requiredParams) {
            HttpClient::validateParameters($allowedParameters, $requiredParams, $requiredParams);
        })->toThrow(
            Exception::class,
            'Missing required parameter(s): ' . json_encode($requiredParams)
        );
    });

    it('throws an exception for invalid parameters', function () {
        // Define the expected parameters
        $allowedParameters = [
            'includeExtract' => 'boolean',
        ];
        // Set an invalid parameter type
        $invalidAllowedParameters = [
            'includeExtract' => 'bool', // Should be boolean
        ];

        // Define the invalid parameters that violate the expected type
        $parametersWithInvalidType = [
            'includeExtract' => 'true',
        ];

        // Define the invalid parameters that violate the expected type
        $parametersWithInvalidKey = [
            'fakeParam' => 'boolean',
        ];

        // Assert that the invalid parameters fail validation (an exception is thrown)
        expect(function () use ($allowedParameters, $parametersWithInvalidType) {
            HttpClient::validateParameters($allowedParameters, $parametersWithInvalidType);
        })->toThrow(
            Exception::class,
            'Invalid type for parameter: includeExtract',
        );

        // Assert that the invalid parameter types fail validation (an exception is thrown)
        expect(function () use ($invalidAllowedParameters, $allowedParameters) {
            HttpClient::validateParameters($invalidAllowedParameters, $allowedParameters);
        })->toThrow(
            Exception::class,
            'Unsupported parameter type: bool',
        );

        // Assert that the invalid parameter types fail validation (an exception is thrown)
        $invalidParameterRules = [
            'includeExtract' => 'bool', // Should be "boolean"
        ];
        expect(function () use ($allowedParameters, $parametersWithInvalidKey) {
            HttpClient::validateParameters($allowedParameters, $parametersWithInvalidKey);
        })->toThrow(
            Exception::class,
            'Invalid parameter: fakeParam',
        );
    });
});

