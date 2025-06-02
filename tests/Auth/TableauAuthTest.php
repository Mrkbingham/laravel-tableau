<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Exceptions\APIException;
use InterWorks\Tableau\TableauAPI;
use InterWorks\Tableau\Tests\Mocks\TableauMock;

beforeEach(function () {
    $this->tableauURL = env('TABLEAU_URL');

    // Initialize mocking system
    TableauMock::init();
    TableauMock::mockAuth();

    // Create a generic Tableau connection to re-use
    $this->tableau = new TableauAPI();
});

describe('TableauAuthTest', function() {
    it('can authenticate successfully and return a token', function () {
        // Assert that the returned token is correct
        expect($this->tableau->auth()->getToken())->not->toBeEmpty();
        expect($this->tableau->auth()->getToken())->toBe('mock-auth-token-username-67890');
    });

    it('can authenticate with username', function () {
        $tableauWithUsername = new TableauAPI(AuthType::USERNAME);

        // Assert that the returned token is correct
        expect($tableauWithUsername->auth()->getToken())->not->toBeEmpty();
        expect($tableauWithUsername->auth()->getToken())->toBe('mock-auth-token-username-67890');
    });

    it('throws an exception on authentication failure', function () {
        // Modify the config to use an erroneous username and password
        Config::set('tableau.credentials.username', 'wrong-username');
        Config::set('tableau.credentials.password', 'wrong-password');

        // Expect the APIException to be thrown
        $this->expectException(APIException::class);
        $this->expectExceptionMessage('Unauthorized: (401001)');
        $this->expectExceptionCode(401);

        new TableauAPI(AuthType::USERNAME);
    });

    it('handles network errors gracefully', function () {
        // Reset mocks and simulate a network error
        TableauMock::reset();
        TableauMock::mockNetworkErrors();

        // Expect the APIException to be thrown
        $this->expectException(APIException::class);
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
