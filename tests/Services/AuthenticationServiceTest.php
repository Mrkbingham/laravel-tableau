<?php

use InterWorks\Tableau\Tableau;

it('authenticates with Tableau server', function () {
    $tableau = new Tableau();

    expect($tableau->getAuthToken())->not->toBeEmpty();
});

it('gets the list of workbooks', function () {
    $tableau = new TableauService();
    $tableau->authenticate();

    $workbooks = $tableau->getWorkbooks();

    expect($workbooks['workbooks']['workbook'])->toBeArray();
});
