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
    include_once "./includes/role-management.php";
    include_once "./endpoints/Endpoint.php";

    $response       = array();
    $request_body   = json_decode(file_get_contents('php://input'), true);
    $request_header = getallheaders();

    $endpoint_id    = $request_body["endpoint_id"] ?? null;
    if($endpoint_id == null) $endpoint_id = $_GET['endpoint_id'] ?? null;
    $parameters     = $request_body["parameters"] ?? array();
    $get_parameters = $_GET;
    $token          = $request_body["token"] ?? null;
    if($token == null) $token = $request_header["Authorization"] ?? null;

    if($token != null) $token = str_replace("Bearer ", "", $token);

    require_once "./includes/ErrorLogger.php";
    $logger = new includes\ErrorLogger();

    $logger->log("===================================================");
    $logger->log("Request type: " . $_SERVER['REQUEST_METHOD']);
    $logger->log("Request header: " . json_encode($request_header));
    $logger->log("Request received: " . json_encode($request_body));
    $logger->log("Token: " . $token);
    $logger->log("Endpoint ID: " . $endpoint_id);
    $logger->log("Parameters: " . json_encode($parameters));

    switch($endpoint_id)
    {
        case "auth":
            require_once "./endpoints/AuthEndpoint.php";
            $endpoint = new endpoints\AuthEndpoint();
            $response = $endpoint->handleRequest($parameters, $get_parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;

        case "zipcodestack":
            require_once "./endpoints/ZipcodestackEndpoint.php";
            $endpoint = new endpoints\ZipcodestackEndpoint();
            $response = $endpoint->handleRequest($parameters, $get_parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;
        
        case "profile":
            require_once "./endpoints/ProfileEndpoint.php";
            $endpoint = new endpoints\ProfileEndpoint();
            $response = $endpoint->handleRequest($parameters, $get_parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;

        case "relationship":
            require_once "./endpoints/RelationshipEndpoint.php";
            $endpoint = new endpoints\RelationshipEndpoint();
            $response = $endpoint->handleRequest($parameters, $get_parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;
        
        case "post":
            require_once "./endpoints/PostEndpoint.php";
            $endpoint = new endpoints\PostEndpoint();
            $response = $endpoint->handleRequest($parameters, $get_parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;

        case "interact":
            require_once "./endpoints/InteractEndpoint.php";
            $endpoint = new endpoints\InteractEndpoint();
            $response = $endpoint->handleRequest($parameters, $get_parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;

        case "notification":
            require_once "./endpoints/NotificationEndpoint.php";
            $endpoint = new endpoints\NotificationEndpoint();
            $response = $endpoint->handleRequest($parameters, $get_parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;

        case "twofactor":
            require_once "./endpoints/TwoFactorEndpoint.php";
            $endpoint = new endpoints\TwoFactorEndpoint();
            $response = $endpoint->handleRequest($parameters, $get_parameters, $_SERVER['REQUEST_METHOD'], $token, false);
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;

        default: throw new Exception('Invalid endpoint id \''. $endpoint_id .'\' given', 400);
    }
}
catch(Exception $e)
{

    require_once "./includes/ErrorLogger.php";
    $logger = new includes\ErrorLogger();
    $logger->log($e->getMessage());
    
    $response               = array();
    $response['status']     = 'error';
    $response['message']    = "Es gab einen Fehler bei der Verarbeitung.";
    $response['hint']       = $e->getMessage();
    $response['line']       = $e->getLine();
    $response['file']       = $e->getFile();
    $response['code']       = $e->getCode();

    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

?>