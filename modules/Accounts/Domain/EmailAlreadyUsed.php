<?php

declare(strict_types=1);

namespace Modules\Accounts\Domain;

use Zana\Ddd\Domain\DomainException;

/**
 * Two users cannot share an e-mail address.
 */
final class EmailAlreadyUsed extends DomainException
{
    public function __construct(EmailAddress $email)
    {
        parent::__construct("A user with the e-mail address {$email->value} already exists.");
    }
}
