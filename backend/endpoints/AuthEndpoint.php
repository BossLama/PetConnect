<?php

/**
 * Required parameters:
 * - email
 * - username
 * - password
 */


namespace endpoints;
class AuthEndpoint extends Endpoint
{

    public function onPost() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Please use a valid request method";
        return $response;
    }

    public function onGet() : array
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
        $email      = $this->parameters["email"] ?? null;
        $password   = $this->parameters["password"] ?? null;

        if($email == null || $password == null)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Your credentials are invalid";
            $response["code"]       = "400";
            $response["hint"]       = "Please provide all required parameters";
            return $response;
        }

    
        $user = \entities\UserProfile::findByEmail($email);

        if($user == null)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Your credentials are invalid";
            $response["code"]       = "400";
            $response["hint"]       = "User with the given email does not exist";
            return $response;
        }

        if(!$user->verifyPassword($password))
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Your credentials are invalid";
            $response["code"]       = "400";
            $response["hint"]       = "Password is incorrect";
            return $response;
        }

        require_once "./entities/JsonWebToken.php";
        $token = \entities\JsonWebToken::create($user);

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "User authenticated";
        $response["code"]       = "200";
        $response["hint"]       = "User has been authenticated";
        $response["token"]      = $token->asToken();
        return $response;
    }

}

?>