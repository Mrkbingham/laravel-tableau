<?php

namespace InterWorks\Tableau\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use InterWorks\Tableau\Http\ErrorHandler;
use InterWorks\Tableau\Http\ResponseParser;

class ServerInfoService
{
    /**
     * Returns the Product version from Tableau's open API.
     *
     * Since this call happens prior to authentication, use the HTTP facade, and not the HttpClient.
     *
     * @return ?array
     */
    public static function fetchServerInfo(): ?array
    {
        $tableauURL = Config::get('tableau.url');
        // 2.4 is the earliest version that supports the serverinfo endpoint
        $response = Http::withHeaders([
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
        ])->get($tableauURL . '/api/2.4/serverinfo');

        if (!$response->successful()) {
            $errorHandler = new ErrorHandler($response);

            return $errorHandler->outputMessage();
        }

        return ResponseParser::parse($response);
    }
}
