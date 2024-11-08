<?php

namespace endpoints;
class ProfileEndpoint
{

    // Return a method not allowed response
    public function onPost()
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Use the auth endpoint to create a new user profile";
        return $response;
    }

    // Update an existing user profile
    public function onPut()
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
    public function onDelete()
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Use the auth endpoint to delete a user profile";
        return $response;
    }

    // Return a method not allowed response
    public function onGet()
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
        $user           = \entities\UserProfile::findByID($user_id);

        $filter = array();
        if(isset($this->get_parameters["user_id"])) $filter = $this->get_parameters["user_id"];
        if(isset($this->get_parameters["username"])) $filter = $this->get_parameters["username"];
        if(isset($this->get_parameters["email"])) $filter = $this->get_parameters["email"];
        if(isset($this->get_parameters["zip_code"])) $filter = $this->get_parameters["zip_code"];
        if(isset($this->get_parameters["pet_type"])) $filter = $this->get_parameters["pet_type"];
        if(isset($this->get_parameters["animal_breed"])) $filter = $this->get_parameters["animal_breed"];

        $users = \entities\UserProfile::getAll();

        $filtered_users = array();
        foreach($users as $resultUser)
        {
            $is_in_filter = false;
            foreach($filter as $key => $value)
            {
                if($resultUser[$key] == $value) $is_in_filter = true;
            }
            if($is_in_filter) $filtered_users[] = $resultUser;
        }

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Die Benutzerprofile wurden erfolgreich abgerufen";
        $response["code"]       = "200";
        $response["hint"]       = "User profiles have been retrieved";
        $response["profiles"]   = $filtered_users;
        return $response;
    }
}

?>