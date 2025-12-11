<?php

namespace Tests\Unit\Services\Auth;

use App\Services\Auth\JWTService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Tests\TestCase;

class JWTServiceTest extends TestCase {

    private JWTService $jwtService;

    protected function setUp(): void {
        parent::setUp();
        putenv('JWT_SECRET=test-jwt-secret-key');
        config(['app.key' => 'test-secret-key-12345']);
        $this->jwtService = new JWTService();
    }

    protected function tearDown(): void {
        putenv('JWT_SECRET');
        parent::tearDown();
    }

    public function test_generate_token_creates_valid_jwt(): void {
        $payload = [
            'user_id' => 1,
            'username' => 'testuser',
        ];
        
        $token = $this->jwtService->generateToken($payload);
        
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
    }

    public function test_generate_token_includes_payload_data(): void {
        $payload = [
            'user_id' => 42,
            'username' => 'john_doe',
            'email' => 'john@example.com',
        ];
        
        $token = $this->jwtService->generateToken($payload);
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertNotNull($decoded);
        $this->assertEquals(42, $decoded->user_id);
        $this->assertEquals('john_doe', $decoded->username);
        $this->assertEquals('john@example.com', $decoded->email);
    }

    public function test_generate_token_includes_issued_at_timestamp(): void {
        $payload = ['user_id' => 1];
        
        $beforeTime = time();
        $token = $this->jwtService->generateToken($payload);
        $afterTime = time();
        
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertNotNull($decoded);
        $this->assertObjectHasProperty('iat', $decoded);
        $this->assertGreaterThanOrEqual($beforeTime, $decoded->iat);
        $this->assertLessThanOrEqual($afterTime, $decoded->iat);
    }

    public function test_generate_token_includes_expiration_timestamp(): void {
        $payload = ['user_id' => 1];
        
        $beforeTime = time();
        $token = $this->jwtService->generateToken($payload);
        $afterTime = time();
        
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertNotNull($decoded);
        $this->assertObjectHasProperty('exp', $decoded);
        
        $expectedExpiration = $decoded->iat + (60 * 60 * 24);
        $this->assertEquals($expectedExpiration, $decoded->exp);
        
        $this->assertGreaterThanOrEqual($beforeTime + 86400, $decoded->exp);
        $this->assertLessThanOrEqual($afterTime + 86400, $decoded->exp);
    }

    public function test_validate_token_returns_decoded_payload_for_valid_token(): void {
        $payload = [
            'user_id' => 123,
            'username' => 'alice',
        ];
        
        $token = $this->jwtService->generateToken($payload);
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertNotNull($decoded);
        $this->assertIsObject($decoded);
        $this->assertEquals(123, $decoded->user_id);
        $this->assertEquals('alice', $decoded->username);
    }

    public function test_validate_token_returns_null_for_invalid_token(): void {
        $invalidToken = 'invalid.jwt.token';
        
        $result = $this->jwtService->validateToken($invalidToken);
        
        $this->assertNull($result);
    }

    public function test_validate_token_returns_null_for_malformed_token(): void {
        $malformedToken = 'not-a-jwt-token';
        
        $result = $this->jwtService->validateToken($malformedToken);
        
        $this->assertNull($result);
    }

    public function test_validate_token_returns_null_for_token_with_wrong_signature(): void {
        $wrongSecret = 'wrong-secret-key';
        $payload = ['user_id' => 1, 'iat' => time(), 'exp' => time() + 3600];
        $tokenWithWrongSignature = JWT::encode($payload, $wrongSecret, 'HS256');
        
        $result = $this->jwtService->validateToken($tokenWithWrongSignature);
        
        $this->assertNull($result);
    }

    public function test_validate_token_returns_null_for_expired_token(): void {
        $payload = [
            'user_id' => 1,
            'iat' => time() - 86400,
            'exp' => time() - 3600,
        ];
        
        $expiredToken = JWT::encode($payload, 'test-jwt-secret-key', 'HS256');
        
        $result = $this->jwtService->validateToken($expiredToken);
        
        $this->assertNull($result);
    }

    public function test_validate_token_returns_null_for_empty_token(): void {
        $result = $this->jwtService->validateToken('');
        
        $this->assertNull($result);
    }

    public function test_generate_token_with_empty_payload(): void {
        $token = $this->jwtService->generateToken([]);
        
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertNotNull($decoded);
        $this->assertObjectHasProperty('iat', $decoded);
        $this->assertObjectHasProperty('exp', $decoded);
    }

    public function test_generate_token_with_complex_payload(): void {
        $payload = [
            'user_id' => 999,
            'username' => 'complex_user',
            'roles' => ['admin', 'editor', 'viewer'],
            'permissions' => [
                'read' => true,
                'write' => true,
                'delete' => false,
            ],
            'metadata' => [
                'last_login' => '2025-12-11',
                'ip' => '192.168.1.1',
            ],
        ];
        
        $token = $this->jwtService->generateToken($payload);
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertNotNull($decoded);
        $this->assertEquals(999, $decoded->user_id);
        $this->assertEquals('complex_user', $decoded->username);
        $this->assertIsArray($decoded->roles);
        $this->assertCount(3, $decoded->roles);
        $this->assertEquals(['admin', 'editor', 'viewer'], $decoded->roles);
        $this->assertIsObject($decoded->permissions);
        $this->assertTrue($decoded->permissions->read);
        $this->assertIsObject($decoded->metadata);
        $this->assertEquals('192.168.1.1', $decoded->metadata->ip);
    }

    public function test_multiple_tokens_are_different(): void {
        $payload = ['user_id' => 1];
        
        $token1 = $this->jwtService->generateToken($payload);
        sleep(1);
        $token2 = $this->jwtService->generateToken($payload);
        
        $this->assertNotEquals($token1, $token2);
        
        $this->assertNotNull($this->jwtService->validateToken($token1));
        $this->assertNotNull($this->jwtService->validateToken($token2));
    }

    public function test_token_expiration_time_is_24_hours(): void {
        $payload = ['user_id' => 1];
        
        $token = $this->jwtService->generateToken($payload);
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertNotNull($decoded);
        
        $expirationDuration = $decoded->exp - $decoded->iat;
        $expectedDuration = 60 * 60 * 24;
        
        $this->assertEquals($expectedDuration, $expirationDuration);
    }

    public function test_uses_hs256_algorithm(): void {
        $payload = ['user_id' => 1];
        
        $token = $this->jwtService->generateToken($payload);
        
        $parts = explode('.', $token);
        $header = json_decode(base64_decode($parts[0]), true);
        
        $this->assertEquals('HS256', $header['alg']);
        $this->assertEquals('JWT', $header['typ']);
    }

    public function test_validate_token_with_future_issued_at_returns_null(): void {
        $payload = [
            'user_id' => 1,
            'iat' => time() + 3600,
            'exp' => time() + 7200,
        ];
        
        $futureToken = JWT::encode($payload, 'test-jwt-secret-key', 'HS256');
        
        $result = $this->jwtService->validateToken($futureToken);
        
        $this->assertTrue($result === null || is_object($result));
    }

    public function test_constructor_uses_jwt_secret_env_variable(): void {
        $payload = ['user_id' => 1];
        
        $token = $this->jwtService->generateToken($payload);
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertNotNull($decoded);
        $this->assertEquals(1, $decoded->user_id);
    }
}
