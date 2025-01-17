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
    it('can add and delete tags', function () {
        $testTag = 'restAPITestTag';
        // Call the method to get a workbook by ID
        $workbookID = env('TABLEAU_WORKBOOK_ID');
        $response = $this->tableau->workbooks()->addTags($workbookID, [$testTag]);

        // Assert the response contains the correct workbook data
        expect($response)->toHaveKey('tags');
        expect($response['tags'])->toHaveKey('tag');
        $tags = collect($response['tags']['tag']);
        expect($tags->contains('label', $testTag))->toBeTrue();

        // Delete the tag
        $response = $this->tableau->workbooks()->deleteTag($workbookID, $testTag);
    });

    it('can delete', function () {
        // Not implemented
    })->skip('not yet implemented');

    it('can download', function () {
        // Call the method to get a workbook by ID
        $workbookID = env('TABLEAU_WORKBOOK_ID');
        $response = $this->tableau->workbooks()->download($workbookID);

        // Make sure the response is not empty
        expect($response)->not->toBeEmpty();
    });

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
        // Call the method to get a workbook by ID
        $workbookID = env('TABLEAU_WORKBOOK_ID');
        $response = $this->tableau->workbooks()->queryViews($workbookID);

        // Assert the response contains the correct view data
        expect($response)->toHaveKey('views');
        expect($response['views'])->toHaveKey('view');
        foreach($response['views']['view'] as $viewData) {
            expect($viewData)->toHaveKey('contentUrl');
            expect($viewData)->toHaveKey('id');
            expect($viewData)->toHaveKey('name');
        }
    });

    it('can getWorkbookById', function () {
        // Call the method to get a workbook by ID
        $workbookContentURL = env('TABLEAU_WORKBOOK_CONTENT_URL');
        $workbookID = env('TABLEAU_WORKBOOK_ID');
        $workbookName = env('TABLEAU_WORKBOOK_NAME');
        $response = $this->tableau->workbooks()->getWorkbookById($workbookID);

        // Assert the response contains the correct workbook data
        expect($response)->toHaveKey('workbook');
        expect($response['workbook']['contentUrl'])->toBe($workbookContentURL);
        expect($response['workbook']['id'])->toBe($workbookID);
        expect($response['workbook']['name'])->toBe($workbookName);
    });

    it('can getWorkbookByContentURL', function () {
        // Call the method to get a workbook by ID
        $workbookContentURL = env('TABLEAU_WORKBOOK_CONTENT_URL');
        $workbookID = env('TABLEAU_WORKBOOK_ID');
        $workbookName = env('TABLEAU_WORKBOOK_NAME');
        $response = $this->tableau->workbooks()->getWorkbookByContentURL($workbookContentURL);

        // Assert the response contains the correct workbook data
        expect($response)->toHaveKey('workbook');
        expect($response['workbook']['contentUrl'])->toBe($workbookContentURL);
        expect($response['workbook']['id'])->toBe($workbookID);
        expect($response['workbook']['name'])->toBe($workbookName);
    });
});
