<?php

use InterWorks\Tableau\TableauAPI;

describe('TableauAPITest', function() {
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
});
