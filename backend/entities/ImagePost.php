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
class ImagePost
{

    private $post_id;
    private $user_id;
    private $image;
    private $description;
    private $date;
    private $status;                        // 0 = public post, 1 = friends only, 2 = private post

    public function __construct(array $parameters)
    {
        $this->post_id = $parameters["post_id"] ?? $this->createPostID();
        $this->user_id = $parameters["user_id"] ?? null;
        $this->image = $parameters["image"] ?? null;
        $this->description = $parameters["description"] ?? null;
        $this->date = $parameters["date"] ?? date('Y-m-d H:i:s');
        $this->status = $parameters["status"] ?? 1;
    }

    private function createPostID()
    {
        $prefix = time();
        $id = uniqid($prefix);
        return $id;
    }

    public function allowedToSee($userID)
    {
        if($this->status == 0) return true;
        //TODO: Check if user is friend
        if($this->status == 2 && $this->user_id == $userID) return true;
        return false;
    }

    public function asArray()
    {
        return array(
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'image' => $this->image,
            'description' => $this->description,
            'date' => $this->date,
            'status' => $this->status
        );
    }

    public function save()
    {
        if(!defined('POSTS_STORAGE_FILE')) 
        {
            $response = array();
            $response['status'] = 'error';  
            $response['message'] = 'User storage file not defined';
            $response['code'] = 500;
            $response['hint'] = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POSTS_STORAGE_FILE))
        {
            file_put_contents(POSTS_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POSTS_STORAGE_FILE, true));
        $posts[$this->post_id] = $this->asArray();
        file_put_contents(POSTS_STORAGE_FILE, json_encode($posts, JSON_PRETTY_PRINT));       // TODO: Remove JSON_PRETTY_PRINT for production
    }

    public function delete()
    {
        if(!defined('POSTS_STORAGE_FILE')) 
        {
            $response = array();
            $response['status'] = 'error';  
            $response['message'] = 'User storage file not defined';
            $response['code'] = 500;
            $response['hint'] = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POSTS_STORAGE_FILE))
        {
            file_put_contents(POSTS_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POSTS_STORAGE_FILE, true));
        unset($posts[$this->post_id]);
        file_put_contents(POSTS_STORAGE_FILE, json_encode($posts, JSON_PRETTY_PRINT));       // TODO: Remove JSON_PRETTY_PRINT for production
    }

    public static function getAll()
    {
        if(!defined('POSTS_STORAGE_FILE')) 
        {
            $response = array();
            $response['status'] = 'error';  
            $response['message'] = 'User storage file not defined';
            $response['code'] = 500;
            $response['hint'] = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POSTS_STORAGE_FILE))
        {
            file_put_contents(POSTS_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POSTS_STORAGE_FILE, true));
        return $posts;
    }

    public static function findByID($post_id)
    {
        if(!defined('POSTS_STORAGE_FILE')) 
        {
            $response = array();
            $response['status'] = 'error';  
            $response['message'] = 'User storage file not defined';
            $response['code'] = 500;
            $response['hint'] = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POSTS_STORAGE_FILE))
        {
            file_put_contents(POSTS_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POSTS_STORAGE_FILE, true));
        return $posts[$post_id] ?? null;
    }

    public static function findByUserID($user_id)
    {
        if(!defined('POSTS_STORAGE_FILE')) 
        {
            $response = array();
            $response['status'] = 'error';  
            $response['message'] = 'User storage file not defined';
            $response['code'] = 500;
            $response['hint'] = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POSTS_STORAGE_FILE))
        {
            file_put_contents(POSTS_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POSTS_STORAGE_FILE, true));
        $user_posts = array();
        foreach($posts as $post)
        {
            if($post['user_id'] == $user_id)
            {
                $user_posts[] = $post;
            }
        }
        return $user_posts;
    }

    public static function findByZIP($zip)
    {
        if(!defined('POSTS_STORAGE_FILE')) 
        {
            $response = array();
            $response['status'] = 'error';  
            $response['message'] = 'User storage file not defined';
            $response['code'] = 500;
            $response['hint'] = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POSTS_STORAGE_FILE))
        {
            file_put_contents(POSTS_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POSTS_STORAGE_FILE, true));
        $zip_posts = array();
        foreach($posts as $post)
        {
            $owner = UserProfile::findByID($post['user_id']);
            if($owner->getZIP() == $zip)
            {
                $zip_posts[] = $post;
            }
        }
        return $zip_posts;
    }
}

?>