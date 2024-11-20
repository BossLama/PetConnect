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
                $profileCreator = $profileCreator->asArray();
                $profileCreator['password'] = null;
                $profileCreator['email'] = null;
                $profileCreator['created_at'] = null;
                $profileCreator['last_login'] = null;
                $postArray['creator'] = $profileCreator;

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
        $type               = 0;
        $creator            = $token_payload['user_id'];
        $message            = $this->parameters['message'] ?? "";
        $image              = $this->parameters['image'] ?? null;
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
            'type'          => $type,
            'creator'       => $creator,
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