<?php

namespace InterWorks\Tableau\API;

use InterWorks\Tableau\Http\HttpClient;
use InterWorks\Tableau\Http\ResponseParser;

class Workbooks
{
    protected $client;
    protected $token;

    public function __construct($token)
    {
        // Initialize the HTTP client with the token for authentication
        $this->client = new HttpClient(config('tableau.server_url') . '/api/' . config('tableau.api_version'), $token);
        $this->token = $token;
    }

    /**
     * Get all workbooks from the Tableau server
     */
    public function getAllWorkbooks($siteId, $queryParams = [])
    {
        $endpoint = "/sites/{$siteId}/workbooks";
        $response = $this->client->get($endpoint, $queryParams);

        // Parse the response and return as an array
        return ResponseParser::parse($response);
    }

    /**
     * Get a specific workbook by ID
     */
    public function getWorkbookById($siteId, $workbookId)
    {
        $endpoint = "/sites/{$siteId}/workbooks/{$workbookId}";
        $response = $this->client->get($endpoint);

        // Parse the response and return as an array
        return ResponseParser::parse($response);
    }

    /**
     * Download a workbook in .twbx format by its ID
     */
    public function downloadWorkbook($siteId, $workbookId)
    {
        $endpoint = "/sites/{$siteId}/workbooks/{$workbookId}/content";
        $response = $this->client->get($endpoint);

        // Return the raw response body (binary data for the .twbx file)
        return $response;
    }

    /**
     * Update workbook information
     */
    public function updateWorkbook($siteId, $workbookId, $data = [])
    {
        $endpoint = "/sites/{$siteId}/workbooks/{$workbookId}";
        $response = $this->client->put($endpoint, $data);

        // Parse the response and return as an array
        return ResponseParser::parse($response);
    }

    /**
     * Delete a workbook by ID
     */
    public function deleteWorkbook($siteId, $workbookId)
    {
        $endpoint = "/sites/{$siteId}/workbooks/{$workbookId}";
        $response = $this->client->delete($endpoint);

        // Return the response status (empty body on success)
        return $response->successful();
    }
}
