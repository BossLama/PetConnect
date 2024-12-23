<?php

namespace endpoints;
class TwoFactorEndpoint extends Endpoint
{

    // Return a method not allowed response
    public function onPost() : array
    {
        $user_id        = $this->get_parameters["user_id"] ?? null;
        $totp           = $this->get_parameters["totp"] ?? null;
        $totp = intval($totp);

        require_once "./entities/UserProfile.php";
        $userEntity     = \entities\UserProfile::findByID($user_id);
        if($userEntity == null)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Benutzer nicht gefunden";
            $response["code"]       = "400";
            $response["hint"]       = "User does not exist";
            return $response;
        }

        $totp_result = $userEntity->isTOTPValid($totp);
        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Zwei-Faktor-Authentifizierung erfolgreich";
        $response["code"]       = "200";
        $response["hint"]       = "Two-factor authentication successful";
        $response["result"]     = $totp_result;
        return $response;  
    }

    // Update an existing user profile
    public function onPut() : array
    {
        return array();
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
        return array();
    }
}

?>