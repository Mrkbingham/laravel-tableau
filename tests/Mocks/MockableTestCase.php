<?php

namespace InterWorks\Tableau\Tests\Mocks;

use Illuminate\Support\Facades\Config;
use InterWorks\Tableau\Tests\Mocks\TableauMock;
use InterWorks\Tableau\Tests\TestCase;

/**
 * Base test class with built-in mocking capabilities
 *
 * This class provides a foundation for all Tableau API tests with pre-configured
 * mocking, eliminating the need for real server connections.
 */
abstract class MockableTestCase extends TestCase
{
    /**
     * Set up before each test
     *
     * This initializes the TableauMock system and sets up test configuration.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up test configuration
        $this->setupTestConfig();

        // Initialize mocking system
        TableauMock::init();
    }

    /**
     * Tear down after each test
     *
     * This resets the TableauMock state to ensure clean tests.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Reset mocks after each test
        TableauMock::reset();

        parent::tearDown();
    }

    /**
     * Set up test-specific configuration
     *
     * @return void
     */
    protected function setupTestConfig(): void
    {
        Config::set('tableau.url', 'https://tableau-server.example.com');
        Config::set('tableau.site_name', 'test-site');
        Config::set('tableau.product_version', '2024.3');
        Config::set('tableau.credentials.pat_name', 'test-pat');
        Config::set('tableau.credentials.pat_secret', 'test-secret');
        Config::set('tableau.credentials.username', 'test-user');
        Config::set('tableau.credentials.password', 'test-password');
        Config::set('tableau.token_expiry', 240);
    }

    /**
     * Enable all standard mocks
     *
     * @return void
     */
    protected function enableAllMocks(): void
    {
        TableauMock::mockAll();
    }

    /**
     * Enable authentication mocks only
     *
     * @return void
     */
    protected function enableAuthMocks(): void
    {
        TableauMock::mockAuth();
    }

    /**
     * Enable workbook mocks only
     *
     * @return void
     */
    protected function enableWorkbookMocks(): void
    {
        TableauMock::mockWorkbooks();
    }

    /**
     * Enable error response mocks for testing error handling
     *
     * @return void
     */
    protected function enableErrorMocks(): void
    {
        TableauMock::mockErrorResponses();
    }

    /**
     * Enable network error mocks for testing network failures
     *
     * @return void
     */
    protected function enableNetworkErrorMocks(): void
    {
        TableauMock::mockNetworkErrors();
    }

    /**
     * Get mock workbook data
     *
     * @return array
     */
    protected function getMockWorkbook(): array
    {
        return TableauMock::getMockWorkbookData();
    }

    /**
     * Get mock site data
     *
     * @return array
     */
    protected function getMockSite(): array
    {
        return TableauMock::getMockSiteData();
    }

    /**
     * Mock authentication failure
     *
     * @return void
     */
    protected function mockAuthFailure(): void
    {
        Config::set('tableau.credentials.username', 'wrong-username');
        Config::set('tableau.credentials.password', 'wrong-password');
        TableauMock::mockAuth();
    }

    /**
     * Mock re-authentication scenario
     *
     * @return void
     */
    protected function mockReAuthScenario(): void
    {
        TableauMock::mockReAuth();
    }

    /**
     * Create a custom mock for specific testing needs
     *
     * @param string  $endpoint The API endpoint to mock.
     * @param array   $response The mock response data.
     * @param integer $status   The HTTP status code to return (default is 200).
     *
     * @return void
     */
    protected function customMock(string $endpoint, array $response, int $status = 200): void
    {
        TableauMock::customMock($endpoint, $response, $status);
    }
}
