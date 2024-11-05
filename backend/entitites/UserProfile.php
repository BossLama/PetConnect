<?php
/**
 *  =================================================================================
 *  Name        :       UserProfile.php
 *  Purpose     :       Entity class for the user profile
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       01.11.2024
 *  =================================================================================
 *  
 *  USAGE       :
 *  Include this file in your PHP script to get access to the UserProfile class.
 *  
 *  EXAMPLE     :
 *  $user = new UserProfile(array());
 *  $user->save();
 *  $user->delete();
 *  $users = UserProfile::getAll();
 *  $user = UserProfile::findByID('user_id');
 *  $user = UserProfile::findByEmail('email');
 *  
 */
namespace entities;
class UserProfile
{
    // Class properties
    private $user_id;
    private $username;
    private $password;
    private $email;
    private $zip_code;
    private $pet_type;
    private $animal_breed;

    public function __construct(array $parameters)
    {
        $this->user_id    = $parameters['user_id'] ?? generateUserID();             // Randomly generated user ID
        $this->username   = $parameters['username'] ?? null;                        // Username of profile (Name of the pet)
        $this->password   = $parameters['password'] ?? null;                        // Password of profile
        $this->email      = $parameters['email'] ?? null;                           // Email of profile (unique, used for login)
        $this->zip_code   = $parameters['zip_code'] ?? null;                        // Zip code of the user
        $this->pet_type   = $parameters['pet_type'] ?? null;                        // Type of pet (e.g. dog, cat, ...)
        $this->animal_breed = $parameters['animal_breed'] ?? null;                  // Breed of the pet (e.g. Golden Retriever)
    }


    public function generateUserID()
    {
        return md5(uniqid(rand(), true));
    }


    public function isPasswordSecure()
    {
        if(!isset($this->password))                         return false;
        if(strlen($this->password < 8))                     return false;
        if(!preg_match('/[A-Z]/', $this->password))         return false;
        if(!preg_match('/[a-z]/', $this->password))         return false;
        if(!preg_match('/[0-9]/', $this->password))         return false;
        if(!preg_match('/[^A-Za-z0-9]/', $this->password))  return false;
        return true;
    }


    public function encryptPassword()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }


    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    
    public function save()
    {
        if(!defined('USER_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(USER_STORAGE_FILE))
        {
            file_put_contents(USER_STORAGE_FILE, json_encode(array()));
        }

        $users = json_decode(file_get_contents(USER_STORAGE_FILE), true);
        $users[$this->user_id] = $this;
        file_put_contents(USER_STORAGE_FILE, json_encode($users, JSON_PRETTY_PRINT));       // TODO: Remove JSON_PRETTY_PRINT for production
    }


    public function delete()
    {
        if(!defined('USER_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(USER_STORAGE_FILE))
        {
            file_put_contents(USER_STORAGE_FILE, json_encode(array()));
        }

        $users = json_decode(file_get_contents(USER_STORAGE_FILE), true);
        unset($users[$this->user_id]);
        file_put_contents(USER_STORAGE_FILE, json_encode($users, JSON_PRETTY_PRINT));       // TODO: Remove JSON_PRETTY_PRINT for production
    }


    public static function getAll()
    {
        if(!defined('USER_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(USER_STORAGE_FILE))
        {
            file_put_contents(USER_STORAGE_FILE, json_encode(array()));
        }

        return json_decode(file_get_contents(USER_STORAGE_FILE), true);
    }


    public static function findByID($userID)
    {
        if(!defined('USER_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(USER_STORAGE_FILE))
        {
            file_put_contents(USER_STORAGE_FILE, json_encode(array()));
        }

        $users = json_decode(file_get_contents(USER_STORAGE_FILE), true);
        return $users[$userID] ?? null;
    }

    public static function findByEmail($email)
    {
        if(!defined('USER_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(USER_STORAGE_FILE))
        {
            file_put_contents(USER_STORAGE_FILE, json_encode(array()));
        }

        $users = json_decode(file_get_contents(USER_STORAGE_FILE), true);
        foreach($users as $user)
        {
            if($user->email == $email) return $user;
        }
        return null;
    }
}
