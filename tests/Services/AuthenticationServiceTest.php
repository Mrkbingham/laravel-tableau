<?php

use InterWorks\Tableau\Tableau;

it('authenticates with Tableau server', function () {
    $tableau = new Tableau();
    $tableau->authenticate();

    expect($tableau->authToken)->not->toBeEmpty();
});

it('requires base URL to be set', function () {
    Config::set('tableau.base_url', null);

    $tableau = new Tableau();

    expect(function () use ($tableau) {
        $tableau->authenticate();
    })->toThrow();
});

it('gets the list of workbooks', function () {
    $tableau = new TableauService();
    $tableau->authenticate();

    $workbooks = $tableau->getWorkbooks();

    expect($workbooks['workbooks']['workbook'])->toBeArray();
});
