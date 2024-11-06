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
    $response       = array();
    $request_body   = json_decode(file_get_contents('php://input'), true);

    $endpoint_id    = $request_body["endpoint_id"];
    $parameters     = $request_body["parameters"] ?? array();

    switch($endpoint_id)
    {
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