<?php

use InterWorks\Tableau\TableauAPI;

describe('TableauAPITest', function() {
    it('can authenticate with product version', function () {
        if (!Config::get('tableau.product_version')) {
            throw new Exception('Product version not set in config, this test requires a product version');
        }
        $tableau = new TableauAPI();

        expect($tableau->auth()->getToken())->not->toBeEmpty();
    });

    it('can authenticate without product version', function () {
        Config::set('tableau.product_version', null);
        $tableau = new TableauAPI();

        expect($tableau->auth()->getToken())->not->toBeEmpty();
    });

    it('can sign out', function () {
        $tableau = new TableauAPI();

        // Mock the sign out so we don't invalidate the existing token for other sessions
        Http::fake([
            Config::get('tableau.url') . '/api/*' => Http::response([
                'success' => true
            ], 204)
        ]);

        // Sign out
        $tableau->auth()->signOut();

        expect($tableau->auth()->getToken())->toBeEmpty();
    });
});
