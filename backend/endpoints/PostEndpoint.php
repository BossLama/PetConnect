<?php
/**
 *  =================================================================================
 *  File        :       ZipcodestackEndpoint.php
 *  Description :       Endpoint class for handling ZIP code lookups using the Zipcodestack API.
 *                      This endpoint processes GET requests to fetch location data based on
 *                      a provided ZIP code and country. Only GET requests are supported.
 *                      Other request methods (POST, DELETE, PUT) return an error response.
 *
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last Edited :       01.11.2024
 *  
 *  Methods:
 *      - onGet(): Handles ZIP code lookup by calling the Zipcodestack API, validates the
 *                 API key, retrieves data for the specified ZIP code and country, and 
 *                 returns the city if found.
 *      - onPost(), onDelete(), onPut(): Return a "method not allowed" error.
 *
 *  Notes:
 *      - Requires a defined API key constant ZIPCODESTACK_API_KEY for API requests.
 *      - Returns JSON responses with status, message, code, and additional data or hints.
 *
 *  =================================================================================
 */



namespace endpoints;
class PostEndpoint extends Endpoint
{

    public function onGet() : array
    {
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
                $filtered_posts[] = $post;
            }
        }

        $posts = array_slice($filtered_posts, $min, $limit);

        $response               = array();
        $response["status"]     = "success";
        $response["message"]    = "Posts found";
        $response["code"]       = "200";
        $response["data"]       = $posts;
        return $response;
    }

    public function onPost() : array
    {
        $response               = array();
        $response["status"]     = "error";
        $response["message"]    = "Method is not allowed";
        $response["code"]       = "400";
        $response["hint"]       = "Please use a valid request method";
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