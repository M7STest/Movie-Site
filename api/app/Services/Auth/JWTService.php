<?php

namespace App\Services\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTService {
    
    private string $secret;
    private string $algorithm = 'HS256';
    
    public function __construct() {
        $this->secret = env('JWT_SECRET', config('app.key'));
    }
    
    public function generateToken(array $payload): string {
        $issuedAt = time();
        $expire = $issuedAt + (60 * 60 * 24);
        
        $data = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
        ]);
        
        return JWT::encode($data, $this->secret, $this->algorithm);
    }
    
    public function validateToken(string $token): ?object {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (Exception $e) {
            return null;
        }
    }
}
