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
class Post
{
    //Class properties
    private $post_id;           // Unique Post ID to identify a post
    private $visibility;        // visibility of post (0 = private,  1 = friends, 2 = community, 3 = public)
    private $type;              // the type of the post (0 = normal post, 1 = comment)
    private $shares;            // how often a post was shared
    private $likes;             // how often a post was liked
    private $missing_report;    // is the post a missing report (true/false)
    private $creator;           // user_id
    private $posted_at;         // posted_at
    private $message;           // text message of the comment
    private $reply_to;          // PostID under which the reply is posted
    private $related_image_id;  // Related Image
    private $related_meetup_id; // Related Meetup


    public function __construct (array $post)
    {
        $this->post_id              = $post['post_id'] ?? $this->generatePostID();
        $this->visibility           = $post['visibility'] ?? 0;
        $this->type                 = $post['type'] ?? 0;
        $this->shares               = $post['shares'] ?? [];
        $this->likes                = $post['likes'] ?? [];
        $this->missing_report       = $post['missing_report'] ?? false;
        $this->creator              = $post['creator'] ?? null;
        $this->posted_at            = $post['posted_at'] ?? date('Y-m-d H:i:s');
        $this->reply_to             = $post['reply_to'] ?? null;
        $this->message              = $post['message'] ?? null;
        $this->related_image_id     = $post['related_image_id'] ?? null;
        $this->related_meetup_id    = $post['related_meetup_id'] ?? null;

        if ( $this->type == 01)
        {
            $this->reply_to = $post['reply_to'] ?? null;
        }

    }

    public function generatePostID(): string
    {
        return md5(uniqid(rand(), true));
    }

    public function toArray(): array
    {
        return array(
            'post_id'               => $this->post_id,
            'visibility'            => $this->visibility,
            'type'                  => $this->type,
            'shares'                => $this->shares,
            'likes'                 => $this->likes,
            "missing_report"        => $this->missing_report,
            'creator'               => $this->creator,
            'posted_at'             => $this->posted_at,
            'message'               => $this->message,
            'reply_to'              => $this->reply_to,
            'related_image_id'      => $this->related_image_id,
            'related_meetup_id'     => $this->related_meetup_id
        );
    }

    public function canSee($userID)
    {
        if ($this->visibility == "0") return $this->creator == $userID;
        if ($this->visibility == "1")
        {
            if($this->creator == $userID) return true;
            require_once "./entities/Relationship.php";
            $relationship = Relationship::findByFromAndTo($this->creator, $userID);
            return $relationship != null && $relationship->getStatus() == 2;
        }
        if ($this->visibility == "2")
        {
            require_once "./entities/UserProfile.php";
            $owner = UserProfile::findByID($this->creator);
            $target = UserProfile::findByID($userID);
            return $owner->getZipCode() == $target->getZipCode();
        }
        return true;
    }

    public function addLike($userID)
    {
        if(!in_array($userID, $this->likes))
        {
            $this->likes[] = $userID;
            $this->save();
        }
    }

    public function removeLike($userID)
    {
        if(in_array($userID, $this->likes))
        {
            $this->likes = array_diff($this->likes, array($userID));
            $this->save();
        }
    }

    public function hasLiked($userID)
    {
        return in_array($userID, $this->likes);
    }

    public function save()
    {
        if(!defined('POST_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'Posts storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POST_STORAGE_FILE))
        {
            file_put_contents(POST_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POST_STORAGE_FILE), true);
        $posts[$this->post_id] = $this->toArray();
        file_put_contents(POST_STORAGE_FILE, json_encode($posts, JSON_PRETTY_PRINT));
    }

    //TODO: Delete Post
    //TODO: static --> findByCreator


    public static function findByID($post_id)
    {
        if(!defined('POST_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'Posts storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POST_STORAGE_FILE))
        {
            file_put_contents(POST_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POST_STORAGE_FILE), true);
        if(!isset($posts[$post_id])) return null;
        return new Post($posts[$post_id]);
    }

    public static function getAll()
    {
        if(!defined('POST_STORAGE_FILE')) 
        {
            $response               = array();
            $response['status']     = 'error';  
            $response['message']    = 'Posts storage file not defined';
            $response['code']       = 500;
            $response['hint']       = 'Configuration file may not included';
            return json_encode($response);
        }
        if(!file_exists(POST_STORAGE_FILE))
        {
            file_put_contents(POST_STORAGE_FILE, json_encode(array()));
        }

        $posts = json_decode(file_get_contents(POST_STORAGE_FILE), true);
        $all_posts = array();
        foreach($posts as $post)
        {
            $all_posts[] = new Post($post);
        }

        return $all_posts;
    }

    // ============================ GETTER METHODS ============================
    public function getPostID(): string                        {return $this->post_id;}
    public function getCreator(): string                    {return $this->creator;}
    public function getPostedAt(): mixed                    {return $this->posted_at;}
    public function getMessage(): string                    {return $this->message;}
    public function getLikes(): array                         {return $this->likes;}
    public function getMissingReport(): bool                 {return $this->missing_report;}
    public function getShares(): array                        {return $this->shares;}
    public function getReplyTo(): string                       {return $this->reply_to;}
    // ============================ SETTER METHODS ============================
    public function setPostID($post_id): void               {$this->post_id = $post_id;}
    public function setCreator($creator): void              {$this->creator = $creator;}
    public function setPostedAt($posted_at): void           {$this->posted_at = $posted_at;}
    public function setMessage($message): void              {$this->message = $message;}
    public function setLikes($likes): void                  {$this->likes = $likes;}
    public function setMissingReport($missing_report): void {$this->missing_report = $missing_report;}
    public function setShares($shares): void                {$this->shares = $shares;}
    public function setReplyTo($reply_to): void             {$this->reply_to = $reply_to;}
}