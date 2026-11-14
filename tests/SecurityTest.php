<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bootstrap;
use Psr\Http\Message\ServerRequestInterface;
use Zana\Core\ApplicationBuilder;
use Zana\Testing\ApplicationTestCase;

/**
 * Every route, attacked in memory (Zana 2.22, FE-68): anonymous and expired-session requests to the routes that need
 * a user, then the same as a signed-in user without roles, CSRF, repeated requests, and hostile input. A new route is
 * attacked as soon as it exists.
 */
final class SecurityTest extends ApplicationTestCase
{
    protected function createApplicationBuilder(): ApplicationBuilder
    {
        return Bootstrap::builder(dirname(__DIR__))
            ->withEnvironment('testing')
            ->withConfiguration(['cache.default' => 'memory', 'cache.stores.memory' => ['driver' => 'array']]);
    }

    public function testEveryRouteWithstandsTheBaselineAttacks(): void
    {
        $this->assertSame(0, $this->console()->run('migrate')->exitCode(), 'the routes read the database');

        // A user without roles, signed in as the API's clients are: with a token from POST /api/tokens.
        $created = $this->console()->run('user:create grace@example.com "Grace Hopper" --password=correct-horse-battery');
        $this->assertSame(0, $created->exitCode(), $created->output());
        $token = $this->http()->post('/api/tokens', ['email' => 'grace@example.com', 'password' => 'correct-horse-battery'])->assertOk()->jsonPath('token');
        $this->assertIsString($token);

        // Leave a route out only with the reason: except: ['POST /api/webhook' => '…'].
        $this->assertRoutesWithstandAttacks(signIn: static fn (ServerRequestInterface $request): ServerRequestInterface => $request->withHeader('Authorization', "Bearer {$token}"));
    }
}
