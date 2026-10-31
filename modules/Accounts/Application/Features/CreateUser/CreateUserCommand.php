<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Features\CreateUser;

use Zana\Ddd\Application\Command;
use Zana\Validation\Constraint\Email;
use Zana\Validation\Constraint\Length;
use Zana\Validation\Constraint\NotBlank;

final class CreateUserCommand extends Command
{
    /**
     * @param list<string> $roles
     */
    public function __construct(
        #[NotBlank, Email]
        public readonly string $email,
        #[NotBlank, Length(max: 200)]
        public readonly string $name,
        #[NotBlank, Length(min: 10, max: 4096)]
        public readonly string $password,
        public readonly array $roles = [],
    ) {
        parent::__construct();
    }
}
