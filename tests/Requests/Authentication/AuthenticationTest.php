<?php

use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Requests\Authentication\SignIn;
use InterWorks\Tableau\Tableau;

describe('AuthenticationTest', function () {
    test('connector can authenticate with username and password', function () {
        $tableau = new Tableau(AuthType::USERNAME);

        $signInRequest = new SignIn($tableau->getAuth());
        $response = $tableau->send($signInRequest);
        expect($response->status())->toBe(200);
    });

    test('connector can authenticate with Personal Access Token', function () {
        $tableau = new Tableau(AuthType::PAT);

        $signInRequest = new SignIn($tableau->getAuth());
        $response = $tableau->send($signInRequest);
        expect($response->status())->toBe(200);
    });

    test('connector can authenticate with JWT', function () {
        $tableau = new Tableau(AuthType::JWT);

        $signInRequest = new SignIn($tableau->getAuth());
        $response = $tableau->send($signInRequest);
        expect($response->status())->toBe(200);
    });
});
