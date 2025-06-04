<?php

use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Requests\Authentication\SignInRequest;
use InterWorks\Tableau\Requests\ConnectedApps\GetConnectedAppRequest;
use InterWorks\Tableau\Requests\ConnectedApps\ListConnectedAppsRequest;
use InterWorks\Tableau\Tests\Fixtures\TableauFixture;
use InterWorks\Tableau\Tests\TestCase;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

uses(TestCase::class)->in(__DIR__);

// Set this to false if you'd like to override the mock to test against a real Tableau instance.
$useMock = true;
if ($useMock) {
    // Setup the global mock client
    MockClient::global([
        // Sign in request
        SignInRequest::class => function (PendingRequest $pendingRequest) {
            return match ($pendingRequest->getConnector()->getAuthType()) {
                AuthType::JWT => new TableauFixture('sign-in/jwt'),
                AuthType::PAT => new TableauFixture('sign-in/pat'),
                AuthType::USERNAME => new TableauFixture('sign-in/username')
            };
        },
        // Connected Apps requests
        GetConnectedAppRequest::class => new TableauFixture('connected-apps/get-connected-app'),
        ListConnectedAppsRequest::class => new TableauFixture('connected-apps/list-connected-apps')
    ]);
}

