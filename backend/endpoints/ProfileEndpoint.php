<?php

namespace endpoints;
class ProfileEndpoint extends Endpoint
{

    // Return a method not allowed response
    public function onPost() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Use the auth endpoint to create a new user profile";
        return $response;
    }

    // Update an existing user profile
    public function onPut() : array
    {
        $tokenString      = $this->token ?? "";
        $token_valid      = \entities\JsonWebToken::verifyToken($tokenString);
        if(!$token_valid)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Sie sind nicht authentifiziert";
            $response["code"]       = "400";
            $response["hint"]       = "Token is invalid";
            return $response;
        }

        $token          = \entities\JsonWebToken::fromToken($tokenString);

        $user_id        = $token->payload["user_id"] ?? null;
        $username       = $this->parameters["username"] ?? null;
        $email          = $this->parameters["email"] ?? null;
        $password       = $this->parameters["password"] ?? null;
        $zip_code       = $this->parameters["zip_code"] ?? null;
        $pet_type       = $this->parameters["pet_type"] ?? null;
        $animal_breed   = $this->parameters["animal_breed"] ?? null;

        $user           = \entities\UserProfile::findByID($user_id);
        if($user == null)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Das Benutzerprofil existiert nicht";
            $response["code"]       = "400";
            $response["hint"]       = "User does not exist";
            return $response;
        }

        if($username != null) $user->setUsername($username);
        if($email != null) $user->setEmail($email);
        if($password != null) $user->setPassword($password);
        if($zip_code != null) $user->setZipCode($zip_code);
        if($pet_type != null) $user->setPetType($pet_type);
        if($animal_breed != null) $user->setAnimalBreed($animal_breed);

        $user->save();

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Ihre Änderungen wurden gespeichert";
        $response["code"]       = "200";
        $response["hint"]       = "User profile updated successfully";
        return $response;
    }

    // Return a method not allowed response
    public function onDelete() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Use the auth endpoint to delete a user profile";
        return $response;
    }

    // Return a method not allowed response
    public function onGet() : array
    {

        require_once "./entities/JsonWebToken.php";
        require_once "./entities/UserProfile.php";

        $tokenString      = $this->token ?? "";
        $token_valid      = \entities\JsonWebToken::verifyToken($tokenString);

        if(!$token_valid)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Sie sind nicht authentifiziert";
            $response["code"]       = "400";
            $response["hint"]       = "Token is invalid";
            $response["token"]      = $tokenString;

            return $response;
        }

        $token          = \entities\JsonWebToken::fromToken($tokenString);
        $user_id        = $token->payload["user_id"] ?? null;
        $user           = \entities\UserProfile::findByID($user_id);
        
        $user->setPassword(null);

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Benutzerprofil wurde gefunden";
        $response["code"]       = "200";
        $response["hint"]       = "User profile found";
        $response["data"]       = $user->asArray();

        return $response;
    }
}

?>