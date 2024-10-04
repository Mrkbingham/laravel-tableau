<?php

use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\API\Workbooks;
use InterWorks\Tableau\TableauAPI;

beforeEach(function () {
    $this->siteId = 'test-site-id';
    $this->token  = 'test-token';

    // Initialize the Workbooks class with a token
    $this->tableau = new TableauAPI();
});

describe('WorkbooksAPI', function () {
    it('can fetch all workbooks', function () {
        // Call the method to get all workbooks
        $response = $this->tableau->workbooks()->getAllWorkbooks($this->siteId);

        // Assert the response contains the workbooks array
        expect($response)->toHaveKey('workbooks');
        expect($response['workbooks'])->toHaveCount(2);
    });

    it('can fetch a single workbook by id', function () {
        // Call the method to get a workbook by ID
        $workbookId = 'workbook1';
        $response = $this->tableau->workbooks()->getWorkbookById($this->siteId, $workbookId);

        // Assert the response contains the correct workbook data
        expect($response)->toHaveKey('workbook');
        expect($response['workbook']['id'])->toBe('workbook1');
        expect($response['workbook']['name'])->toBe('Workbook 1');
    });

    it('can delete a workbook', function () {
        // Call the method to delete a workbook
        $workbookId = 'workbook1';
        $response = $this->tableau->workbooks()->deleteWorkbook($this->siteId, $workbookId);

        // Assert the response is successful
        expect($response)->toBeTrue();
    });
});
