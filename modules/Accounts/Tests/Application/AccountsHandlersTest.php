<?php

declare(strict_types=1);

namespace Modules\Accounts\Tests\Application;

use Modules\Accounts\Application\Features\CreateUser\CreateUserCommand;
use Modules\Accounts\Application\Features\CreateUser\CreateUserHandler;
use Modules\Accounts\Application\Features\IssueToken\InvalidCredentials;
use Modules\Accounts\Application\Features\IssueToken\IssueTokenCommand;
use Modules\Accounts\Application\Features\IssueToken\IssueTokenHandler;
use Modules\Accounts\Application\Features\ShowProfile\ShowProfileHandler;
use Modules\Accounts\Application\Features\ShowProfile\ShowProfileQuery;
use Modules\Accounts\Domain\EmailAlreadyUsed;
use Modules\Accounts\Tests\FixedTokens;
use Modules\Accounts\Tests\InMemoryUsers;
use Modules\Accounts\Tests\PlainPasswords;
use PHPUnit\Framework\TestCase;

final class AccountsHandlersTest extends TestCase
{
    private InMemoryUsers $users;

    protected function setUp(): void
    {
        $this->users = new InMemoryUsers();
    }

    public function testCreatingAUserStoresTheHashOnly(): void
    {
        $id = $this->create('Ada@Example.com', ['admin']);

        $user = $this->users->get($id);
        $this->assertSame('ada@example.com', $user->email->value);
        $this->assertSame('hashed:correct-horse-battery', $user->passwordHash);
        $this->assertSame(['admin'], $user->roles);
    }

    public function testAnEmailAddressBelongsToOneUser(): void
    {
        $this->create('ada@example.com');

        $this->expectException(EmailAlreadyUsed::class);
        $this->create('ADA@example.com');
    }

    public function testATokenIsIssuedForTheRightPasswordOnly(): void
    {
        $id = $this->create('ada@example.com');
        $issue = new IssueTokenHandler($this->users, new PlainPasswords(), new FixedTokens());

        $this->assertSame("token-for-{$id}", $issue(new IssueTokenCommand('ada@example.com', 'correct-horse-battery'))['token']);

        foreach ([['ada@example.com', 'wrong'], ['grace@example.com', 'correct-horse-battery'], ['not an address', 'x']] as [$email, $password]) {
            try {
                $issue(new IssueTokenCommand($email, $password));
                $this->fail("{$email} / {$password} must be refused.");
            } catch (InvalidCredentials $e) {
                $this->assertSame('The e-mail address or the password is wrong.', $e->getMessage());
            }
        }
    }

    public function testTheProfile(): void
    {
        $id = $this->create('ada@example.com');
        $show = new ShowProfileHandler($this->users);

        $this->assertSame(['id' => $id, 'email' => 'ada@example.com', 'name' => 'Ada Lovelace', 'roles' => []], $show(new ShowProfileQuery($id)));
        $this->assertNull($show(new ShowProfileQuery('01a1940c-3882-7345-928a-ac1a4d9338b2')));
    }

    /**
     * @param list<string> $roles
     */
    private function create(string $email, array $roles = []): string
    {
        return (new CreateUserHandler($this->users, new PlainPasswords()))(new CreateUserCommand($email, 'Ada Lovelace', 'correct-horse-battery', $roles));
    }
}
