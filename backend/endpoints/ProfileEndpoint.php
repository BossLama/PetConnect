<?php
/**
 *  =================================================================================
 *  Name        :       ProfileEndpoint.php
 *  Purpose     :       Endpoint for managing user profiles, including retrieval and updates.
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last Edited :       01.11.2024
 *  =================================================================================
 *  
 * Class ProfileEndpoint
 * 
 * This class provides an API endpoint for managing user profiles. It supports retrieving
 * and updating user profiles based on authenticated requests. Other methods are restricted 
 * to ensure security and proper usage.
 *
 * Methods:
 * - onGet(): Retrieves the authenticated user's profile.
 *     - Validates the provided JWT token.
 *     - Returns user profile data, excluding sensitive fields like passwords.
 *
 * - onPut(): Updates the authenticated user's profile.
 *     - Validates the token and checks the user's existence.
 *     - Updates fields such as username, email, password, and pet details if provided.
 *
 * - onPost(), onDelete(): Return a "method not allowed" error.
 * 
 * Each method ensures structured JSON responses, including status, message, code, 
 * and optional hints or data. Error handling covers invalid tokens, missing users, 
 * and unauthorized requests.
 *
 * Usage:
 * - Part of the `endpoints` namespace, extending the base `Endpoint` class.
 * - Relies on `JsonWebToken` and `UserProfile` classes for authentication and user management.
 * - Supports flexible updates to user profiles while safeguarding sensitive information.
 *
 * @package endpoints
 */


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
        $user = $user->asPrivateArray();
        
        $load_all       = $this->get_parameters["load_all"] ?? false;
        if($load_all)
        {
            $user = \entities\UserProfile::getAll();
            usort($user, function($a, $b) {
                return $a->getUsername() <=> $b->getUsername();
            });

            $userArray = array();
            foreach($user as $u)
            {
                $uArray = $u->asPrivateArray();
                require_once "./entities/Relationship.php";
                $relationshipX = \entities\Relationship::findByFromAndTo($user_id, $u->getUserID());
                if($relationshipX != null)
                {
                    $uArray["relationship"] = $relationshipX->getStatus();
                    if($relationshipX->getFromUser() == $user_id && $relationshipX->getStatus() == 1)
                    {
                        $uArray["relationship"] = 3;
                    }
                }
                else 
                {
                    $uArray["relationship"] = -1;
                }
                $userArray[] = $uArray;
            }
            $user = $userArray;
        }

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Benutzerprofil wurde gefunden";
        $response["code"]       = "200";
        $response["hint"]       = "User profile found";
        $response["data"]       = $user;
        $response["user_id"]    = $user_id;

        return $response;
    }
}

?>