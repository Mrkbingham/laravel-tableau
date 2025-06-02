<?php

use InterWorks\Tableau\TableauAPI;
use InterWorks\Tableau\Tests\Mocks\TableauMock;
use InterWorks\Tableau\Exceptions\APIException;

describe('Error Handling Scenarios', function () {

    it('handles network timeout scenarios', function () {
        TableauMock::mockNetworkFailure();

        $api = new TableauAPI();

        expect(fn() => $api->getServerInfo())
            ->toThrow(APIException::class);
    });

    it('handles expired token scenarios', function () {
        // First authenticate successfully
        TableauMock::mockAuthentication();
        $api = new TableauAPI();
        $api->authenticateWithPAT('test-pat-token', 'test-content-url');

        // Now mock token expiration
        TableauMock::mockTokenExpiration();

        expect(fn() => $api->getWorkbooks())
            ->toThrow(APIException::class);
    });

    it('handles malformed API responses', function () {
        TableauMock::init();
        TableauMock::mockMalformedResponse();

        $api = new TableauAPI();

        expect(fn() => $api->getServerInfo())
            ->toThrow(APIException::class);
    });

    it('handles rate limiting scenarios', function () {
        TableauMock::init();
        TableauMock::mockRateLimitError();

        $api = new TableauAPI();

        try {
            $api->getServerInfo();
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(429);
            expect($e->getErrorMessage())->toContain('Too Many Requests');
        }
    });

    it('handles server maintenance scenarios', function () {
        TableauMock::init();
        TableauMock::mockServerMaintenance();

        $api = new TableauAPI();

        try {
            $api->getServerInfo();
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(503);
            expect($e->getErrorMessage())->toContain('Service Unavailable');
        }
    });

    it('handles invalid workbook ID requests', function () {
        TableauMock::mockAuthentication();
        TableauMock::mockErrorResponses();

        $api = new TableauAPI();
        $api->authenticateWithPAT('test-pat-token', 'test-content-url');

        try {
            $api->getWorkbook('invalid-workbook-id');
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(404);
            expect($e->getErrorMessage())->toContain('Resource not found');
        }
    });

    it('handles permission denied scenarios', function () {
        TableauMock::mockAuthentication();
        TableauMock::init();
        TableauMock::mockPermissionDenied();

        $api = new TableauAPI();
        $api->authenticateWithPAT('test-pat-token', 'test-content-url');

        try {
            $api->deleteWorkbook('restricted-workbook');
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(403);
            expect($e->getErrorMessage())->toContain('Forbidden');
        }
    });

    it('handles authentication with invalid credentials', function () {
        TableauMock::mockAuthenticationFailure();

        $api = new TableauAPI();

        try {
            $api->authenticateWithCredentials('invalid-user', 'invalid-pass', 'site');
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(401);
            expect($e->getErrorMessage())->toContain('Unauthorized');
        }
    });

    it('handles workbook download failures', function () {
        TableauMock::mockAuthentication();
        TableauMock::init();
        TableauMock::mockDownloadFailure();

        $api = new TableauAPI();
        $api->authenticateWithPAT('test-pat-token', 'test-content-url');

        try {
            $api->downloadWorkbook('problematic-workbook');
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(500);
            expect($e->getErrorMessage())->toContain('Internal server error');
        }
    });

    it('handles connection reset scenarios', function () {
        TableauMock::init();
        TableauMock::mockConnectionReset();

        $api = new TableauAPI();

        expect(fn() => $api->getServerInfo())
            ->toThrow(APIException::class);
    });
});
