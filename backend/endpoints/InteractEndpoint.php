<?php

namespace endpoints;
class InteractEndpoint extends Endpoint
{

    // Return a method not allowed response
    public function onPost() : array
    {
        $interaction = $this->parameters["interaction"] ?? null;    // 0 = comment, 1 = like, 2 = share
        $post_id     = $this->parameters["post_id"] ?? null;
        $comment     = $this->parameters["comment"] ?? null;

        if($interaction == null || $post_id == null)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Missing parameters";
            $response["code"]       = "400";
            $response["hint"]       = "Interaction and post_id are required";
            return $response;
        }

        require_once "./entities/JsonWebToken.php";
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

        switch($interaction)
        {
            // Comment
            case 0:
                require_once "./entities/Post.php";
                $post = \entities\Post::findByID($post_id);
                if($post == null)
                {
                    $response               = array();
                    $response["status"]     = "error";
                    $response["message"]    = "Post does not exist";
                    $response["code"]       = "400";
                    $response["hint"]       = "Post does not exist";
                    return $response;
                }

                $commentParams = array();
                $commentParams["visibility"]    = $post->getVisibility();
                $commentParams["creator"]       = $user_id;
                $commentParams["reply_to"]      = $post_id;
                $commentParams["message"]       = $comment;
                $commentParams["type"]          = 1;

                $comment = new \entities\Post($commentParams);
                $comment->save();

                $response               = array();
                $response["status"]     = "success";
                $response["message"]    = "Comment created";
                $response["code"]       = "200";
                $response["hint"]       = "Comment created";
                return $response;
            // Like
            case 1:
                require_once "./entities/Post.php";
                $post = \entities\Post::findByID($post_id);
                if($post == null)
                {
                    $response               = array();
                    $response["status"]     = "error";
                    $response["message"]    = "Post does not exist";
                    $response["code"]       = "400";
                    $response["hint"]       = "Post does not exist";
                    return $response;
                }

                // Remove like if already liked
                if($post->hasLiked($user_id))
                {
                    $post->removeLike($user_id);
                    $response               = array();
                    $response["status"]     = "success";
                    $response["message"]    = "Like removed";
                    $response["code"]       = "200";
                    $response["type"]       = "removed";
                    $response["hint"]       = "Like removed";
                    return $response;

                }
                // Add like if not liked
                else
                {
                    $post->addLike($user_id);
                    $response               = array();
                    $response["status"]     = "success";
                    $response["message"]    = "Like added";
                    $response["code"]       = "200";
                    $response["type"]       = "added";
                    $response["hint"]       = "Like added";
                    return $response;
                }
            default:
                $response               = array();
                $response["status"]     = "error";
                $response["message"]    = "Invalid interaction";
                $response["code"]       = "400";
                $response["hint"]       = "Invalid interaction";
                return $response;
        }

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