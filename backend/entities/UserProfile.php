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
    private $totp_secret;
    private $totp_enabled;
    private $email;
    private $zip_code;
    private $pet_type;
    private $animal_breed;
    private $profile_picture;

    private $created_at;
    private $last_login;
    private $role;
    private $status;

    public function __construct(array $parameters)
    {
        $this->user_id    = $parameters['user_id'] ?? $this->generateUserID();      // Randomly generated user ID
        $this->username   = $parameters['username'] ?? null;                        // Username of profile (Name of the pet)
        $this->password   = $parameters['password'] ?? null;                        // Password of profile
        $this->email      = $parameters['email'] ?? null;                           // Email of profile (unique, used for login)
        $this->zip_code   = $parameters['zip_code'] ?? null;                        // Zip code of the user
        $this->pet_type   = $parameters['pet_type'] ?? null;                        // Type of pet (e.g. dog, cat, ...)
        $this->animal_breed = $parameters['animal_breed'] ?? null;                  // Breed of the pet (e.g. Golden Retriever)
        $this->profile_picture = $parameters['profile_picture'] ?? null;            // Profile picture of the user
        $this->created_at = $parameters['created_at'] ?? date('Y-m-d H:i:s');       // Date of profile creation
        $this->last_login = $parameters['last_login'] ?? null;                      // Date of last login
        $this->status     = $parameters['status'] ?? 1;                             // Status of the profile (0 = unverified, 1 = active, 2 = banned)
        $this->role       = $parameters['role'] ?? 1;                               // Role of the profile (1 = user, 2 = professional, 3 = admin)
        $this->totp_enabled = $parameters['totp_enabled'] ?? false;                 // TOTP enabled for the user
        $this->totp_secret = $parameters['totp_secret'] ?? $this->createTOTPSecret();// Secret for TOTP
    }   


    public function generateUserID()
    {
        return md5(uniqid(rand(), true));
    }

    public function createTOTPSecret()
    {
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/TwoFactorAuth.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/TwoFactorAuthException.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Algorithm.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Rng/IRNGProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Rng/CSRNGProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Time/ITimeProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Time/LocalMachineTimeProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Qr/IQRCodeProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Qr/BaconQrCodeProvider.php";
        
        //use RobThree\Auth\TwoFactorAuth;
        //use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
        
        $tfa = new \RobThree\Auth\TwoFactorAuth(new \RobThree\Auth\Providers\Qr\BaconQrCodeProvider());
        $this->totp_secret = $tfa->createSecret();

        return $this->totp_secret;
    }

    public function isTOTPValid($totp)
    {
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/TwoFactorAuth.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/TwoFactorAuthException.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Algorithm.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Rng/IRNGProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Rng/CSRNGProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Time/ITimeProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Time/LocalMachineTimeProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Qr/IQRCodeProvider.php";
        require_once "./plugins/TwoFactorAuth-3.0.2/lib/Providers/Qr/BaconQrCodeProvider.php";
        
        $tfa = new \RobThree\Auth\TwoFactorAuth(new \RobThree\Auth\Providers\Qr\BaconQrCodeProvider());
        return $tfa->verifyCode($this->totp_secret, $totp);
    }

    public function isTOTPEnabled()
    {
        return $this->totp_enabled;
    }

    public function isPasswordSecure()
    {
        if(!isset($this->password))                         return false;
        if(strlen($this->password < 8))                     return false;
        if(!preg_match('/[A-Z]/', $this->password))         return false;
        if(!preg_match('/[a-z]/', $this->password))         return false;
        if(!preg_match('/[0-9]/', $this->password))         return false;
        if(!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $this->password)) return false;
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
    
    public function asArray()
    {
        return array(
            'user_id'       => $this->user_id,
            'username'      => $this->username,
            'password'      => $this->password,
            'totp_secret'   => $this->totp_secret,
            'totp_enabled'  => $this->totp_enabled,
            'email'         => $this->email,
            'zip_code'      => $this->zip_code,
            'pet_type'      => $this->pet_type,
            'animal_breed'  => $this->animal_breed,
            'created_at'    => $this->created_at,
            'last_login'    => $this->last_login,
            'status'        => $this->status,
            'role'          => $this->role,
            'profile_picture' => $this->profile_picture
        );
    }

    public function asPrivateArray()
    {
        return array(
            'user_id'       => $this->user_id,
            'username'      => $this->username,
            'email'         => $this->email,
            'zip_code'      => $this->zip_code,
            'pet_type'      => $this->pet_type,
            'animal_breed'  => $this->animal_breed,
            'created_at'    => $this->created_at,
            'last_login'    => $this->last_login,
            'status'        => $this->status,
            'role'          => $this->role,
            'profile_picture' => $this->profile_picture
        );
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
        $users[$this->user_id] = $this->asArray();
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

        $users = json_decode(file_get_contents(USER_STORAGE_FILE), true);
        $user_profiles = array();

        foreach($users as $user)
        {
            $user_profiles[] = new UserProfile($user);
        }
        return $user_profiles;
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
        if(!isset($users[$userID])) return null;
        return new UserProfile($users[$userID]);
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
            if($user["email"] == $email) return new UserProfile($user);
        }
        return null;
    }

    public static function findByUsername($username)
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
            if($user["username"] == $username) return new UserProfile($user);
        }
        return null;
    }



    // ============================ GETTER METHODS ============================
    public function getUserID()         { return $this->user_id; }
    public function getUsername()        { return $this->username; }
    public function getPassword()        { return $this->password; }
    public function getEmail()           { return $this->email; }
    public function getZipCode()         { return $this->zip_code; }
    public function getPetType()         { return $this->pet_type; }
    public function getAnimalBreed()     { return $this->animal_breed; }
    public function getCreatedAt()       { return $this->created_at; }
    public function getLastLogin()       { return $this->last_login; }
    public function getStatus()          { return $this->status; }
    public function getRole()            { return $this->role; }
    public function getProfilePicture()  { return $this->profile_picture; }

    // ============================ SETTER METHODS ============================
    public function setUserID($user_id)         { $this->user_id = $user_id; }
    public function setUsername($username)      { $this->username = $username; }
    public function setPassword($password)      { $this->password = $password; }
    public function setEmail($email)            { $this->email = $email; }
    public function setZipCode($zip_code)       { $this->zip_code = $zip_code; }
    public function setPetType($pet_type)       { $this->pet_type = $pet_type; }
    public function setAnimalBreed($animal_breed) { $this->animal_breed = $animal_breed; }
    public function setCreatedAt($created_at)   { $this->created_at = $created_at; }
    public function setLastLogin($last_login)   { $this->last_login = $last_login; }
    public function setStatus($status)          { $this->status = $status; }
    public function setRole($role)              { $this->role = $role; }
    public function setProfilePicture($profile_picture) { $this->profile_picture = $profile_picture; }
}
