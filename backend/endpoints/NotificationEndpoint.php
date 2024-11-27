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
class NotificationEndpoint extends Endpoint
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
        $user_id = \entities\JsonWebToken::fromToken($tokenString)->payload['user_id'];

        require_once './entities/Notification.php';
        $notifications = \entities\Notification::findByReceiver($user_id);
        $filtered_notifications = array();

        // delete all notifications
        foreach($notifications as $notification)
        {
            $filtered_notifications[] = $notification->toArray();
            $notification->delete();
        }

        usort($filtered_notifications, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $response = array();
        $response["status"] = "success";
        $response["message"] = "Notifications fetched successfully";
        $response["code"] = "200";
        $response["notifications"] = $filtered_notifications;
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