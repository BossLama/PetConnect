<?php
/**
 *  =================================================================================
 *  Name        :       ZipcodestackEndpoint.php
 *  Purpose     :       Entity class for the JSON Web Token
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       01.11.2024
 *  =================================================================================
 *  
**/


namespace endpoints;
class ZipcodestackEndpoint extends Endpoint
{

    public function onGet() : array
    {
        $zip        = $this->parameters['zip'] ?? null;
        $country    = $this->parameters['country'] ?? "de";

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
            $response["message"]    = "API key not found";
            $response["code"]       = "500";
            $response["hint"]       = "Configuration file may not be loaded";
            return $response;
        }

        $url = "https://api.zipcodestack.com/v1/search?codes=". $zip ."&country=". $country ."&apikey=" . ZIPCODESTACK_API_KEY;
        $result = file_get_contents($url);

        $result = json_decode($result, true);
        $result = $result['results'];

        if(count($result) == 0)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "No results found";
            $response["code"]       = "404";
            $response["hint"]       = "Please provide a valid zip code";
            return $response;
        }

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Zip code found";
        $response["code"]       = "200";
        $response["city"]       = $result[0];

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