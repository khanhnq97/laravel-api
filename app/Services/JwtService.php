<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $key;
    private string $algorithm;

    public function __construct()
    {
        $this->key = env('JWT_SECRET');
        $this->algorithm = 'HS256';
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->key, $this->algorithm);
    }

    public function decode(string $token): object
    {
        return JWT::decode($token, new Key($this->key, $this->algorithm));
    }
}
