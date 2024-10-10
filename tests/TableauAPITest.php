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

    it('can re-authenticate when using invalid token', function () {
        // Authenticate and retrieve the token
        $tableau = new TableauAPI();
        $originalToken = $tableau->auth()->getToken();
        // Logout
        $tableau->auth()->signOut();

        // Manually re-set the token to use the original token
        $tableau->auth()->setToken($originalToken);

        // Try to make a request with the old token
        $workbookData = $tableau->workbooks()->getWorkbookById(env('TABLEAU_WORKBOOK_ID'));

        // Make sure there is a new token
        expect($tableau->auth()->getToken())->not->toBe($originalToken);

        // Make sure the request was successful
        expect($workbookData)->toHaveKey('workbook');
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
