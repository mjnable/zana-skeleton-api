<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Features\CreateUser;

use Modules\Accounts\Application\Ports\Passwords;
use Modules\Accounts\Domain\EmailAddress;
use Modules\Accounts\Domain\EmailAlreadyUsed;
use Modules\Accounts\Domain\User;
use Modules\Accounts\Domain\UserRepositoryInterface;
use Zana\Mediator\Attribute\Handles;

#[Handles(CreateUserCommand::class)]
final readonly class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private Passwords $passwords,
    ) {
    }

    /**
     * @return string The new user's id
     */
    public function __invoke(CreateUserCommand $command): string
    {
        $email = new EmailAddress($command->email);
        if ($this->users->findByEmail($email) !== null) {
            throw new EmailAlreadyUsed($email);
        }

        $user = User::create($email, $command->name, $this->passwords->hash($command->password), $command->roles);
        $this->users->add($user);

        return (string) $user->id();
    }
}
