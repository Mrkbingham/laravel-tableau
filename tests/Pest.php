<?php

use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Requests\Authentication\SignInRequest;
use InterWorks\Tableau\Tests\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use InterWorks\Tableau\Tests\Fixtures\TableauAuthFixture;

uses(TestCase::class)->in(__DIR__);

// Setup the global mock client
MockClient::global([
    // Sign in request
    SignInRequest::class => function (PendingRequest $pendingRequest) {
        return match ($pendingRequest->getConnector()->getAuthType()) {
            AuthType::JWT => new TableauAuthFixture('sign-in/jwt'),
            AuthType::PAT => new TableauAuthFixture('sign-in/pat'),
            AuthType::USERNAME => new TableauAuthFixture('sign-in/username')
        };
    },
]);
