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

    // Checks if the post is visible to a user
    public function allowedToSee($userID)
    {
        if($this->status == 0) return true;
        //TODO: Check if user is friend
        if($this->status == 2 && $this->user_id == $userID) return true;
        return false;
    }
}

?>