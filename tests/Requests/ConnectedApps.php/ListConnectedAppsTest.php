<?php

use InterWorks\Tableau\Data\Authentication\PATAuthentication;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Facades\TableauAPI;
use InterWorks\Tableau\Requests\Authentication\SignInRequest;
use InterWorks\Tableau\Requests\ConnectedApps\ListConnectedAppsRequest;
use InterWorks\Tableau\Requests\Views\QueryViewsForSiteRequest;
use InterWorks\Tableau\Tableau;

describe('ListConnectedAppsTest', function () {
    test('can list connected apps ', function () {
        $tableau = new Tableau(AuthType::PAT);

        $listConnectedAppsRequest = new ListConnectedAppsRequest();
        $response = $tableau->send($listConnectedAppsRequest);
        expect($response->status())->toBe(200);
    });
});
