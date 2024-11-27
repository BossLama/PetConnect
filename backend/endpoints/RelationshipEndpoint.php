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
class RelationshipEndpoint extends Endpoint
{

    public function onGet() : array
    {

        $relationship_id = $this->parameters['relationship_id'] ?? null;

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

        if(isset($relationship_id))
        {
            require_once "./entities/Relationship.php";
            $relationship = \entities\Relationship::findByID($relationship_id);
            if($relationship)
            {
                $response               = array();
                $response["status"]     = "success";
                $response["message"]    = "Beziehung gefunden";
                $response["code"]       = "200";
                $response["data"]       = $relationship->asArray();
                return $response;
            }
            else
            {
                $response               = array();
                $response["status"]     = "error";
                $response["message"]    = "Beziehung nicht gefunden";
                $response["code"]       = "404";
                $response["hint"]       = "Beziehung mit ID nicht gefunden";
                return $response;
            }
        }
        else
        {
            require_once "./entities/Relationship.php";
            $relationships = \entities\Relationship::findByToOrFrom($user_id);
            $response               = array();
            $response["status"]     = "success";
            $response["message"]    = "Beziehungen gefunden";
            $response["code"]       = "200";
            $response["data"]       = $relationships;
            return $response;
        }
        
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
        $sender = \entities\JsonWebToken::fromToken($tokenString)->payload['user_id'];
        $receiver = $this->parameters['receiver'] ?? null;

        if(!isset($receiver))
        {
            $response               = array();
            $response["status"]     = "error";
            $response["message"]    = "Empfänger nicht angegeben";
            $response["code"]       = "400";
            $response["hint"]       = "Empfänger muss angegeben werden";
            return $response;
        }

        require_once "./entities/Relationship.php";
        // Check if a relationship already exists
        $relationship = \entities\Relationship::findByFromAndTo($sender, $receiver);
        if($relationship)
        {
            if($relationship->getStatus() == 0)
            {
                $response               = array();
                $response["status"]     = "error";
                $response["message"]    = "Diese Anfrage wurde blockiert";
                $response["code"]       = "400";
                $response["hint"]       = "Nutzer haben sich blockiert";
                return $response;
            }
            else if($relationship->getStatus() == 1)
            {
                if($relationship->getToUser() == $sender)
                {

                    require_once "./entities/Notification.php";
                    $notification = new \entities\Notification([]);
                    $notification->setType(0);
                    $notification->setReceiver($relationship->getFromUser());
                    $notification->setSender($relationship->getToUser());
                    $notification->setRelatedItemID($relationship->getRelationID());
                    $notification->setMessage("Freundschaftsanfrage wurde akzeptiert");
                    $notification->save();

                    $relationship->setStatus(2);
                    $relationship->save();
                    $response               = array();
                    $response["status"]     = "success";
                    $response["message"]    = "Anfrage wurde akzeptiert";
                    $response["code"]       = "200";
                    $response["hint"]       = "Anfrage wurde akzeptiert";
                    return $response;
                }
                else
                {
                    require_once "./entities/Notification.php";
                    $notifications = \entities\Notification::findByRelatedItem($relationship->getRelationID(), 0);
                    foreach($notifications as $notification)
                    {
                        $notification->delete();
                    }

                    $relationship->delete();
                    $response               = array();
                    $response["status"]     = "success";
                    $response["message"]    = "Anfrage wurde zurückgezogen";
                    $response["code"]       = "200";
                    $response["hint"]       = "Anfrage wurde zurückgezogen";
                    return $response;
                }
            }
            else if($relationship->getStatus() == 2)
            {

                require_once "./entities/Notification.php";
                $notifications = \entities\Notification::findByRelatedItem($relationship->getRelationID(), 0);
                foreach($notifications as $notification)
                {
                    $notification->delete();
                }

                $relationship->delete();
                $response               = array();
                $response["status"]     = "success";
                $response["message"]    = "Freundschaft wurde gelöscht";
                $response["code"]       = "200";
                $response["hint"]       = "Freundschaft wurde gelöscht";
                return $response;
            }
        }
        else
        {

            require_once "./entities/Notification.php";
            $notification = new \entities\Notification([]);
            $notification->setType(0);
            $notification->setReceiver($receiver);
            $notification->setSender($sender);
            $notification->setMessage("Neue Freundschaftsanfrage erhalten");
            $notification->save();

            $relationship = new \entities\Relationship([]);
            $relationship->setFromUser($sender);
            $relationship->setToUser($receiver);
            $relationship->setStatus(1);
            $relationship->save();
            $response               = array();
            $response["status"]     = "success";
            $response["message"]    = "Anfrage wurde gesendet";
            $response["code"]       = "200";
            $response["hint"]       = "Anfrage wurde gesendet";
            return $response;
        }

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