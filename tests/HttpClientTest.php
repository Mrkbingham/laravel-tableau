<?php

use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Http\HttpClient;
use InterWorks\Tableau\Services\VersionService;

beforeEach(function () {
    $this->tableauURL = env('TABLEAU_URL');

    // Initialize the HttpClient with base URL and token
    $this->client = new HttpClient();
});

describe('HttpClient', function () {
    it('can get the base URL', function () {
        $expectedURL = $this->tableauURL . '/api/' . VersionService::getApiVersion(env('TABLEAU_PRODUCT_VERSION'));


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
});

