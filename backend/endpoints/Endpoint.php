<?php

namespace endpoints;
abstract class Endpoint
{

    public abstract function handleRequest(array $parameters, string $token, bool $requires_auth);

    
    public function authenticate(string $token)
    {
        $token = \entities\JsonWebToken::fromToken($token);
        if (!$token->verify()) return null;
        $payload = $token->payload;
        if(!isset($payload['user_id'])) return null;
        $user = \entities\User::fromId($payload['user_id']);
        return $user;
    }

}
?>