<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Features\ShowProfile;

use Zana\Ddd\Application\Query;
use Zana\Validation\Constraint\NotBlank;

final class ShowProfileQuery extends Query
{
    public function __construct(
        #[NotBlank]
        public readonly string $userId,
    ) {
        parent::__construct();
    }
}
