<?php

namespace endpoints;
abstract class Endpoint
{

    public function handleRequest(array $parameters = array(), string $method = "",  string $token, bool $requires_auth = true)
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
            $response["hint"]       = "Give a valid token"
            return $response;
        }

        switch($method)
        {
            case "POST":
                return onPost();
                break;
            case "GET":
                return onGet();
                break;
            case "DELETE":
                return onDelete();
                break;
            case "UPDATE":
                return onUpdate();
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

    public abstract function onPost() : array;
    public abstract function onGet() : array;
    public abstract function onDelete() : array;
    public abstract function onUpdate() : array;

    
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