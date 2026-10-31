<?php

declare(strict_types=1);

namespace Modules\Accounts\Tests;

use Modules\Accounts\Application\Ports\Tokens;

final class FixedTokens implements Tokens
{
    public function issue(string $userId): array
    {
        return ['token' => "token-for-{$userId}", 'token_type' => 'Bearer', 'expires_in' => 60];
    }
}
