<?php

declare(strict_types=1);

namespace Modules\Accounts\Domain;

use Zana\Ddd\Domain\DomainException;
use Zana\Ddd\Domain\ValueObject;

/**
 * An e-mail address: trimmed, lower-cased, and well formed.
 */
final readonly class EmailAddress extends ValueObject
{
    public string $value;

    /**
     * @throws DomainException
     */
    public function __construct(string $value)
    {
        $value = strtolower(trim($value));
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException('The e-mail address is not valid.');
        }

        $this->value = $value;
    }
}
