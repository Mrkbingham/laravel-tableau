<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\TableauApi\Exceptions\ApiException;
use InterWorks\Tableau\TableauApi\TableauAuth;

beforeEach(function () {
    $this->auth = new TableauAuth();
});

it('authenticates successfully and returns a token', function () {
    // Perform authentication
    $token = $this->auth->authenticate('username', 'password');

    // Assert that the returned token is correct
    expect($token)->toBe('test-token');
    expect($this->auth->getSiteId())->toBe('site-id');
});

it('throws exception on authentication failure', function () {
    // Modify the config to use an erroneous username and password
    Config::set('tableau.credentials.username', 'wrong-username');
    Config::set('tableau.credentials.password', 'wrong-password');
    $this->auth = new TableauAuth();

    // Expect the ApiException to be thrown
    $this->expectException(ApiException::class);
    $this->expectExceptionMessage('Invalid credentials');
    $this->expectExceptionCode(401);

    // Attempt to authenticate with invalid credentials
    $this->auth->authenticate('wrong-username', 'wrong-password');
});

it('handles network errors gracefully', function () {
    // Simulate a network error
    Http::fake([
        'tableau.com/api/*' => Http::response('Network error', 500)
    ]);

    // Expect the ApiException to be thrown
    $this->expectException(ApiException::class);
    $this->expectExceptionMessage('Network error');
    $this->expectExceptionCode(500);

    // Attempt to authenticate, which should fail
    $this->auth->authenticate('username', 'password');
});

it('can reuse authentication token', function () {
    // Perform authentication
    $this->auth->authenticate(
        Config::get('tableau.credentials.username'),
        Config::get('tableau.credentials.password')
    );

    // Ensure the token is stored
    $originalToken = $this->auth->getToken();
    expect($originalToken)->toNotBeEmpty();

    // Perform authentication
    $this->auth->authenticate(
        Config::get('tableau.credentials.username'),
        Config::get('tableau.credentials.password')
    );

    // Assert that the reused token is correct
    expect($this->auth->getToken())->toBe($originalToken);
});
