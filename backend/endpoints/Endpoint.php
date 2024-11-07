<?php
/**
 *  =================================================================================
 *  Name        :       Endpoint.php
 *  Purpose     :       Entity class for the JSON Web Token
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       01.11.2024
 *  =================================================================================
 *  
 * Abstract Class Endpoint
 * 
 * This class defines the base structure for an API endpoint with methods to handle various HTTP requests.
 * It provides a mechanism for authentication, request handling, and delegates method-specific actions to
 * the child classes that implement it.
 * 
 * Methods:
 * - handleRequest(): Main request handler that processes the HTTP method and parameters, checks authentication
 *                    if required, and calls the appropriate method (onPost, onGet, onDelete, onPut) based on the HTTP method.
 *                    If the request method is invalid, it returns an error response.
 * 
 * - authenticate(): Authenticates a request using a JSON Web Token (JWT). Verifies the token, extracts the payload,
 *                   and returns the associated user if authentication is successful.
 * 
 * Abstract Methods (to be implemented by subclasses):
 * - onPost(): Handles HTTP POST requests.
 * - onGet(): Handles HTTP GET requests.
 * - onDelete(): Handles HTTP DELETE requests.
 * - onPut(): Handles HTTP PUT requests.
 * 
 * Usage:
 * - This abstract class is part of the `endpoints` namespace and is intended to be extended by other endpoint classes.
 * - Each child class must define the methods for handling specific HTTP request types.
 * - Token-based authentication is built-in and can be required or bypassed for specific requests.
 *
 * @package endpoints
 */


namespace endpoints;
abstract class Endpoint
{

    public function handleRequest(array $parameters = array(), string $method = "",  ?string $token, bool $requires_auth = true)
    {
        $this->parameters           = $parameters;
        $this->method               = strtoupper($method);
        $this->token                = $token;

        if ($requires_auth && !authenticate($token))
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Request is not authenticated";
            $response["code"]       = "401";
            $response["hint"]       = "Give a valid token";
            return $response;
        }

        switch($method)
        {
            case "POST":
                return $this->onPost();
                break;
            case "GET":
                return $this->onGet();
                break;
            case "DELETE":
                return $this->onDelete();
                break;
            case "PUT":
                return $this->onPut();
                break;
            default:
                $response               = array();
                $response["status"]     = "error";
                $response["message"]    = "Method is not allowed";
                $response["code"]       = "400";
                $response["hint"]       = "Please use a valid request method";
                return $response;
        }
    }

    abstract function onPost() : array;
    abstract function onGet() : array;
    abstract function onDelete() : array;
    abstract function onPut() : array;

    
    public function authenticate(string $token)
    {
        $token = \entities\JsonWebToken::fromToken($token);
        if (!$token->verify()) return null;
        $payload = $token->payload;
        if(!isset($payload['user_id'])) return null;
        $user = \entities\User::fromId($payload['user_id']);
        return $user;
    }

}
?>