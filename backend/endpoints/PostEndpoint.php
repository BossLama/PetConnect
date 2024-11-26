<?php
/**
 *  =================================================================================
 *  Name        :       PostEndpoint.php
 *  Purpose     :       Endpoint for managing posts, including creation and retrieval.
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last Edited :       01.11.2024
 *  =================================================================================
 *  
 * Class PostEndpoint
 * 
 * This class provides an API endpoint for creating and retrieving posts. 
 * It supports operations like fetching a single post, retrieving a list of posts, 
 * and creating new posts. Authentication is enforced via JSON Web Tokens.
 *
 * Methods:
 * - onGet(): Handles fetching posts.
 *     - Fetches a specific post by its ID, or retrieves a paginated list of visible posts.
 *     - Validates the user's token and permissions.
 *     - Includes creator information and like status in the response.
 *
 * - onPost(): Handles post creation.
 *     - Validates the token and input parameters (e.g., message length).
 *     - Allows optional inclusion of an image.
 *     - Saves the post and returns success details.
 *
 * - onDelete(), onPut(): Return a "method not allowed" error.
 * 
 * Each method returns a structured JSON response containing status, message, code, 
 * and optional hints or data. Error handling ensures appropriate responses for 
 * invalid requests, missing parameters, or unauthorized actions.
 *
 * Usage:
 * - This endpoint is part of the `endpoints` namespace and extends a base `Endpoint` class for integration.
 * - Relies on classes in the `entities` namespace, such as `Post` and `JsonWebToken`, to manage posts and user authentication.
 * - Enforces authentication and access control using JWTs.
 *
 * @package endpoints
 */

namespace endpoints;
class PostEndpoint extends Endpoint
{

    public function onGet() : array
    {
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

        $token_valid = \entities\JsonWebToken::fromToken($tokenString);
        $token_payload = $token_valid->payload;

        $post_id = $this->get_parameters['post_id'] ?? null;
        $min     = $this->get_parameters['min'] ?? 0;
        $limit   = $this->get_parameters['limit'] ?? 10;

        if(isset($post_id))
        {
            require_once "./entities/Post.php";
            $post = \entities\Post::findByID($post_id);
            if($post == null)
            {
                $response               = array();
                $response["status"]     = "error";
                $response["message"]    = "Post not found";
                $response["code"]       = "404";
                $response["hint"]       = "Post with ID $post_id does not exist";
                return $response;
            }

            $response               = array();
            $response["status"]     = "success";
            $response["message"]    = "Post found";
            $response["code"]       = "200";
            $response["data"]       = $post->toArray();
            return $response;
        }

        require_once "./entities/Post.php";
        $posts = \entities\Post::getAll();
        $filtered_posts = array();

        foreach($posts as $post)
        {
            if($post->canSee($token_payload['user_id']))
            {
                $postArray = $post->toArray();
                require_once "./entities/UserProfile.php";
                $profileCreator = \entities\UserProfile::findByID($postArray['creator']);
                $profileCreator = $profileCreator->asPrivateArray();
                $profileCreator['password'] = null;
                $profileCreator['email'] = null;
                $profileCreator['created_at'] = null;
                $profileCreator['last_login'] = null;

                $postArray['creator'] = $profileCreator;
                $postArray['liked']   = $post->hasLiked($token_payload['user_id']);
                $filtered_posts[] = $postArray;
            }
        }

        $max = $min + $limit;
        if($max > count($filtered_posts)) $max = count($filtered_posts);
        $filtered_posts = array_slice($filtered_posts, $min, $max);

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Posts found";
        $response["code"]       = "200";
        $response["data"]       = $filtered_posts;
        return $response;
    }

    public function onPost() : array
    {
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

        $token_valid = \entities\JsonWebToken::fromToken($tokenString);
        $token_payload = $token_valid->payload;

        $visibility         = $this->parameters['visibility'] ?? 0;
        $reply_to           = $this->parameters['reply_to'] ?? null;
        $type               = 0;
        $creator            = $token_payload['user_id'];
        $message            = $this->parameters['message'] ?? "";
        $image              = $this->parameters['image'] ?? null;
        $missing_report     = $this->parameters['missing_report'] ?? null;
        $related_image_id   = null;

        if($message == "" || strlen($message) < 10)
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Bitte geben Sie einen Text von min. 10 Zeichen an";
            $response["given"]      = $message;
            $response["code"]       = "400";
            $response["hint"]       = "Please provide a message";
            return $response;
        }

        if(isset($image))
        {
            if(is_dir(IMAGE_STORAGE_FOLDER) == false) mkdir(IMAGE_STORAGE_FOLDER, 0700, true);
            $related_image_id = md5(uniqid(rand(), true));
            $imagePath = IMAGE_STORAGE_FOLDER . $related_image_id . ".image";
            file_put_contents($imagePath, base64_decode($image));
        }

        require_once "./entities/Post.php";
        $post = new \entities\Post(array(
            'visibility'    => $visibility,
            'reply_to'      => $reply_to,
            'type'          => $type,
            'creator'       => $creator,
            'missing_report'=> $missing_report,
            'message'       => $message,
            'related_image_id' => $related_image_id
        ));
        $post->save();

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Post wurde erstellt";
        $response["code"]       = "200";
        $response["hint"]       = "Post has been created";
        return $response;
    }

    public function onDelete() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Please use a valid request method";
        return $response;
    }

    public function onPut() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Please use a valid request method";
        return $response;
    }

}

?>