<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Ports;

/**
 * Password hashing, which the Application layer uses without depending on
 * the security package; Infrastructure\Security\SecurityPasswords implements it.
 */
interface Passwords
{
    public function hash(string $plain): string;

    public function verify(string $plain, string $hash): bool;

    public function needsRehash(string $hash): bool;
}
