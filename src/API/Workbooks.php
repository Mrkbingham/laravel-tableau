<?php

namespace InterWorks\Tableau\API;

use Exception;
use InterWorks\Tableau\Auth\TableauAuth;
use InterWorks\Tableau\Http\HttpClient;

class Workbooks
{
    /** @var HttpClient */
    protected $client;
    /** @var TableauAuth */
    protected $auth;
    /** @var string */
    protected $siteID;

    /**
     * Workbooks API constructor.
     *
     * @param TableauAuth $tableauAuth The TableauAuth object to use for authentication.
     *
     * @return void
     */
    public function __construct(TableauAuth $tableauAuth)
    {
        // Initialize the HTTP client with the token for authentication
        $this->client = $tableauAuth->client();
        $this->auth = $tableauAuth;
        // Set the siteID
        $this->siteID = $tableauAuth->getSiteID();
    }

    /**
     * Adds one or more tags to the specified workbook.
     *
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#add_tags_to_workbook
     *
     * @param string $workbookID The ID of the workbook to add tags to.
     * @param array  $tags       An array of tags to add to the workbook.
     *
     * @throws Exception This method is not yet implemented.
     *
     * @return void
     */
    public function addTags(string $workbookID, array $tags)
    {
        throw new Exception('workbooks()->addTags() is not yet implemented');
    }

    /**
     * Deletes a tag from the specified workbook.
     *
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#delete_tag_from_workbook
     *
     * @param string $workbookID The ID of the workbook to remove the tag from.
     * @param string $tag        The tag to remove from the workbook.
     *
     * @throws Exception This method is not yet implemented.
     *
     * @return void
     */
    public function deleteTag(string $workbookID, string $tag)
    {
        throw new Exception('workbooks()->deleteTag() is not yet implemented');
    }

    /**
     * Deletes a workbook.
     *
     * When a workbook is deleted, all of its assets are also deleted, including associated views,
     * data connections, and so on.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#delete_workbook
     *
     * @param string $workbookID The ID of the workbook to delete.
     *
     * @return array
     */
    public function delete(string $workbookID)
    {
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}";
        $response = $this->client->delete($endpoint);

        // Return the response status (empty body on success)
        return $response->successful();
    }

    /**
     * Downloads a workbook in .twb or .twbx format.
     *
     * Security note: Content in .twb or .twbx files downloaded using this method is stored in plain text. All data,
     * including filter values that may give semantic clues to the data, will be readable by anyone who opens the files.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#download_workbook
     *
     * @param string $workbookID The ID of the workbook to download.
     * @param array  $parameters If true, the workbook is downloaded with its extract.
     *               - includeExtract (bool): If true, the workbook is downloaded with its extract.
     *
     * @return array
     */
    public function download(string $workbookID, array $parameters = [])
    {
        // Make sure the parameters are valid
        $allowedParameters = ['includeExtract' => 'bool'];
        HttpClient::validateParameters($allowedParameters, $parameters);

        // Set the endpoint and make the request
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}/content";
        $response = $this->client->get($endpoint, $parameters);

        // Return the raw response body (binary data for the .twbx file)
        return $response;
    }

    /**
     * Gets an encrypted version of the embedded credentials from the workbook. These credentials can only be migrated
     * to the linked destination Tableau Cloud or Tableau Server.
     *
     * Before you use this method, you must create public and private keys for resources with embedded credentials. For
     * more information, see https://help.tableau.com/current/server/en-us/cmt-migration_embedded_credentials.htm
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#download_workbook_encrypted_keychain
     *
     * @param string $workbookID      The ID of the workbook to download.
     * @param array  $destinationData The destination site URL namespace, site LUID, and server URL.
     *               - destinationSiteUrlNamespace (string): The namespace of the destination site.
     *               - destinationSiteLuid (string): The LUID of the destination site.
     *               - destinationServerUrl (string): The URL of the destination server.
     *
     * @throws Exception If the destinationData array is missing required keys.
     *
     * @return array
     */
    public function downloadEncryptedKeychain(string $workbookID, array $destinationData)
    {
        // Ensure the payload data is valid
        $requiredKeys = ['destinationSiteUrlNamespace', 'destinationSiteLuid', 'destinationServerUrl'];
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $destinationData)) {
                throw new Exception("Missing required key '{$key}' in destinationData array");
            }
        }

        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}/retrieveKeychain";
        $response = $this->client->post($endpoint, $destinationData);

        // Return the response body
        return $response;
    }

    /**
     * Downloads a .pdf containing images of the sheets that the user has permission to view in a workbook.
     *
     * Download Images/PDF permissions must be enabled for the workbook (true by default). If Show sheets in tabs is not
     * selected for the workbook, only the default tab will appear in the .pdf file.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#download_workbook_pdf
     *
     * @param string $workbookID The ID of the workbook to download.
     * @param array  $parameters The query parameters to include in the request.
     *               - maxAge (int): The maximum age of the cached PDF in minutes.
     *               - type (string): The type of page layout. Possible values: A3, A4, A5, B5, Executive, Folio,
     *                 Ledger, Legal, Letter, Note, Quarto, or Tabloid.
     *               - orientation (string): The orientation of the PDF, default is Portrait. Possible values: Portrait,
     *                 Landscape.
     *
     * @return array
     */
    public function downloadPDF(string $workbookID, array $parameters = []) {
        // Make sure the parameters are valid
        $allowedParameters = ['maxAge' => 'integer', 'type' => 'string', 'orientation' => 'string'];
        HttpClient::validateParameters($allowedParameters, $parameters);

        // Set the endpoint and make the request
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}/pdf";
        $response = $this->client->get($endpoint, $parameters);

        return $response;
    }

    /**
     * Downloads a PowerPoint (.pptx) file containing slides with images of the sheets that the user has permission to
     * view in a workbook.
     *
     * Download Images/PDF permissions must be enabled for the workbook (true by default). If Show
     * sheets in tabs is not selected for the workbook, only the default tab will appear in the .pptx file.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#download_workbook_powerpoint
     *
     * @param string $workbookID The ID of the workbook to download.
     * @param array  $parameters The query parameters to include in the request.
     *               - maxAge (int): The maximum age of the cached PPTX in minutes.
     *
     * @return array
     */
    public function downloadPowerPoint(string $workbookID, array $parameters = []) {
        // Make sure the parameters are valid
        $allowedParameters = ['maxAge' => 'integer'];
        HttpClient::validateParameters($allowedParameters, $parameters);

        // Set the endpoint and make the request
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}/powerpoint";
        $response = $this->client->get($endpoint, $parameters);

        return $response;
    }

    /**
     * Downloads a specific version of a workbook in .twb or .twbx format.
     *
     * This method is available only if version history is enabled for the specified site.
     * Security note: Content in .twb or .twbx files downloaded using this method is stored in plain text. All data,
     * including filter values that may give semantic clues to the data, will be readable by anyone who opens the files.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#download_workbook_revision
     *
     * @param string  $workbookID     The ID of the workbook to download.
     * @param integer $revisionNumber The revision number of the workbook to download.
     * @param array   $parameters     The query parameters to include in the request.
     *                - includeExtract (bool): The maximum age of the cached PPTX in minutes.
     *
     * @return array
     */
    public function downloadRevision(string $workbookID, int $revisionNumber, array $parameters = []) {
        // Make sure the parameters are valid
        $allowedParameters = ['includeExtract' => 'bool'];
        HttpClient::validateParameters($allowedParameters, $parameters);

        // Set the endpoint and make the request
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}/revisions/{$revisionNumber}/content";
        $response = $this->client->get($endpoint, $parameters);

        return $response;
    }

    /**
     * Retrieves the downgrade info for a given workbook.
     *
     * Returns a list of the features that would be impacted, and the severity of the impact, when a workbook is
     * exported as a downgraded version (for instance, exporting a v2019.3 workbook to a v10.5 version).
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#get_workbook_downgrade_info
     *
     * @param string $workbookID The ID of the workbook.
     * @param array  $parameters Optional query parameters.
     *              - productVersion (string): The target-version of the product to downgrade to.
     *
     * @return array
     */
    public function getDowngradeInfo(string $workbookID, array $parameters = [])
    {
        // Make sure the parameters are valid
        $allowedParameters = ['productVersion' => 'string'];
        $requiredParameters = ['productVersion'];
        HttpClient::validateParameters($allowedParameters, $parameters, $requiredParameters);

        // Set the endpoint and make the request
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}/downgradeInfo";
        $response = $this->client->get($endpoint, $parameters);

        return $response;
    }

    /**
     * Returns a list of revision information (history) for the specified workbook.
     *
     * Note: This method is available only if version history is enabled for the specified site.
     * To get a specific version of the workbook from revision history, use the downloadRevision() method.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#get_workbook_revisions
     *
     * @param string $workbookID The ID of the workbook.
     * @param array  $parameters Optional query parameters.
     *               - pageSize (int): The number of revisions to return per page.
     *               - pageNumber (int): The page number of revisions to return.
     *
     * @return array
     */
    public function getRevisions(string $workbookID, array $parameters = [])
    {
        // Make sure the parameters are valid
        $allowedParameters = ['pageSize' => 'integer', 'pageNumber' => 'integer'];
        HttpClient::validateParameters($allowedParameters, $parameters);

        // Set the endpoint and make the request
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}/revisions";
        $response = $this->client->get($endpoint, $parameters);

        return $response;
    }

    /**
     * Publishes a workbook on the specified site.
     *
     * To make changes to a published workbook, call updateWorkbook() or updateWorkbookConnection().
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#publish_workbook
     *
     * @param string $file       The path to the workbook file to publish.
     * @param string $name       The name of the workbook to publish.
     * @param array  $parameters Optional query parameters.
     *               - overwrite (bool): If true, the workbook is overwritten if it already exists.
     *               - skipConnectionCheck (bool): Skips checking if a non-published connection of a workbook is
     *                 reachable.
     *               - uploadSessionId (string): The ID of the upload session to use for the workbook.
     *               - workbookType (string): The type of workbook to publish. Possible values: twb, twbx.
     *               - asJob (bool): If true, the request is run asynchronously.
     *
     * @throws Exception This method is not yet implemented.
     *
     * @return void
     */
    public function publish(string $file, string $name, array $parameters = [])
    {
        // TODO: Add validation to the payload
        throw new Exception('workbooks()->publish() is not yet implemented');
    }

    /**
     * Returns all the views for the specified workbook, optionally including usage statistics.
     *
     * Note: After you create a resource, the server updates its search index. If you make a query immediately to see a
     * new resource, the query results might not be up to date.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#query_views_for_workbook
     *
     * @param string $workbookID The ID of the workbook.
     * @param array  $parameters Optional query parameters.
     *               - includeUsageStatistics (bool): If true, includes usage statistics for the views.
     *
     * @return array
     */
    public function queryViews(string $workbookID, array $parameters = [])
    {
        // Make sure the parameters are valid
        $allowedParameters = ['includeUsageStatistics' => 'bool'];
        HttpClient::validateParameters($allowedParameters, $parameters);

        // Set the endpoint and make the request
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}/views";
        $response = $this->client->get($endpoint, $parameters);

        return $response;
    }

    /**
     * Get a specific workbook by ID
     *
     * Returns information about the specified workbook, including information about views and tags.
     * Note: After you create a resource, the server updates its search index. If you make a query immediately to see a
     * new resource, the query results might not be up to date.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#query_workbook
     *
     * @param string $workbookID The ID of the workbook to retrieve.
     * @param array  $parameters Optional query parameters.
     *               - includeUsageStatistics (bool): If true, includes usage statistics for the views.
     *
     * @return array
     */
    public function getWorkbookById(string $workbookID, array $parameters = [])
    {
        $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}";
        $response = $this->client->get($endpoint, $parameters);

        return $response;
    }

    /**
     * Get a specific workbook by Content URL
     *
     * Returns information about the specified workbook, including information about views and tags.
     * Note: After you create a resource, the server updates its search index. If you make a query immediately to see a
     * new resource, the query results might not be up to date.
     * @see https://help.tableau.com/current/api/rest_api/en-us/REST/rest_api_ref_workbooks_and_views.htm#query_workbook
     *
     * @param string $contentURL The ID of the workbook to retrieve.
     * @param array  $parameters Optional query parameters.
     *               - includeUsageStatistics (bool): If true, includes usage statistics for the views.
     *
     * @return array
     */
    public function getWorkbookByContentURL(string $contentURL, array $parameters = [])
    {
        // Setup ?key=contentUrl query parameter
        $parameters['key'] = ['contentUrl'];

        // Make the request
        $endpoint = "/sites/{$this->siteID}/workbooks/{$contentURL}";
        $response = $this->client->get($endpoint, $parameters);

        return $response;
    }

    // /**
    //  * Get all workbooks from the Tableau server
    //  */
    // public function getAllWorkbooks($parameters = [])
    // {
    //     $endpoint = "/sites/{$this->siteID}/workbooks";
    //     return $this->client->get($endpoint, $parameters);
    // }

    // /**
    //  * Update workbook information
    //  */
    // public function updateWorkbook($siteId, $workbookID, $data = [])
    // {
    //     $endpoint = "/sites/{$this->siteID}/workbooks/{$workbookID}";
    //     $response = $this->client->put($endpoint, $data);
    // }
}
