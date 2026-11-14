<?php

declare(strict_types=1);

namespace App\Tests;

use App\Bootstrap;
use Zana\Core\ApplicationBuilder;
use Zana\Testing\ApplicationTestCase;

/**
 * Every route, attacked in memory (Zana 2.22, FE-68): anonymous and expired-session requests to the routes that need
 * a user, CSRF, repeated requests, and hostile input on the public ones. A new route is attacked as soon as it exists.
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

        // Leave a route out only with the reason: $this->assertRoutesWithstandAttacks(except: ['POST /api/webhook' => '…']);
        $this->assertRoutesWithstandAttacks();
    }
}
