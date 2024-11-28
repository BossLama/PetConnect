<?php
/**
 *  =================================================================================
 *  Name        :       JsonWebToken.php
 *  Purpose     :       Entity class for the JSON Web Token
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       01.11.2024
 *  =================================================================================
 *  
 *  USAGE       :
 *  Include this file in your PHP script to get access to the JsonWebToken class.
 *  
 *  EXAMPLE     :
 *  $token = new JsonWebToken(array());             // Create a new token with parameters in array
 *  $token->asToken();                              // Returns the token as a string
 *  $token = JsonWebToken::create($user);           // Create a new token for a user
 *  $token = JsonWebToken::verifyToken($token);     // Verify a token - returns true or false
 *  $token = JsonWebToken::fromToken($token);       // Create a token object from a token string
 */

namespace entities;
class JsonWebToken
{

    public $header;
    public $payload;
    public $signature;

    public function __construct(array $parameters)
    {
        $this->header = $parameters['header'] ?? null;
        $this->payload = $parameters['payload'] ?? null;
        $this->signature = $parameters['signature'] ?? null;
    }

    public function asToken()
    {
        $jHeader = json_encode($this->header);
        $jPayload = json_encode($this->payload);
        $token = self::encodeBase64URL($jHeader) . "." . self::encodeBase64URL($jPayload) . "." . $this->signature;
        return $token;
    }

    public static function create($user)
    {
        $header = 
        [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = 
        [
            'user_id' => $user->getUserId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $header = json_encode($header);
        $payload = json_encode($payload);


        if (!file_exists(JWT_SECRET_FILE))
        {
            $secret = openssl_random_pseudo_bytes(1028);
            $secret = base64_encode($secret);
            file_put_contents(JWT_SECRET_FILE, $secret);
        }
        $signature = hash_hmac('sha256', $header . "." . $payload, file_get_contents(JWT_SECRET_FILE));

        $header = json_decode($header, true);
        $payload = json_decode($payload, true);

        return new JsonWebToken(['header' => $header, 'payload' => $payload, 'signature' => $signature]);
    }

    public static function verifyToken($token)
    {
        // Error logger
        require_once "./includes/ErrorLogger.php";

        if($token == null || strlen($token) == 0)
        {
            \includes\ErrorLogger::log("Token is null or empty");
            return false;
        }
        $parts = explode(".", $token);
        if(count($parts) != 3)
        {
            \includes\ErrorLogger::log("Token has invalid format");
            return false;
        }

        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];

        $header = self::decodeBase64URL($header);
        $payload = self::decodeBase64URL($payload);

        $signature_check = hash_hmac('sha256', $header . "." . $payload, file_get_contents(JWT_SECRET_FILE));

        $payload = json_decode($payload, true);
        $exp = $payload['exp'] ?? 0;

        if($exp < time())
        {
            \includes\ErrorLogger::log("Token has expired");
            return false;
        }

        if($signature != $signature_check)
        {
            \includes\ErrorLogger::log("Token signature is invalid");
            return false;
        }

        return $signature == $signature_check;        
    }

    public static function fromToken($token)
    {
        if(self::verifyToken($token) == false) return null;
        $parts = explode(".", $token);
        $header = json_decode(self::decodeBase64URL($parts[0]), true);
        $payload = json_decode(self::decodeBase64URL($parts[1]), true);
        $signature = $parts[2];
        return new JsonWebToken(['header' => $header, 'payload' => $payload, 'signature' => $signature]);
    }


    private static function encodeBase64URL($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function decodeBase64URL($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

}

?>