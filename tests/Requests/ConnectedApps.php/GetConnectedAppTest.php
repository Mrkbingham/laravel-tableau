<?php

use InterWorks\Tableau\Data\ConnectedApps\ConnectedApp;
use InterWorks\Tableau\Data\ConnectedApps\ConnectedAppSecret;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Requests\ConnectedApps\GetConnectedAppRequest;
use InterWorks\Tableau\Tableau;
use InterWorks\Tableau\Tests\Fixtures\TableauFixture;
use Saloon\Http\Faking\MockClient;

beforeEach(function () {
    $this->connectedAppId = '483518ba-feb5-3ebe-aa2a-028b2e851a30';
});

describe('GetConnectedAppTest', function () {
    test('can get a connected app by ID', function () {
        $tableau = new Tableau(AuthType::PAT);

        $mockClient = new MockClient([
            GetConnectedAppRequest::class => new TableauFixture('connected-apps/get-connected-app')
        ]);

        $getConnectedAppRequest = new GetConnectedAppRequest($this->connectedAppId);
        $response = $tableau->send($getConnectedAppRequest, $mockClient);

        expect($getConnectedAppRequest->resolveEndpoint())->toBe("/sites/:siteId/connected-apps/direct-trust/{$this->connectedAppId}");
        expect($response->status())->toBe(200);
    });
});
