<?php

namespace InterWorks\Tableau\Tests\Traits;

use Illuminate\Support\Facades\Config;
use InterWorks\Tableau\Tests\Mocks\TableauMock;

/**
 * Base test class with built-in mocking capabilities
 *
 * This class provides a foundation for all Tableau API tests with pre-configured
 * mocking, eliminating the need for real server connections.
 */
trait MocksTableauAPI
{
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
        $this->resetMocks();
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

    /**
     * Clear all mocks
     *
     * @return void
     */
    protected function resetMocks(): void
    {
        TableauMock::reset();
    }
}
