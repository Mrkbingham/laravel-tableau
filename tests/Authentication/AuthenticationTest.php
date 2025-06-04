<?php

use InterWorks\Tableau\Data\Authentication\PATAuthentication;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Requests\Authentication\SignInRequest;
use InterWorks\Tableau\Requests\Views\QueryViewsForSiteRequest;
use InterWorks\Tableau\Tableau;
use InterWorks\Tableau\TableauAuthenticator;

describe('AuthenticationTest', function () {
    test('connector can authenticate with username and password', function () {
        $tableau = new Tableau(AuthType::USERNAME);

        $signInRequest = new SignInRequest($tableau->getAuth());
        $response = $tableau->send($signInRequest);
        expect($response->status())->toBe(200);
    });

    test('connector can authenticate with Personal Access Token', function () {
        $tableau = new Tableau(AuthType::PAT);

        $signInRequest = new SignInRequest($tableau->getAuth());
        $response = $tableau->send($signInRequest);
        expect($response->status())->toBe(200);
    });

    test('connector can authenticate with JWT', function () {
        $tableau = new Tableau(AuthType::JWT);

        $signInRequest = new SignInRequest($tableau->getAuth());
        $response = $tableau->send($signInRequest);
        expect($response->status())->toBe(200);
    });
});
