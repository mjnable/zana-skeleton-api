<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Features\IssueToken;

use Zana\Ddd\Application\Command;
use Zana\Validation\Constraint\NotBlank;

final class IssueTokenCommand extends Command
{
    public function __construct(
        #[NotBlank]
        public readonly string $email,
        #[NotBlank]
        public readonly string $password,
    ) {
        parent::__construct();
    }
}
