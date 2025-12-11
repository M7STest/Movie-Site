<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\AuthController;
use App\Services\Auth\JWTService;
use Illuminate\Http\Request;
use Tests\TestCase;

class AuthControllerTest extends TestCase {

    private AuthController $controller;
    private JWTService $jwtService;

    protected function setUp(): void {
        parent::setUp();
        
        $_ENV['AUTH_USERNAME'] = 'testuser';
        $_ENV['AUTH_PASSWORD'] = 'testpass';
        $_SERVER['AUTH_USERNAME'] = 'testuser';
        $_SERVER['AUTH_PASSWORD'] = 'testpass';
        
        $this->jwtService = $this->createMock(JWTService::class);
        $this->controller = new AuthController($this->jwtService);
    }

    protected function tearDown(): void {
        unset($_ENV['AUTH_USERNAME'], $_ENV['AUTH_PASSWORD']);
        unset($_SERVER['AUTH_USERNAME'], $_SERVER['AUTH_PASSWORD']);
        parent::tearDown();
    }

    public function test_login_returns_token_with_valid_credentials(): void {
        $request = Request::create('/login', 'POST', [
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $expectedToken = 'test.jwt.token';
        
        $this->jwtService
            ->expects($this->once())
            ->method('generateToken')
            ->with(['username' => 'testuser'])
            ->willReturn($expectedToken);

        $response = $this->controller->login($request);

        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals($expectedToken, $data['token']);
        $this->assertEquals('Bearer', $data['type']);
        $this->assertEquals(86400, $data['expires_in']);
    }

    public function test_login_returns_401_with_invalid_username(): void {
        $request = Request::create('/login', 'POST', [
            'username' => 'wronguser',
            'password' => 'testpass',
        ]);

        $this->jwtService
            ->expects($this->never())
            ->method('generateToken');

        $response = $this->controller->login($request);

        $this->assertEquals(401, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function test_login_returns_401_with_invalid_password(): void {
        $request = Request::create('/login', 'POST', [
            'username' => 'testuser',
            'password' => 'wrongpass',
        ]);

        $this->jwtService
            ->expects($this->never())
            ->method('generateToken');

        $response = $this->controller->login($request);

        $this->assertEquals(401, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
    }

    public function test_login_returns_401_with_empty_credentials(): void {
        $request = Request::create('/login', 'POST', [
            'username' => '',
            'password' => '',
        ]);

        $this->jwtService
            ->expects($this->never())
            ->method('generateToken');

        $response = $this->controller->login($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_me_returns_user_info_from_jwt_payload(): void {
        $payload = (object) [
            'username' => 'testuser',
            'iat' => 1234567890,
            'exp' => 1234654290,
        ];

        $request = new Request();
        $request->attributes->set('jwt_payload', $payload);

        $response = $this->controller->me($request);

        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('testuser', $data['username']);
        $this->assertEquals(1234567890, $data['iat']);
        $this->assertEquals(1234654290, $data['exp']);
    }

    public function test_me_returns_null_values_when_payload_incomplete(): void {
        $payload = (object) [];

        $request = new Request();
        $request->attributes->set('jwt_payload', $payload);

        $response = $this->controller->me($request);

        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertNull($data['username']);
        $this->assertNull($data['iat']);
        $this->assertNull($data['exp']);
    }

    public function test_login_generates_token_with_username_in_payload(): void {
        $request = Request::create('/login', 'POST', [
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $this->jwtService
            ->expects($this->once())
            ->method('generateToken')
            ->with($this->callback(function($payload) {
                return isset($payload['username']) && $payload['username'] === 'testuser';
            }))
            ->willReturn('token.string');

        $response = $this->controller->login($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
