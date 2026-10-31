<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Features\IssueToken;

/**
 * The e-mail address or the password is wrong; which one is not said.
 */
final class InvalidCredentials extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('The e-mail address or the password is wrong.');
    }
}
