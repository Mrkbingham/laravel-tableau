<?php

namespace InterWorks\Tableau\Tests\Mocks;

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
     */
    public static function init(): void
    {
        self::$tableauUrl = Config::get('tableau.url', 'https://tableau-server.example.com');
        self::loadFixtures();
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

    /**
     * Mock all authentication endpoints
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
     * Mock network errors for testing network failure scenarios
     */
    public static function mockNetworkErrors(): void
    {
        Http::fake([
            self::$tableauUrl . '/api/*' => Http::response('Network error', 500)
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
}
