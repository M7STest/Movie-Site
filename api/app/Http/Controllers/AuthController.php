<?php

namespace App\Http\Controllers;

use App\Services\Auth\JWTService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller {
    
    private JWTService $jwtService;
    
    public function __construct(JWTService $jwtService) {
        $this->jwtService = $jwtService;
    }
    
    public function login(Request $request): JsonResponse {
        $username = $request->input('username');
        $password = $request->input('password');
        
        $validUsername = env('AUTH_USERNAME', 'demo@demo.com');
        $validPassword = env('AUTH_PASSWORD', 'password');
        
        if ($username !== $validUsername || $password !== $validPassword) {
            return response()->json(['error' => 'Invalid credentials test'], 401);
        }
        
        $token = $this->jwtService->generateToken([
            'username' => $username,
        ]);
        
        return response()->json([
            'token' => $token,
            'type' => 'Bearer',
            'expires_in' => 86400,
        ]);
    }
    
    public function me(Request $request): JsonResponse {
        $payload = $request->attributes->get('jwt_payload');
        
        return response()->json([
            'username' => $payload->username ?? null,
            'iat' => $payload->iat ?? null,
            'exp' => $payload->exp ?? null,
        ]);
    }
}
