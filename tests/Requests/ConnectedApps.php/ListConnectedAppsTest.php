<?php

use InterWorks\Tableau\Data\ConnectedApps\ConnectedApp;
use InterWorks\Tableau\Data\ConnectedApps\ConnectedApplication;
use InterWorks\Tableau\Data\ConnectedApps\ConnectedAppSecret;
use InterWorks\Tableau\Data\ConnectedApps\ConnectedAppsCollection;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Requests\ConnectedApps\ListConnectedAppsRequest;
use InterWorks\Tableau\Tableau;
use InterWorks\Tableau\Tests\Fixtures\TableauFixture;
use Saloon\Http\Faking\MockClient;

describe('ListConnectedAppsTest', function () {
    test('can list connected apps ', function () {
        $tableau = new Tableau(AuthType::PAT);

        $mockClient = new MockClient([
            ListConnectedAppsRequest::class => new TableauFixture('connected-apps/list-connected-apps')
        ]);

        $listConnectedAppsRequest = new ListConnectedAppsRequest();
        $response = $tableau->send($listConnectedAppsRequest, $mockClient);
        expect($response->status())->toBe(200);
    });

    test('can parse connected apps response into DTOs', function () {
        $tableau = new Tableau(AuthType::PAT);

        $mockClient = new MockClient([
            ListConnectedAppsRequest::class => new TableauFixture('connected-apps/list-connected-apps')
        ]);

        $listConnectedAppsRequest = new ListConnectedAppsRequest();
        $response = $tableau->send($listConnectedAppsRequest, $mockClient);

        // Parse the response into DTOs
        $connectedAppsCollection = $response->dto();

        // Validate the collection
        expect($connectedAppsCollection)->toBeInstanceOf(ConnectedAppsCollection::class);
        expect($connectedAppsCollection->all())->toHaveCount(44);

        // Get all apps
        $apps = $connectedAppsCollection->all();

        // Validate first app (with secret)
        $firstApp = $apps->first();
        expect($firstApp)->toBeInstanceOf(ConnectedApp::class);
        expect($firstApp->name)->toBe('test_app__1');
        expect($firstApp->enabled)->toBe(true);
        expect($firstApp->clientId)->toBeString();
        expect($firstApp->projectIds)->toHaveCount(0);
        expect($firstApp->domainSafelist)->toBeNull();
        expect($firstApp->unrestrictedEmbedding)->toBe(true);
        expect($firstApp->createdAt->format('Y-m-d H:i:s'))->toBe('2023-06-16 20:02:28');

        // Validate secret
        expect($firstApp->secrets->all()->first())->toBeInstanceOf(ConnectedAppSecret::class);
        $firstAppSecret = $firstApp->secrets->all()->first();
        expect($firstAppSecret->id)->toBe('528d17d3-8667-3ceb-868c-72fabde49c3e');
        expect($firstAppSecret->createdAt->format('Y-m-d H:i:s'))->toBe('2023-06-16 20:02:29');

        // Validate second app (no secret)
        $secondApp = $apps->get(1);
        expect($secondApp->name)->toBe('test_app__2');
        expect($secondApp->enabled)->toBe(false);
        expect($secondApp->clientId)->toBe('9e9f1da0-773d-3f45-9727-d344335daca4');
        expect($secondApp->unrestrictedEmbedding)->toBe(true);
        expect($secondApp->domainSafelist)->toBeNull();
        expect($secondApp->secrets)->toBeNull();

        // Test collection filtering methods
        $enabledApps = $connectedAppsCollection->enabled();
        expect($enabledApps)->toHaveCount(43);
        expect($enabledApps->first()->name)->toBe('test_app__1');

        $disabledApps = $connectedAppsCollection->disabled();
        expect($disabledApps)->toHaveCount(1);
        expect($disabledApps->pluck('name')->toArray())->toEqual([
            'test_app__2',
        ]);
    });
});
