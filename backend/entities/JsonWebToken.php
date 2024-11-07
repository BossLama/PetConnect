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

    private $header;
    private $payload;
    private $signature;

    public function __construct(array $parameters)
    {
        $this->header = $parameters['header'] ?? null;
        $this->payload = $parameters['payload'] ?? null;
        $this->signature = $parameters['signature'] ?? null;
    }

    public function asToken()
    {
        $token = base64_encode($this->header) . "." . base64_encode($this->payload) . "." . $this->signature;
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

        $header = base64_encode(json_encode($header));
        $payload = base64_encode(json_encode($payload));


        if (!file_exists(JWT_SECRET_FILE))
        {
            $secret = openssl_random_pseudo_bytes(1028);
            $secret = base64_encode($secret);
            file_put_contents(JWT_SECRET_FILE, $secret);
        }
        $signature = hash_hmac('sha256', $header . "." . $payload, file_get_contents(JWT_SECRET_FILE));

        return new JsonWebToken(['header' => $header, 'payload' => $payload, 'signature' => $signature]);
    }

    public static function verifyToken($token)
    {
        if($token == null || strlen($token) == 0) return false;
        $parts = explode(".", $token);
        if(count($parts) != 3) return false;

        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];

        $header = json_decode(base64_decode($header), true);
        $payload = json_decode(base64_decode($payload), true);

        if($header['alg'] != 'HS256') return false;

        $expectedSignature = hash_hmac('sha256', $parts[0] . "." . $parts[1], file_get_contents(JWT_SECRET_FILE));
        if($signature != $expectedSignature) return false;
        if($payload['exp'] < time()) return false;

        return true;
    }

    public static function fromToken($token)
    {
        if(self::verifyToken($token) == false) return null;
        $parts = explode(".", $token);
        $header = json_decode(base64_decode($parts[0]), true);
        $payload = json_decode(base64_decode($parts[1]), true);
        $signature = $parts[2];
        return new JsonWebToken(['header' => $header, 'payload' => $payload, 'signature' => $signature]);
    }

}

?>