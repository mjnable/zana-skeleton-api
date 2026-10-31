<?php

declare(strict_types=1);

namespace Modules\Accounts\Tests\Domain;

use Modules\Accounts\Domain\EmailAddress;
use PHPUnit\Framework\TestCase;
use Zana\Ddd\Domain\DomainException;

final class EmailAddressTest extends TestCase
{
    public function testAddressesAreTrimmedAndLowerCased(): void
    {
        $this->assertSame('ada@example.com', (new EmailAddress(' Ada@Example.COM '))->value);
        $this->assertTrue((new EmailAddress('ada@example.com'))->equals(new EmailAddress('ADA@example.com')));
        $this->assertFalse((new EmailAddress('ada@example.com'))->equals(new EmailAddress('grace@example.com')));
    }

    public function testAnInvalidAddressIsRefused(): void
    {
        $this->expectException(DomainException::class);

        new EmailAddress('ada at example.com');
    }
}
