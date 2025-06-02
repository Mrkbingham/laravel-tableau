<?php

use Illuminate\Http\Client\ConnectionException;
use InterWorks\Tableau\Enums\AuthType;
use InterWorks\Tableau\Exceptions\APIException;
use InterWorks\Tableau\TableauAPI;
use InterWorks\Tableau\Tests\Mocks\TableauMock;

beforeEach(function () {
    // Setup mocked endpoints
    $this->enableAllMocks();
});

describe('ErrorHandlingTest', function () {
    it('handles network timeout scenarios', function () {
        TableauMock::mockNetworkFailure();

        expect(fn() => new TableauAPI())->toThrow(ConnectionException::class);
    });

    it('handles malformed API responses', function () {
        $this->resetMocks();
        TableauMock::mockMalformedResponse();

        expect(fn() => new TableauAPI())->toThrow(APIException::class);
    });

    it('handles permission denied scenarios', function () {
        // Signin
        $api = new TableauAPI();

        // Mock a permission denied response for deleting a workbook
        $this->resetMocks();
        TableauMock::mockPermissionDenied("403004");

        try {
            $api->workbooks()->delete('e4ea0ab5-bf97-42e6-936e-eb654b7a2aab');
            // $api->workbooks()->delete('unauthorized-workbook');
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(403);
            expect($e->getErrorMessage())->toContain('Forbidden');
        }
    });

    it('handles authentication with invalid credentials', function () {
        TableauMock::mockAuthenticationFailure();

        try {
            new TableauAPI();
            expect()->toBeFalse(); // Should not reach here
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(401);
            expect($e->getErrorMessage())->toContain('Unauthorized');
        }
    });

    it('handles workbook download failures', function () {
        TableauMock::mockAuthentication();
        TableauMock::mockDownloadFailure();

        $api = new TableauAPI(AuthType::PAT);

        try {
            $api->downloadWorkbook('problematic-workbook');
            expect()->toBeFalse(); // Should not reach here
        } catch (APIException $e) {
            expect($e->getStatusCode())->toBe(500);
            expect($e->getErrorMessage())->toContain('Internal server error');
        }
    });

    it('handles connection reset scenarios', function () {
        TableauMock::mockConnectionReset();

        expect(fn() => new TableauAPI())->toThrow(APIException::class);
    });
});
