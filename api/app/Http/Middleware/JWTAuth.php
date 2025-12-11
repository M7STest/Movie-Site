<?php

namespace App\Http\Middleware;

use App\Services\Auth\JWTService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTAuth {
    
    private JWTService $jwtService;
    
    public function __construct(JWTService $jwtService) {
        $this->jwtService = $jwtService;
    }
    
    public function handle(Request $request, Closure $next): Response {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
        
        $payload = $this->jwtService->validateToken($token);
        
        if (!$payload) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }
        
        $request->attributes->set('jwt_payload', $payload);
        
        return $next($request);
    }
}
