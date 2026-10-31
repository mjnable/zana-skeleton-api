<?php

declare(strict_types=1);

namespace Modules\Accounts\Infrastructure\Security;

use Modules\Accounts\Domain\UserRepositoryInterface;
use Zana\Security\UserInterface;
use Zana\Security\UserProviderInterface;

/**
 * Finds the user named by a bearer token, for AuthenticationMiddleware.
 */
final readonly class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function findByIdentifier(string $identifier): ?UserInterface
    {
        $user = $this->users->find($identifier);

        return $user === null ? null : new AuthenticatedUser((string) $user->id(), $user->roles);
    }
}
