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
    private $prefix;            //prefixes are used to define the eligble usergroup who can see this post (00 = private,  01 = friends, 02 = everyone)
    private $type;              //the type of the post (00 = normal post, 01 = comment)
    private $post_id;           //Unique Post ID to identify a post
    private $shares;            //how often a post was shared
    private $likes;             //how often a post was liked
    private $comments;          //how often a post was commented
    private $creator;           //user_id
    private $posted_at;         //posted_at
    private $message;           //text message of the comment
    private $post_nmr;          //the part of the post ID that makes the ID unique
    private $reply_to;          //PostID under which the reply is posted
    private $related_image_id;  //Related Image
    private $related_meetup_id; //Related Meetup

    //TODO: related_image_id
    //TODO: related_meetup_id


    public function __construct (array $post)
    {
        $this->prefix = $post['prefix'] ?? 00;
        $this->type = $post['type'] ?? 00;
        $this->post_nmr = $post['post_nmr'] ?? null;
        $this->shares = $post['shares'] ?? 0;
        $this->likes = $post['likes'] ?? 0;
        $this->comments = $post['comment'] ?? 0;
        $this->creator = $post['creator'] ?? null;
        $this->posted_at = $post['posted_at'] ?? date('Y-m-d H:i:s');
        $this->message = $post['message'] ?? null;
        $this->related_image_id = $post['related_image_id'] ?? null;
        $this->related_meetup_id = $post['related_meetup_id'] ?? null;

        if ( $this->type == 01)
        {
            $this->reply_to = $post['reply_to'] ?? null;
        }

    }

    public function generatePostID(): string
    {
        $id = md5(uniqid(rand(), true));
        return $this->prefix+$this->type+$this->$id;
    }

    public function generatePost(): array
    {
        return array(
            'post_id'=> $this->generatePostID(),
            'creator'=> $this->creator,
            'posted_at'=> $this->posted_at,
            'message'=> $this->message,
            'likes' => 0,
            'comments' => 0,
            'shares' => 0,
            'related_image_id'=> $this->related_image_id,
            'related_meetup_id'=> $this->related_meetup_id
        );
    }

    //TODO: Delete Post

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