<?php
/**
 *  =================================================================================
 *  Name        :       AuthEndpoint.php
 *  Purpose     :       Entity class for the JSON Web Token
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       01.11.2024
 *  =================================================================================
 *  
 * Class AuthEndpoint
 * 
 * This class provides an endpoint for user authentication and registration within an API.
 * It handles various HTTP request methods to register, authenticate, and validate users.
 *
 * Methods:
 * - onPost(): Handles user registration. Validates input parameters, ensures unique email, checks password strength,
 *              and saves a new user profile if valid. Generates a JWT token upon successful registration.
 * 
 * - onGet(): Validates an existing JWT token, confirming whether it is valid or not.
 * 
 * - onDelete(): Returns an error as this method is not supported for this endpoint.
 * 
 * - onPut(): Handles user login by validating the provided email and password.
 *            Generates a new JWT token for an authenticated user.
 * 
 * Each method returns a structured response with a status, message, code, and optional hint to guide the client.
 *
 * Usage:
 * - This endpoint is part of the `endpoints` namespace and extends a base `Endpoint` class, allowing it to integrate
 *   with other API functionality within the system.
 * - Requires classes from the `entities` namespace, including `UserProfile` and `JsonWebToken`.
 *
 * @package endpoints
 */


namespace endpoints;
class AuthEndpoint extends Endpoint
{

    // Register a new user profile
    public function onPost() : array
    {
        include_once "./entities/UserProfile.php";

        $username   = $this->parameters["username"] ?? null;
        $email      = $this->parameters["email"] ?? null;
        $password   = $this->parameters["password"] ?? null;
        $zip        = $this->parameters["zip"] ?? null;

        if($username == null || $email == null || $password == null || $zip == null)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Please provide all required parameters";
            $response["code"]       = "400";
            $response["hint"]       = "Please provide all required parameters";
            return $response;
        }

        $user = \entities\UserProfile::findByEmail($email);
        if(isset($user))
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "This mail is already in use";
            $response["code"]       = "400";
            $response["hint"]       = "User with the given email already exists";
            return $response;
        }

        $user_data = array();
        $user_data["username"]  = $username;
        $user_data["email"]     = $email;
        $user_data["password"]  = $password;
        $user_data["zip_code"]  = $zip;

        $user = new \entities\UserProfile($user_data);

        if(!$user->isPasswordSecure())
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Password is not secure enough. Use at least 8 characters, one number and one special character";
            $response["code"]       = "400";
            $response["hint"]       = "Password must be at least 8 characters long and contain at least one number";
            return $response;
        }

        $user->encryptPassword();
        $user->save();

        $user = \entities\UserProfile::findByEmail($email);
        if(!isset($user))
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "User could not be created";
            $response["code"]       = "500";
            $response["hint"]       = "User could not be found after creation";
            return $response;
        }

        require_once "./entities/JsonWebToken.php";
        $token = \entities\JsonWebToken::create($user);

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "User created";
        $response["code"]       = "200";
        $response["hint"]       = "User has been created";
        $response["token"]      = $token->asToken();
        return $response;
    }

    // Method is not allowed
    public function onGet() : array
    {
        $tokenString      = $this->parameters["token"] ?? null;
        $token_valid      = \entities\JsonWebToken::verifyToken($tokenString);

        if(!$token_valid)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Token is invalid";
            $response["code"]       = "400";
            $response["hint"]       = "Token is invalid";
            return $response;
        }

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Token is valid";
        $response["code"]       = "200";
        $response["hint"]       = "Token is valid";
        return $response;
    }

    // Method is not allowed
    public function onDelete() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Please use a valid request method";
        return $response;
    }


    // Login to an existing user profile
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

        include_once "./entities/UserProfile.php";
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