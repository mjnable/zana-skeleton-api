<?php

declare(strict_types=1);

namespace Modules\Accounts\Infrastructure\Security;

use Modules\Accounts\Application\Ports\Tokens;
use Zana\Configuration\Config;
use Zana\Security\TokenSigner;

/**
 * Signed bearer tokens (security.secret), valid for security.token_lifetime seconds.
 */
final readonly class SecurityTokens implements Tokens
{
    public function __construct(
        private TokenSigner $signer,
        #[Config('security.token_lifetime')]
        private int $lifetime = 3600,
    ) {
    }

    public function issue(string $userId): array
    {
        return ['token' => $this->signer->issue($userId, $this->lifetime), 'token_type' => 'Bearer', 'expires_in' => $this->lifetime];
    }
}
