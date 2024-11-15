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
    private $comments;          // how often a post was commented
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
        $this->comments             = $post['comment'] ?? [];
        $this->creator              = $post['creator'] ?? null;
        $this->posted_at            = $post['posted_at'] ?? date('Y-m-d H:i:s');
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
            'comments'              => $this->comments,
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
        if ($this->visibility == 0) return $this->creator == $userID;
        if ($this->visibility == 1) return $this->creator == $userID; //TODO: Check if user is friend;
        if ($this->visibility == 2)
        {
            require_once "./entities/UserProfile.php";
            $owner = UserProfile::findByID($this->creator);
            $target = UserProfile::findByID($userID);
            return $owner->getZipCode() == $target->getZipCode();
        }
        return true;
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
    //TODO: hasLiked(userID)
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

        return json_decode(file_get_contents(POST_STORAGE_FILE), true);
    }

    // ============================ GETTER METHODS ============================
    public function getPostID(): int                        {return $this->post_id;}
    public function getCreator(): string                    {return $this->creator;}
    public function getPostedAt(): mixed                    {return $this->posted_at;}
    public function getMessage(): string                    {return $this->message;}
    public function getLikes(): int                         {return $this->likes;}
    public function getComments(): int                      {return $this->comments;}
    public function getShares(): int                        {return $this->shares;}
    public function getReplyTo(): int                       {return $this->reply_to;}
    // ============================ SETTER METHODS ============================
    public function setPostID($post_id): void               {$this->post_id = $post_id;}
    public function setCreator($creator): void              {$this->creator = $creator;}
    public function setPostedAt($posted_at): void           {$this->posted_at = $posted_at;}
    public function setMessage($message): void              {$this->message = $message;}
    public function setLikes($likes): void                  {$this->likes = $likes;}
    public function setComments($comments): void            {$this->comments = $comments;}
    public function setShares($shares): void                {$this->shares = $shares;}
    public function setReplyTo($reply_to): void             {$this->reply_to = $reply_to;}
}