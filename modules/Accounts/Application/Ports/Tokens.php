<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Ports;

/**
 * Bearer tokens for users; Infrastructure\Security\SecurityTokens implements it.
 */
interface Tokens
{
    /**
     * @return array{token: string, token_type: 'Bearer', expires_in: int}
     */
    public function issue(string $userId): array;
}
