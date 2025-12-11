<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\JWTAuth;
use App\Services\Auth\JWTService;
use Illuminate\Http\Request;
use Tests\TestCase;

class JWTAuthTest extends TestCase {

    private JWTAuth $middleware;
    private JWTService $jwtService;

    protected function setUp(): void {
        parent::setUp();
        
        $this->jwtService = $this->createMock(JWTService::class);
        $this->middleware = new JWTAuth($this->jwtService);
    }

    public function test_allows_request_with_valid_token(): void {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer valid.jwt.token');

        $payload = (object) [
            'username' => 'testuser',
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $this->jwtService
            ->expects($this->once())
            ->method('validateToken')
            ->with('valid.jwt.token')
            ->willReturn($payload);

        $nextCalled = false;
        $next = function($req) use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['success' => true]);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame($payload, $request->attributes->get('jwt_payload'));
    }

    public function test_returns_401_when_token_not_provided(): void {
        $request = Request::create('/api/test', 'GET');

        $this->jwtService
            ->expects($this->never())
            ->method('validateToken');

        $nextCalled = false;
        $next = function($req) use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['success' => true]);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertFalse($nextCalled);
        $this->assertEquals(401, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Token not provided', $data['error']);
    }

    public function test_returns_401_with_invalid_token(): void {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer invalid.token');

        $this->jwtService
            ->expects($this->once())
            ->method('validateToken')
            ->with('invalid.token')
            ->willReturn(null);

        $nextCalled = false;
        $next = function($req) use (&$nextCalled) {
            $nextCalled = true;
            return response()->json(['success' => true]);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertFalse($nextCalled);
        $this->assertEquals(401, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Invalid or expired token', $data['error']);
    }

    public function test_returns_401_with_expired_token(): void {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer expired.token');

        $this->jwtService
            ->expects($this->once())
            ->method('validateToken')
            ->with('expired.token')
            ->willReturn(null);

        $next = function($req) {
            return response()->json(['success' => true]);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_extracts_bearer_token_from_header(): void {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer test.token.here');

        $payload = (object) ['username' => 'user'];

        $this->jwtService
            ->expects($this->once())
            ->method('validateToken')
            ->with('test.token.here')
            ->willReturn($payload);

        $next = function($req) {
            return response()->json(['success' => true]);
        };

        $this->middleware->handle($request, $next);
    }

    public function test_sets_jwt_payload_in_request_attributes(): void {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'Bearer valid.token');

        $expectedPayload = (object) [
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'iat' => 1234567890,
            'exp' => 1234654290,
        ];

        $this->jwtService
            ->method('validateToken')
            ->willReturn($expectedPayload);

        $capturedRequest = null;
        $next = function($req) use (&$capturedRequest) {
            $capturedRequest = $req;
            return response()->json(['success' => true]);
        };

        $this->middleware->handle($request, $next);

        $this->assertNotNull($capturedRequest);
        $this->assertSame($expectedPayload, $capturedRequest->attributes->get('jwt_payload'));
    }

    public function test_returns_401_with_malformed_authorization_header(): void {
        $request = Request::create('/api/test', 'GET');
        $request->headers->set('Authorization', 'InvalidFormat token');

        $this->jwtService
            ->expects($this->never())
            ->method('validateToken');

        $next = function($req) {
            return response()->json(['success' => true]);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(401, $response->getStatusCode());
    }
}
