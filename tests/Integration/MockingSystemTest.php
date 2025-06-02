<?php

use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Exceptions\APIException;
use InterWorks\Tableau\Services\ServerInfoService;
use InterWorks\Tableau\TableauAPI;
use InterWorks\Tableau\Tests\Mocks\TableauMock;

describe('MockingSystemTests', function () {
    it('successfully mocks authentication flow', function () {
        TableauMock::mockAuthentication();

        $api = new TableauAPI(AuthType::PAT);

        expect($api->auth()->getToken())->toBe('mock-auth-token-12345');
        expect($api->auth()->getSiteId())->toBe('12345678-1234-1234-1234-123456789012');
    });

    it('handles authentication failures properly', function () {
        TableauMock::mockAuthenticationFailure();

        expect(fn() => new TableauAPI(AuthType::PAT))->toThrow(APIException::class);
    });

    it('successfully mocks workbook operations', function () {
        TableauMock::mockAuthentication();
        TableauMock::mockWorkbookOperations();

        $api = new TableauAPI(AuthType::PAT);

        // Test getting workbooks
        $workbooks = $api->getWorkbooks();
        expect($workbooks)->toBeArray();
        expect($workbooks['workbooks']['workbook'])->toHaveCount(2);

        // Test getting a specific workbook
        $workbook = $api->getWorkbook('workbook-123');
        expect($workbook['workbook']['id'])->toBe('workbook-123');
        expect($workbook['workbook']['name'])->toBe('Sample Workbook 1');
    });

    it('handles server info requests correctly', function () {
        TableauMock::init();
        TableauMock::mockServerInfo();

        $serverInfo = ServerInfoService::fetchServerInfo();
        expect($serverInfo['serverInfo']['productVersion']['value'])->toBe('2024.3.0');
        expect($serverInfo['serverInfo']['restApiVersion'])->toBe('3.24');
    });

    it('mocks token expiration and re-authentication', function () {
        TableauMock::mockTokenExpiration();

        $api = new TableauAPI(AuthType::PAT);

        // Now mock token expiration for next request
        TableauMock::mockReAuthentication();

        // This should trigger re-authentication
        $workbooks = $api->getWorkbooks();
        expect($workbooks)->toBeArray();
    });

    it('handles network failures gracefully', function () {
        TableauMock::mockNetworkFailure();

        expect(fn() => new TableauAPI(AuthType::PAT))
            ->toThrow(\InterWorks\Tableau\Exceptions\APIException::class);
    });

    it('provides consistent error responses', function () {
        TableauMock::init();
        TableauMock::mockAuthentication();
        TableauMock::mockErrorResponses();

        $api = new TableauAPI();

        // Test 404 error
        try {
            $api->workbooks()->getWorkbookById('non-existent-workbook');
        } catch (\InterWorks\Tableau\Exceptions\APIException $e) {
            expect($e->getStatusCode())->toBe(404);
            expect($e->getErrorMessage())->toContain('Resource not found');
        }

        // Test 401 error
        try {
            $api->getWorkbooks(); // Without authentication
        } catch (\InterWorks\Tableau\Exceptions\APIException $e) {
            expect($e->getStatusCode())->toBe(401);
            expect($e->getErrorMessage())->toContain('Unauthorized');
        }
    });

    it('allows testing of workbook download scenarios', function () {
        TableauMock::mockAuthentication();
        TableauMock::mockWorkbookDownload();

        $api = new TableauAPI(AuthType::PAT);

        $downloadData = $api->downloadWorkbook('workbook-123');
        expect($downloadData)->toBe('Mock PDF content for testing download functionality');
    });

    it('supports workbook revision testing', function () {
        TableauMock::mockAuthentication();
        TableauMock::mockWorkbookRevisions();

        $api = new TableauAPI(AuthType::PAT);

        $revisions = $api->getWorkbookRevisions('workbook-123');
        expect($revisions['revisions']['revision'])->toHaveCount(3);
        expect($revisions['revisions']['revision'][0]['revisionNumber'])->toBe('3');
    });

    it('enables testing of workbook views', function () {
        TableauMock::mockAuthentication();
        TableauMock::mockWorkbookViews();

        $api = new TableauAPI(AuthType::PAT);

        $views = $api->getWorkbookViews('workbook-123');
        expect($views['views']['view'])->toHaveCount(2);
        expect($views['views']['view'][0]['name'])->toBe('Dashboard 1');
    });
});
