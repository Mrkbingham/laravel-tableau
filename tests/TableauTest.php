<?php

use InterWorks\Tableau\Tableau;

it('returns the correct API version', function () {
    // Loop through the version map
    foreach (Tableau::$apiVersionMap as $productVersion => $apiVersion) {
        Config::set('tableau.version', $productVersion);

        // Create a new Tableau instance
        $tableau = new Tableau();

        // Set the Tableau version
        $tableau->tableauVersion = $productVersion;

        // Get the API version
        $apiVersion = $tableau->getApiVersion();

        // Assert that the API version is correct
        expect($apiVersion)->toBe($apiVersion);
    }
});
