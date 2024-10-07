<?php

use InterWorks\Tableau\TableauAPI;

it('returns the correct API version', function () {
    // Loop through the version map
    foreach (Tableau::$apiVersionMap as $productVersion => $apiVersion) {
        Config::set('tableau.version', $productVersion);

        // Create a new Tableau instance
        $tableau = new TableauAPI();

        // Set the Tableau version
        $tableau->tableauVersion = $productVersion;

        // Get the API version
        $apiVersion = $tableau->getApiVersion();

        // Assert that the API version is correct
        expect($apiVersion)->toBe($apiVersion);
    }
});

it('can authenticate', function () {
    $tableau = new TableauAPI();

    expect($tableau->auth()->getToken())->not->toBeEmpty();
});

it('can sign out', function () {
    $tableau = new TableauAPI();

    // Sign out
    $tableau->auth()->signOut();

    expect($tableau->auth()->getToken())->toBeEmpty();
});

