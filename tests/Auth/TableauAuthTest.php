<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Exceptions\ApiException;
use InterWorks\Tableau\TableauAPI;

beforeEach(function () {
    $this->tableauURL = env('TABLEAU_URL');

    // Create a generic Tableau connection to re-use
    $this->tableau = new TableauAPI();
});

describe('TableauAuthTest', function() {
    it('can authenticate successfully and return a token', function () {
        // Assert that the returned token is correct
        expect($this->tableau->auth()->getToken())->not->toBeEmpty();
    });

    it('can authenticate with username', function () {
        $tableauWithUsername = new TableauAPI(AuthType::USERNAME);

        // Assert that the returned token is correct
        expect($tableauWithUsername->auth()->getToken())->not->toBeEmpty();
    });

    it('throws an exception on authentication failure', function () {
        // Modify the config to use an erroneous username and password
        Config::set('tableau.credentials.username', 'wrong-username');
        Config::set('tableau.credentials.password', 'wrong-password');

        // Expect the ApiException to be thrown
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Unauthorized: (401001)');
        $this->expectExceptionCode(401);

        new TableauAPI(AuthType::USERNAME);
    });

    it('handles network errors gracefully', function () {
        // Simulate a network error
        Http::fake([
            $this->tableauURL . '/api/*' => Http::response('Network error', 500)
        ]);

        // Expect the ApiException to be thrown
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Network error');
        $this->expectExceptionCode(500);

        // Attempt to authenticate, which should fail
        new TableauAPI();
    });

    it('can reuse authentication token', function () {
        // Ensure the token is stored
        $originalToken = $this->tableau->auth()->getToken();
        expect($originalToken)->not->toBeEmpty();

        // Re-authenticate
        $this->tableau->auth()->authenticate();

        // Assert that the reused token is correct
        expect($this->tableau->auth()->getToken())->toBe($originalToken);
    });
});
