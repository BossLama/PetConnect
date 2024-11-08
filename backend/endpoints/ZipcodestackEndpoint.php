<?php
/**
 *  =================================================================================
 *  File        :       ZipcodestackEndpoint.php
 *  Description :       Endpoint class for handling ZIP code lookups using the Zipcodestack API.
 *                      This endpoint processes GET requests to fetch location data based on
 *                      a provided ZIP code and country. Only GET requests are supported.
 *                      Other request methods (POST, DELETE, PUT) return an error response.
 *
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last Edited :       01.11.2024
 *  
 *  Methods:
 *      - onGet(): Handles ZIP code lookup by calling the Zipcodestack API, validates the
 *                 API key, retrieves data for the specified ZIP code and country, and 
 *                 returns the city if found.
 *      - onPost(), onDelete(), onPut(): Return a "method not allowed" error.
 *
 *  Notes:
 *      - Requires a defined API key constant ZIPCODESTACK_API_KEY for API requests.
 *      - Returns JSON responses with status, message, code, and additional data or hints.
 *
 *  =================================================================================
 */



namespace endpoints;
class ZipcodestackEndpoint extends Endpoint
{

    public function onGet() : array
    {
        $zip        = $this->get_parameters['zip'] ?? null;
        $country    = $this->get_parameters['country'] ?? "de";

        if(!isset($zip))
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Please provide a zip code";
            $response["code"]       = "400";
            $response["hint"]       = "Please provide a zip code";
            return $response;
        }

        if(!defined("ZIPCODESTACK_API_KEY"))
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Ups, etwas ist schief gelaufen";
            $response["code"]       = "500";
            $response["hint"]       = "Configuration file may not be loaded";
            return $response;
        }

        $api_key = file_get_contents(ZIPCODESTACK_API_KEY);
        $url = "http://api.zipcodestack.com/v1/search?codes=$zip&country=$country&apikey=$api_key";
        $result = file_get_contents($url);

        $result = json_decode($result, true);
        $result = $result['results'];

        if(count($result) == 0)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Die Postleitzahl wurde nicht gefunden";
            $response["code"]       = "404";
            $response["hint"]       = "Please provide a valid zip code";
            return $response;
        }

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Postleitzahl gefunden";
        $response["code"]       = "200";
        $response["city"]       = $result[$zip][0];

        return $response;
    }

    public function onPost() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Please use a valid request method";
        return $response;
    }

    public function onDelete() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Please use a valid request method";
        return $response;
    }

    public function onPut() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Please use a valid request method";
        return $response;
    }

}

?>