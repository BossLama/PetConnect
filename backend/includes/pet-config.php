<?php
/**
 *  =================================================================================
 *  Name        :       pet-config.php
 *  Purpose     :       Configuration file for the pet store application
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       01.11.2024
 *  =================================================================================
 *  
 *  USAGE       :
 *  Include this file in your PHP script to get access to the configuration variables.
 *  
 *  EXAMPLE     :
 *  include 'pet-config.php';
 *  echo DB_HOST
 */

    define('STORAGE_FOLDER', "./storage/");

    define('USER_STORAGE_FILE', STORAGE_FOLDER . "users.json");
    define('RELATIONSSHIP_STORAGE_FILE', STORAGE_FOLDER . "relationships.json");
    define('POST_STORAGE_FILE', STORAGE_FOLDER . "posts.json");
    define('IMAGE_STORAGE_FOLDER', STORAGE_FOLDER . "images/");
    define('NOTIFICATION_STORAGE_FILE', STORAGE_FOLDER . "notifications.json");


    define('JWT_SECRET_FILE', "./keys/jwt_secret.key");
    define('ZIPCODESTACK_API_KEY', "./keys/zipcodestack.key");

?>