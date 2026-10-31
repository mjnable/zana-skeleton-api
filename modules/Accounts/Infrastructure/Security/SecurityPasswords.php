<?php

declare(strict_types=1);

namespace Modules\Accounts\Infrastructure\Security;

use Modules\Accounts\Application\Ports\Passwords;
use Zana\Security\PasswordHasher;

/**
 * Passwords with the security package: Argon2id, or bcrypt without it.
 */
final readonly class SecurityPasswords implements Passwords
{
    public function __construct(private PasswordHasher $hasher)
    {
    }

    public function hash(string $plain): string
    {
        return $this->hasher->hash($plain);
    }

    public function verify(string $plain, string $hash): bool
    {
        return $this->hasher->verify($plain, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return $this->hasher->needsRehash($hash);
    }
}
