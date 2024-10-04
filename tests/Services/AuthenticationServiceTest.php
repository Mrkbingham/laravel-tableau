<?php

use InterWorks\Tableau\Tableau;

it('authenticates with Tableau server', function () {
    $tableau = new Tableau();

    expect($tableau->getAuthToken())->not->toBeEmpty();
});

it('can sign out', function () {
    $tableau = new Tableau();

    // Sign out
    $tableau->signOut();

    expect($tableau->getAuthToken())->toBeEmpty();
});
