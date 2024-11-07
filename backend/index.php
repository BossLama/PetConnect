<?php
/**
 *  =================================================================================
 *  Name        :       index.php
 *  Purpose     :       Main entry point for the backend API
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       01.11.2024
 *  =================================================================================
 *  
 *  USAGE       :
 *  Call this script from your frontend to access the backend API.
 *  
 *  REQUEST     :
 *  {
 *      "token"          : "your_token",
 *      "endpoint_id"    : "your_endpoint_id",
 *      "parameters"     : 
 *      {
 *          "your_parameter": "your_value"
 *      }
 *  }
 */
header('Content-Type: application/json');

try
{
    include_once "./includes/pet-config.php";
    include_once "./entities/UserProfile.php";
    include_once "./endpoints/Endpoint.php";

    $response       = array();
    $request_body   = json_decode(file_get_contents('php://input'), true);

    $endpoint_id    = $request_body["endpoint_id"] ?? null;
    $parameters     = $request_body["parameters"] ?? array();
    $token          = $request_body["token"] ?? null;

    switch($endpoint_id)
    {
        case "auth":
            require_once "./endpoints/AuthEndpoint.php";
            $endpoint = new endpoints\AuthEndpoint();
            $response = $endpoint->handleRequest($parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;

        default: throw new Exception('Invalid endpoint id given', 400);
    }
}
catch(Exception $e)
{
    $response               = array();
    $response['status']     = 'error';
    $response['message']    = $e->getMessage();
    $response['code']       = $e->getCode();

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

?>