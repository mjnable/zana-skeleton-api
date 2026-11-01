<?php

declare(strict_types=1);

namespace Modules\Accounts\Tests\Presentation;

use App\Bootstrap;
use Zana\Core\ApplicationBuilder;
use Zana\Testing\ApplicationTestCase;

final class TokensApiTest extends ApplicationTestCase
{
    protected function createApplicationBuilder(): ApplicationBuilder
    {
        return Bootstrap::builder(dirname(__DIR__, 4))
            ->withEnvironment('testing')
            ->withConfiguration(['cache.default' => 'memory', 'cache.stores.memory' => ['driver' => 'array']]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(0, $this->console()->run('migrate')->exitCode(), 'a fresh database (config/testing)');
        $created = $this->console()->run('user:create ada@example.com "Ada Lovelace" --password=correct-horse-battery --role=admin');
        $this->assertSame(0, $created->exitCode(), $created->output());
    }

    public function testATokenSignsTheUserIn(): void
    {
        $token = $this->http()->post('/api/tokens', ['email' => 'Ada@Example.com', 'password' => 'correct-horse-battery'])
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('expires_in', 3600)
            ->jsonPath('token');
        $this->assertIsString($token);

        $this->http()->withToken($token)->get('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'ada@example.com')
            ->assertJsonPath('data.name', 'Ada Lovelace')
            ->assertJsonPath('data.roles', ['admin']);
    }

    public function testWrongCredentialsAreRefusedWithoutSayingWhichPart(): void
    {
        $wrongPassword = $this->http()->post('/api/tokens', ['email' => 'ada@example.com', 'password' => 'wrong'])->assertUnauthorized();
        $unknownUser = $this->http()->post('/api/tokens', ['email' => 'grace@example.com', 'password' => 'wrong'])->assertUnauthorized();

        $this->assertSame($wrongPassword->jsonPath('detail'), $unknownUser->jsonPath('detail'));
        $wrongPassword->assertHeader('WWW-Authenticate', 'Bearer');
    }

    public function testMissingFieldsAreRefused(): void
    {
        $errors = $this->http()->post('/api/tokens', [])->assertUnprocessable()->jsonPath('errors');

        $this->assertIsArray($errors);
        $this->assertEqualsCanonicalizing(['email', 'password'], array_keys($errors));
    }

    public function testSigningInIsRateLimited(): void
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->http()->post('/api/tokens', ['email' => 'ada@example.com', 'password' => 'wrong'])->assertUnauthorized();
        }

        $this->http()->post('/api/tokens', ['email' => 'ada@example.com', 'password' => 'correct-horse-battery'])->assertStatus(429);
    }

    public function testTheProfileNeedsAValidToken(): void
    {
        $this->http()->get('/api/me')->assertUnauthorized();
        $this->http()->withToken('not-a-token')->get('/api/me')->assertUnauthorized();
    }

    public function testErrorsAreProblemJsonForEveryClient(): void
    {
        $response = $this->http()->get('/api/nothing-here', headers: ['Accept' => 'text/html'])->assertNotFound();

        $response->assertHeader('Content-Type', 'application/problem+json; charset=utf-8');
        $this->assertSame(404, $response->jsonPath('status'));
    }

    public function testTheConsoleRefusesInvalidUsersFieldByField(): void
    {
        $result = $this->console()->run('user:create not-an-address "Grace" --password=short');

        $this->assertSame(1, $result->exitCode());
        $this->assertStringContainsString('email: This value is not a valid email address.', $result->output());
        $this->assertStringContainsString('password:', $result->output(), 'ValidationBehavior refuses the command before its handler runs');
    }
}
