<?php

declare(strict_types=1);

namespace Modules\Accounts\Domain;

use Zana\Ddd\Domain\RepositoryInterface;

/**
 * @extends RepositoryInterface<User>
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(EmailAddress $email): ?User;
}
