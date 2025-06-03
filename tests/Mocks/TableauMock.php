<?php

namespace InterWorks\Tableau\Tests\Mocks;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use ReflectionObject;

/**
 * TableauMock provides a comprehensive mocking system for Tableau API responses
 * 
 * This class allows tests to mock all Tableau API endpoints without requiring
 * actual server connections, improving test reliability and performance.
 */
class TableauMock
{
    /** @var array */
    protected static $fixtures = [];
    /** @var string */
    protected static $tableauUrl;
    /** @var string */
    protected static $apiVersion = '3.24';

    /**
     * Initialize the mock system
     *
     * @return void
     */
    public static function init(): void
    {
        self::$tableauUrl = Config::get('tableau.url', 'https://tableau-server.example.com');
        self::loadFixtures();
    }

    /**
     * Mock all authentication endpoints
     *
     * @return void
     */
    public static function mockAuth(): void
    {
        // Successful PAT authentication
        Http::fake([
            self::$tableauUrl . '/api/*/auth/signin' => function ($request) {
                $body = $request->data();

                // Check if using PAT authentication
                if (isset($body['credentials']['personalAccessTokenName'])) {
                    if ($body['credentials']['personalAccessTokenName'] === 'wrong-pat' ||
                        $body['credentials']['personalAccessTokenSecret'] === 'wrong-secret') {
                        return Http::response(self::getFixture('auth', 'signin_error_401'), 401);
                    }
                    return Http::response(self::getFixture('auth', 'signin_success_pat'), 200);
                }

                // Check username/password authentication
                if (isset($body['credentials']['name'])) {
                    if ($body['credentials']['name'] === 'wrong-username' ||
                        $body['credentials']['password'] === 'wrong-password') {
                        return Http::response(self::getFixture('auth', 'signin_error_401'), 401);
                    }
                    return Http::response(self::getFixture('auth', 'signin_success_username'), 200);
                }

                return Http::response(self::getFixture('auth', 'signin_error_400'), 400);
            }
        ]);

        // Mock signout
        Http::fake([
            self::$tableauUrl . '/api/*/auth/signout' => Http::response('', 204)
        ]);
    }

    /**
     * Mock all workbook endpoints
     *
     * @return void
     */
    public static function mockWorkbooks(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        // Get workbook by ID
        Http::fake([
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/workbooks/*' => function ($request) {
                $uri = $request->url();

                // Handle different workbook endpoints
                if (str_contains($uri, '/views')) {
                    return Http::response(self::getFixture('workbooks', 'workbook_views'), 200);
                }

                if (str_contains($uri, '/revisions')) {
                    return Http::response(self::getFixture('workbooks', 'workbook_revisions'), 200);
                }

                if (str_contains($uri, '/content')) {
                    // Mock file download - return binary-like content
                    return Http::response('mock-workbook-content', 200, [
                        'Content-Type' => 'application/octet-stream',
                        'Content-Disposition' => 'attachment; filename="workbook.twbx"'
                    ]);
                }

                if (str_contains($uri, '/pdf')) {
                    return Http::response('mock-pdf-content', 200, [
                        'Content-Type' => 'application/pdf'
                    ]);
                }

                if (str_contains($uri, '/powerpoint')) {
                    return Http::response('mock-pptx-content', 200, [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                    ]);
                }

                if (str_contains($uri, '/downgradeInfo')) {
                    return Http::response(self::getFixture('workbooks', 'workbook_downgrade_info'), 200);
                }

                // Handle DELETE requests
                if ($request->method() === 'DELETE') {
                    return Http::response('', 204);
                }

                // Handle workbook not found
                if (str_contains($uri, 'nonexistent-workbook')) {
                    return Http::response(self::getFixture('workbooks', 'workbook_not_found'), 404);
                }

                // Default workbook response
                return Http::response(self::getFixture('workbooks', 'workbook_sample'), 200);
            }
        ]);
    }

    /**
     * Mock server info endpoint
     */
    public static function mockServerInfo(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*/serverinfo' => Http::response(
                self::getFixture('general', 'server_info'),
                200
            )
        ]);
    }

    /**
     * Mock error responses for testing error handling
     */
    public static function mockErrorResponses(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*/server-error-resource' => Http::response(
                self::getFixture('general', 'error_responses')['500'],
                500
            ),
            self::$tableauUrl . '/api/*/forbidden-resource' => Http::response(
                'Access forbidden',
                403
            ),
            self::$tableauUrl . '/api/*/non-existent-resource' => Http::response(
                'Resource not found',
                404
            ),
            self::$tableauUrl . '/api/*/protected-resource' => Http::response(
                'Unauthorized access',
                401
            ),
            self::$tableauUrl . '/api/*/failed-request' => Http::response(
                'Request failed',
                500
            ),
        ]);
    }

    /**
     * Mock network errors for testing network failure scenarios
     */
    public static function mockNetworkErrors(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' => Http::response('Network error', 500)
        ]);
    }

    /**
     * Mock all common endpoints at once
     */
    public static function mockAll(): void
    {
        self::init();
        self::mockAuth();
        self::mockWorkbooks();
        self::mockServerInfo();
    }

    /**
     * Mock specific test scenarios
     */
    public static function mockTestEndpoints(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*/test-endpoint' => function ($request) {
                switch ($request->method()) {
                    case 'GET':
                        return Http::response(['data' => ['item1', 'item2']], 200);
                    case 'POST':
                        return Http::response(['success' => true], 201);
                    case 'PUT':
                        return Http::response(['updated' => true], 200);
                    case 'DELETE':
                        return Http::response('', 204);
                    default:
                        return Http::response(['error' => 'Method not allowed'], 405);
                }
            }
        ]);
    }

    /**
     * Reset the HTTP facade's fake callbacks
     *
     * @return void
     */
    public static function reset(): void
    {
        $reflection = new ReflectionObject(Http::getFacadeRoot());
        $property = $reflection->getProperty('stubCallbacks');
        $property->setAccessible(true);
        $property->setValue(Http::getFacadeRoot(), collect());
    }

    /**
     * Create a custom mock response for specific testing needs
     */
    public static function customMock(string $endpoint, array $response, int $status = 200): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' . $endpoint => Http::response($response, $status)
        ]);
    }

    /**
     * Mock authentication with specific token
     */
    public static function mockAuthWithToken(string $token): void
    {
        $authResponse = self::getFixture('auth', 'signin_success_pat');
        $authResponse['credentials']['token'] = $token;

        Http::fake([
            self::$tableauUrl . '/api/*/auth/signin' => Http::response($authResponse, 200)
        ]);
    }

    /**
     * Mock re-authentication scenario (expired token)
     */
    public static function mockReAuth(): void
    {
        // We need to override the previous mocks to ensure a clean state
        self::reset();

        $callCount = 0;

        Http::fake([
            self::$tableauUrl . '/api/*' => function ($request) use (&$callCount) {
                $callCount++;

                // First call returns 401002 (expired token)
                if ($callCount === 1 && !str_contains($request->url(), '/auth/signin')) {
                    return Http::response(self::getFixture('general', 'error_responses')['401002'], 401);
                }

                // Auth endpoints work normally but return a NEW token
                if (str_contains($request->url(), '/auth/signin')) {
                    $authResponse = self::getFixture('auth', 'signin_success_pat');
                    // Generate a new token for re-authentication
                    $authResponse['credentials']['token'] = 'mock-auth-token-new-' . time();
                    return Http::response($authResponse, 200);
                }

                // Second call succeeds with new token
                return Http::response(self::getFixture('workbooks', 'workbook_sample'), 200);
            }
        ]);
    }

    /**
     * Get mock workbook data for testing
     */
    public static function getMockWorkbookData(): array
    {
        return [
            'id' => '12345678-1234-1234-1234-123456789012',
            'contentUrl' => 'sample-workbook',
            'name' => 'Sample Workbook'
        ];
    }

    /**
     * Get mock site data for testing
     */
    public static function getMockSiteData(): array
    {
        return [
            'id' => '12345678-1234-1234-1234-123456789012',
            'contentUrl' => 'test-site'
        ];
    }

    /**
     * Mock authentication failure
     */
    public static function mockAuthenticationFailure(): void
    {
        self::reset();
        Http::fake([
            self::$tableauUrl . '/api/*/auth/signin' => Http::response(
                self::getFixture('auth', 'signin_error_401'),
                401
            )
        ]);
    }

    /**
     * Mock workbook operations
     */
    public static function mockWorkbookOperations(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/workbooks' => Http::response(
                self::getFixture('workbooks', 'workbooks_list'),
                200
            ),
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/workbooks/*' => function ($request) {
                $uri = $request->url();

                if (preg_match('/workbook-123/', $uri)) {
                    return Http::response(self::getFixture('workbooks', 'workbook_sample'), 200);
                }

                return Http::response(self::getFixture('workbooks', 'workbook_not_found'), 404);
            }
        ]);
    }

    /**
     * Mock token expiration scenario
     */
    public static function mockTokenExpiration(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' => function ($request) {
                if (str_contains($request->url(), '/auth/signin')) {
                    return Http::response(self::getFixture('auth', 'signin_success_pat'), 200);
                }

                return Http::response(self::getFixture('general', 'error_responses')['401002'], 401);
            }
        ]);
    }

    /**
     * Mock re-authentication flow
     */
    public static function mockReAuthentication(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' => function ($request) {
                if (str_contains($request->url(), '/auth/signin')) {
                    return Http::response(self::getFixture('auth', 'signin_success_pat'), 200);
                }

                return Http::response(self::getFixture('workbooks', 'workbooks_list'), 200);
            }
        ]);
    }

    /**
     * Mock network failure
     */
    public static function mockNetworkFailure(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' => function ($request) {
                throw new ConnectionException('Network connection failed');
            }
        ]);
    }

    /**
     * Mock workbook download
     */
    public static function mockWorkbookDownload(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/workbooks/*/content' => Http::response(
                'Mock PDF content for testing download functionality',
                200,
                ['Content-Type' => 'application/pdf']
            )
        ]);
    }

    /**
     * Mock workbook revisions
     */
    public static function mockWorkbookRevisions(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/workbooks/*/revisions' => Http::response(
                self::getFixture('workbooks', 'workbook_revisions'),
                200
            )
        ]);
    }

    /**
     * Mock workbook views
     */
    public static function mockWorkbookViews(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/workbooks/*/views' => Http::response(
                self::getFixture('workbooks', 'workbook_views'),
                200
            )
        ]);
    }

    /**
     * Mock malformed response
     */
    public static function mockMalformedResponse(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' => fn() => Http::response(
                'Invalid JSON response {malformed',
                400
            )
        ]);
    }

    /**
     * Mock rate limit error
     */
    public static function mockRateLimitError(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' => Http::response(
                ['error' => ['summary' => 'Too Many Requests', 'detail' => 'Rate limit exceeded']],
                429
            )
        ]);
    }

    /**
     * Mock server maintenance
     */
    public static function mockServerMaintenance(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' => Http::response(
                ['error' => ['summary' => 'Service Unavailable', 'detail' => 'Server is under maintenance']],
                503
            )
        ]);
    }

    /**
     * Mock permission denied
     *
     * @param string|null $errorCode Optional error code to use for the mock response.
     *
     * @return void
     */
    public static function mockPermissionDenied(?string $errorCode = null): void
    {
        $response = self::getFixture('general', 'error_responses')[$errorCode] ?? [
            'error' => [
                'summary' => 'Forbidden',
                'detail'  => 'You do not have permission to access this resource.'
            ]
        ];

        // Sets all API calls to return a 403 Forbidden response
        Http::fake([
            self::$tableauUrl . '/api/*' => Http::response($response, 403)
        ]);
    }

    /**
     * Mock download failure
     */
    public static function mockDownloadFailure(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/workbooks/*/content' => Http::response(
                ['error' => ['summary' => 'Internal server error', 'detail' => 'Failed to generate download']],
                500
            )
        ]);
    }

    /**
     * Mock connection reset
     *
     * This simulates a scenario where the connection to the Tableau Server is reset,
     * useful for testing how the application handles abrupt disconnections.
     *
     * @return void
     */
    public static function mockConnectionReset(): void
    {
        self::reset();
        Http::fake([
            self::$tableauUrl . '/api/*' => fn ($request) => Create::rejectionFor(
                new ConnectException("The connection was reset", $request->toPsrRequest())
            ),
        ]);
    }

    /**
     * Mock Views API endpoints
     */
    public static function mockViewsOperations(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            // List all views on site
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/views' => Http::response(
                self::getFixture('api', 'views_list'),
                200
            ),
            // Get specific view
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/views/*' => function ($request) {
                $uri = $request->url();

                if (preg_match('/view-1-id/', $uri)) {
                    return Http::response(self::getFixture('api', 'view_sample'), 200);
                }

                return Http::response(['error' => ['summary' => 'View not found']], 404);
            }
        ]);
    }

    /**
     * Mock Datasources API endpoints
     */
    public static function mockDatasourcesOperations(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            // List all datasources on site
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/datasources' => Http::response(
                self::getFixture('api', 'datasources_list'),
                200
            ),
            // Get specific datasource
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/datasources/*' => function ($request) {
                $uri = $request->url();

                if (str_contains($uri, '/content')) {
                    // Mock datasource download
                    return Http::response('mock-datasource-content', 200, [
                        'Content-Type' => 'application/octet-stream',
                        'Content-Disposition' => 'attachment; filename="datasource.tdsx"'
                    ]);
                }

                if (preg_match('/datasource-1-id/', $uri)) {
                    return Http::response(self::getFixture('api', 'datasource_sample'), 200);
                }

                // Handle DELETE requests
                if ($request->method() === 'DELETE') {
                    return Http::response('', 204);
                }

                return Http::response(['error' => ['summary' => 'Datasource not found']], 404);
            }
        ]);
    }

    /**
     * Mock Users API endpoints
     */
    public static function mockUsersOperations(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            // List all users on site
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/users' => Http::response(
                self::getFixture('api', 'users_list'),
                200
            ),
            // Get specific user
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/users/*' => function ($request) {
                $uri = $request->url();

                if (preg_match('/user-1-id/', $uri)) {
                    return Http::response(self::getFixture('api', 'user_sample'), 200);
                }

                // Handle DELETE requests
                if ($request->method() === 'DELETE') {
                    return Http::response('', 204);
                }

                // Handle PUT/PATCH requests (update user)
                if (in_array($request->method(), ['PUT', 'PATCH'])) {
                    return Http::response(self::getFixture('api', 'user_sample'), 200);
                }

                return Http::response(['error' => ['summary' => 'User not found']], 404);
            }
        ]);
    }

    /**
     * Mock Projects API endpoints
     */
    public static function mockProjectsOperations(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            // List all projects on site
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/projects' => Http::response(
                self::getFixture('api', 'projects_list'),
                200
            ),
            // Get specific project
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/projects/*' => function ($request) {
                $uri = $request->url();

                if (preg_match('/87654321-4321-4321-4321-210987654321/', $uri)) {
                    return Http::response(self::getFixture('api', 'project_sample'), 200);
                }

                // Handle DELETE requests
                if ($request->method() === 'DELETE') {
                    return Http::response('', 204);
                }

                // Handle PUT/PATCH requests (update project)
                if (in_array($request->method(), ['PUT', 'PATCH'])) {
                    return Http::response(self::getFixture('api', 'project_sample'), 200);
                }

                return Http::response(['error' => ['summary' => 'Project not found']], 404);
            }
        ]);
    }

    /**
     * Mock View image/PDF export endpoints
     */
    public static function mockViewExports(): void
    {
        $siteId = self::getFixture('auth', 'signin_success_pat')['credentials']['site']['id'];

        Http::fake([
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/views/*/image' => Http::response(
                'mock-view-image-content',
                200,
                ['Content-Type' => 'image/png']
            ),
            self::$tableauUrl . '/api/*/sites/' . $siteId . '/views/*/pdf' => Http::response(
                'mock-view-pdf-content',
                200,
                ['Content-Type' => 'application/pdf']
            )
        ]);
    }

    /**
     * Mock comprehensive API operations for all resources
     */
    public static function mockAllResources(): void
    {
        self::init();
        self::mockAuthentication();
        self::mockWorkbookOperations();
        self::mockViewsOperations();
        self::mockDatasourcesOperations();
        self::mockUsersOperations();
        self::mockProjectsOperations();
        self::mockServerInfo();
    }

    /**
     * Load all fixture files
     *
     * @return void
     */
    protected static function loadFixtures(): void
    {
        $fixturesPath = __DIR__ . '/../Fixtures/';

        $fixtureFiles = [
            'auth' => 'auth_responses.json',
            'workbooks' => 'workbook_responses.json',
            'general' => 'general_responses.json',
            'api' => 'api_responses.json',
        ];

        foreach ($fixtureFiles as $key => $file) {
            $content = file_get_contents($fixturesPath . $file);
            self::$fixtures[$key] = json_decode($content, true);
        }
    }

    /**
     * Get a fixture by category and key
     *
     * @param string $category The category of the fixture (e.g., 'auth', 'workbooks').
     * @param string $key      The specific key within the category.
     *
     * @return array
     */
    public static function getFixture(string $category, string $key): array
    {
        return self::$fixtures[$category][$key] ?? [];
    }
}
