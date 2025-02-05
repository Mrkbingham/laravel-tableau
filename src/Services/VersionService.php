<?php

namespace InterWorks\Tableau\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Http\ErrorHandler;
use InterWorks\Tableau\Http\ResponseParser;

class VersionService
{
    public static $apiVersionMap = [
        '8.3'    => '2.0',
        '9.0'    => '2.0',
        '9.1'    => '2.0',
        '9.2'    => '2.1',
        '9.3'    => '2.2',
        '10.0'   => '2.3',
        '10.1'   => '2.4',
        '10.2'   => '2.5',
        '10.3'   => '2.6',
        '10.4'   => '2.7',
        '10.5'   => '2.8',
        '2018.1' => '3.0',
        '2018.2' => '3.1',
        '2018.3' => '3.2',
        '2019.1' => '3.3',
        '2019.2' => '3.4',
        '2019.3' => '3.5',
        '2019.4' => '3.6',
        '2020.1' => '3.7',
        '2020.2' => '3.8',
        '2020.3' => '3.9',
        '2020.4' => '3.10',
        '2021.1' => '3.11',
        '2021.2' => '3.12',
        '2021.3' => '3.13',
        '2021.4' => '3.14',
        '2022.1' => '3.15',
        '2022.2' => '3.16', // Tableau Cloud only
        '2022.3' => '3.17',
        '2022.4' => '3.18', // Tableau Cloud only
        '2023.1' => '3.19',
        '2023.2' => '3.20', // Tableau Cloud only
        '2023.3' => '3.21',
        '2024.1' => '3.22', // Tableau Cloud only
        '2024.2' => '3.23',
        '2024.3' => '3.24', // Tableau Cloud only
    ];

    /**
     * Returns the Product version from Tableau's open API.
     *
     * Since this call happens prior to authentication, use the HTTP facade, and not the HttpClient.
     *
     * @return string
     */
    public static function fetchAPIVersionFromTableau(): string
    {
        $tableauURL = Config::get('tableau.url');
        // 2.4 is the earliest version that supports the serverinfo endpoint
        $response = Http::get($tableauURL . '/api/2.4/serverinfo');

        if (!$response->successful()) {
            $errorHandler = new ErrorHandler($response);
            return $errorHandler->outputMessage();
        } else {
            $responseData = ResponseParser::parse($response);
            return $responseData['serverInfo']['restApiVersion'];
        }
    }

    /**
     * Returns the REST API version.
     *
     * @throws Exception If the Tableau version is unknown.
     *
     * @return string
     */
    public static function getAPIVersion(): string
    {
        $productVersion = Config::get('tableau.product_version');

        // If there is no product version set, retrieve it from the server
        if (!$productVersion) {
            return self::fetchAPIVersionFromTableau();
        }

        if (!isset(self::$apiVersionMap[$productVersion])) {
            throw new Exception('Unknown Tableau version: ' . $productVersion);
        }
        return self::$apiVersionMap[$productVersion];
    }
}
