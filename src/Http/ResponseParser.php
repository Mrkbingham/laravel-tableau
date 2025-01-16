<?php

namespace InterWorks\Tableau\Http;

use SimpleXMLElement;
use Illuminate\Http\Client\Response;

class ResponseParser
{
    /**
     * Auto-detect and parse based on content type
     *
     * @param Response $response
     *
     * @return mixed
     */
    public static function parse(Response $response)
    {
        $contentType = $response->header('Content-Type');

        if (strpos($contentType, 'application/xml') !== false || strpos($contentType, 'text/xml') !== false) {
            return self::parseXml($response->body());
        } elseif (strpos($contentType, 'application/json') !== false) {
            return self::parseJson($response->body());
        }

        return $response->body(); // Default to returning raw response
    }

    /**
     * Parse JSON response into an array
     *
     * @param string $jsonResponse
     *
     * @return array
     */
    public static function parseJson(string $jsonResponse): array
    {
        return json_decode($jsonResponse, true);
    }

    /**
     * Parse XML response into an array
     *
     * @param string $xmlResponse
     *
     * @return array
     */
    public static function parseXml(string $xmlResponse): array
    {
        $xmlObject = simplexml_load_string($xmlResponse, "SimpleXMLElement", LIBXML_NOCDATA);
        return json_decode(json_encode($xmlObject), true);
    }
}
