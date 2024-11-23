<?php
/**
 *  =================================================================================
 *  Name        :       Relationship.php
 *  Purpose     :       Entity class for the relationships between users (FriendRequests)
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       07.11.2024
 *  =================================================================================
 *  
 *  USAGE       :
 *  Include this file in your PHP script to get access to the Relationsship class.
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
class Relationship
{
    // Class properties
    private $relation_id;
    private $status;
    private $date;
    private $from_user;
    private $to_user;

    public function __construct(array $parameters)
    {
        $this->relation_id      = $parameters["relation_id"];                             // ID of relationship
        $this->status           = $parameters['status'] ?? 1;                             // Status of the profile (0 = blocked, 1 = pending, 2 = friends)
        $this->date             = $parameters['date'] ?? date('Y-m-d H:i:s');     // Date of last relationsship update between users
        $this->from_user        = $parameters['from_user'] ?? null;                       // Sender of the request / blocker
        $this->to_user          = $parameters['to_user'] ?? null;                         // recipent of the request / blocked user
    }

    public function asArray()
    {
        return array(
            'relation_id'   => $this->relation_id, 
            'status'            => $this->status,
            'date'              => $this->date,
            'from_user'         => $this->from_user,
            'to_user'           => $this->to_user
        );
    }

    public function save()
    {
        if(!defined('RELATIONSHIP_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(RELATIONSSHIP_STORAGE_FILE))
        {
            file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode(array()));
        }

        $relationships = json_decode(file_get_contents(RELATIONSSHIP_STORAGE_FILE, true));
        $relationships[$this->relation_id] = $this->asArray();
        file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode($relationships, JSON_PRETTY_PRINT));       // TODO: Remove JSON_PRETTY_PRINT for production
    }


    public function delete()
    {
        if(!defined('RELATIONSHIP_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(RELATIONSSHIP_STORAGE_FILE))
        {
            file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode(array()));
        }

        $relationships = json_decode(file_get_contents(RELATIONSSHIP_STORAGE_FILE), true);
        unset($relationships[$this->relation_id]);
        file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode($relationships, JSON_PRETTY_PRINT));       // TODO: Remove JSON_PRETTY_PRINT for production
    }


    public static function getAll()
    {
        if(!defined('RELATIONSHIP_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(RELATIONSSHIP_STORAGE_FILE))
        {
            file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode(array()));
        }

        return json_decode(file_get_contents(RELATIONSSHIP_STORAGE_FILE), true);
    }


    public static function findByID($relation_id)
    {
        if(!defined('RELATIONSHIP_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(RELATIONSSHIP_STORAGE_FILE))
        {
            file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode(array()));
        }

        $relationships = json_decode(file_get_contents(RELATIONSSHIP_STORAGE_FILE), true);
        return $relationships[$relation_id] ?? null;
    }

    public static function findByFrom($from_user)
    {
        if(!defined('RELATIONSHIP_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(RELATIONSSHIP_STORAGE_FILE))
        {
            file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode(array()));
        }

        $relationships = json_decode(file_get_contents(RELATIONSSHIP_STORAGE_FILE), true);
        foreach($relationships as $relationship)
        {
            if($relationship["from_user"] == $from_user) return new Relationship($relationship);
        }
        return null;
    }

    public static function findByTo($to_user)
    {
        if(!defined('RELATIONSHIP_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(RELATIONSSHIP_STORAGE_FILE))
        {
            file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode(array()));
        }

        $relationships = json_decode(file_get_contents(RELATIONSSHIP_STORAGE_FILE), true);
        foreach($relationships as $relationship)
        {
            if($relationship["to_user"] == $to_user) return new Relationship($relationship);
        }
        return null;
    }

    public static function findByToOrFrom($user_id)
    {
        if(!defined('RELATIONSHIP_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'User storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(RELATIONSSHIP_STORAGE_FILE))
        {
            file_put_contents(RELATIONSSHIP_STORAGE_FILE, json_encode(array()));
        }

        $relationships = json_decode(file_get_contents(RELATIONSSHIP_STORAGE_FILE), true);
        $user_relationships = [];
        foreach($relationships as $relationship)
        {
            if($relationship["to_user"] == $user_id || $relationship["from_user"] == $user_id) $user_relationships[] = new Relationship($relationship);
        }
        return $user_relationships;
    }



    // ============================ GETTER METHODS ============================
    public function getRelationID()      { return $this->relation_id; }
    public function getStatus()          { return $this->status; }
    public function getDate()            { return $this->date; }
    public function getFromUser()        { return $this->from_user; }
    public function getToUser()          { return $this->to_user; }

    // ============================ SETTER METHODS ============================
    public function setRelationID($relation_id)  { $this->relation_id = $relation_id; }
    public function setStatus($status)           { $this->status = $status; }
    public function setDate($date)               { $this->date = $date; }
    public function setFromUser($from_user)      { $this->from_user = $from_user; }
    public function setToUser($to_user)          { $this->to_user = $to_user; }
}
