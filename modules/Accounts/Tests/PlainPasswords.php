<?php

declare(strict_types=1);

namespace Modules\Accounts\Tests;

use Modules\Accounts\Application\Ports\Passwords;

/**
 * Readable "hashes", for the handler tests: "hashed:<password>".
 */
final class PlainPasswords implements Passwords
{
    /** How many passwords were checked: the cost an attacker could time. */
    public int $verified = 0;

    public function hash(string $plain): string
    {
        return "hashed:{$plain}";
    }

    public function verify(string $plain, string $hash): bool
    {
        $this->verified++;

        return $hash === "hashed:{$plain}";
    }

    public function needsRehash(string $hash): bool
    {
        return false;
    }
}
