<?php

declare(strict_types=1);

namespace Modules\Accounts\Infrastructure\Security;

use Zana\Security\UserInterface;

/**
 * The user of a request, as the security package sees it: an id and roles.
 */
final readonly class AuthenticatedUser implements UserInterface
{
    /**
     * @param list<string> $roles
     */
    public function __construct(
        private string $id,
        private array $roles,
    ) {
    }

    public function identifier(): string
    {
        return $this->id;
    }

    public function roles(): array
    {
        return $this->roles;
    }
}
