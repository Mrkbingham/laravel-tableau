<?php

use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\API\Workbooks;
use InterWorks\Tableau\TableauAPI;

beforeEach(function () {
    $this->siteId = Config::get('tableau.site_id');

    // Initialize the Workbooks class with a token
    $this->tableau = new TableauAPI();
});

describe('WorkbooksTest', function () {
    it('can addTags', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can deleteTag', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can delete', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can download', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can downloadEncryptedKeychain', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can downloadPDF', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can downloadPowerPoint', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can downloadRevision', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can getDowngradeInfo', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can getRevisions', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can publish', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can queryViews', function () {
        // Not implemented
    })->skip('not yet implemented');
    it('can getWorkbookById', function () {
        // Call the method to get a workbook by ID
        $workbookContentURL = env('TABLEAU_SERVER_WORKBOOK_CONTENT_URL');
        $workbookID = env('TABLEAU_SERVER_WORKBOOK_ID');
        $workbookName = env('TABLEAU_SERVER_WORKBOOK_NAME');
        $response = $this->tableau->workbooks()->getWorkbookById($workbookID);

        // Assert the response contains the correct workbook data
        expect($response)->toHaveKey('workbook');
        expect($response['workbook']['contentUrl'])->toBe($workbookContentURL);
        expect($response['workbook']['id'])->toBe($workbookID);
        expect($response['workbook']['name'])->toBe($workbookName);
    });
    it('can getWorkbookByContentURL', function () {
        // Call the method to get a workbook by ID
        $workbookContentURL = env('TABLEAU_SERVER_WORKBOOK_CONTENT_URL');
        $workbookID = env('TABLEAU_SERVER_WORKBOOK_ID');
        $workbookName = env('TABLEAU_SERVER_WORKBOOK_NAME');
        $response = $this->tableau->workbooks()->getWorkbookByContentURL($workbookContentURL);
        dd($response);

        // Assert the response contains the correct workbook data
        expect($response)->toHaveKey('workbook');
        expect($response['workbook']['contentUrl'])->toBe($workbookContentURL);
        expect($response['workbook']['id'])->toBe($workbookID);
        expect($response['workbook']['name'])->toBe($workbookName);
    });
});
