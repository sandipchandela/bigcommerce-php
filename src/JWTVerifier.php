<?php
namespace App;

require '../vendor/autoload.php';

use Firebase\JWT\JWT;

class JWTVerifier
{
    private $clientSecret;

    public function __construct(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    public function verify($signedPayloadJWT, $expectedStoreHash)
    {
        try {
            $decoded = JWT::decode($signedPayloadJWT, $this->clientSecret, ['HS256']);
            return $decoded->sub === $expectedStoreHash;
        } catch (Exception $e) {
            return false;
        }
    }
}
