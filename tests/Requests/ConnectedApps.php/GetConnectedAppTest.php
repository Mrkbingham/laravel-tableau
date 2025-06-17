<?php

use InterWorks\Tableau\Data\ConnectedApps\ConnectedApp;
use InterWorks\Tableau\Data\ConnectedApps\ConnectedAppSecret;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Requests\ConnectedApps\GetConnectedApp;
use InterWorks\Tableau\Tableau;
use InterWorks\Tableau\Tests\Fixtures\TableauFixture;
use Saloon\Http\Faking\MockClient;

beforeEach(function () {
    $this->connectedAppId = '483518ba-feb5-3ebe-aa2a-028b2e851a30';
});

describe('GetConnectedAppTest', function () {
    test('can get a connected app by ID', function () {
        $tableau = new Tableau(AuthType::PAT);

        $getConnectedAppRequest = new GetConnectedApp($this->connectedAppId);
        $response = $tableau->send($getConnectedAppRequest);

        expect($getConnectedAppRequest->resolveEndpoint())->toBe("/sites/:siteId/connected-apps/direct-trust/{$this->connectedAppId}");
        expect($response->status())->toBe(200);
    });

    test('can parse single connected app response into DTO', function () {
        $tableau = new Tableau(AuthType::PAT);

        $getConnectedAppRequest = new GetConnectedApp($this->connectedAppId);
        $response = $tableau->send($getConnectedAppRequest);

        // Parse the response into DTO
        $connectedApp = $response->dto();

        // Validate the app DTO
        expect($connectedApp)->toBeInstanceOf(ConnectedApp::class);
        expect($connectedApp->name)->toBe('test_app__1');
        expect($connectedApp->enabled)->toBe(true);
        expect($connectedApp->clientId)->toBe('7c68a315-86d1-3174-979d-cfb4c6af8ed8');
        expect($connectedApp->projectIds)->toBeArray();
        expect($connectedApp->domainSafelist)->toBeNull();
        expect($connectedApp->unrestrictedEmbedding)->toBe(true);
        expect($connectedApp->createdAt->format('Y-m-d H:i:s'))->toBe('2023-06-16 20:02:28');

        // Validate secrets collection
        expect($connectedApp->secrets)->not->toBeNull();
        expect($connectedApp->secrets->all())->toHaveCount(1);

        $secret = $connectedApp->secrets->all()->first();
        expect($secret)->toBeInstanceOf(ConnectedAppSecret::class);
        expect($secret->id)->toBe($this->connectedAppId);
        expect($secret->createdAt->format('Y-m-d H:i:s'))->toBe('2023-06-16 20:02:29');
    });
});
