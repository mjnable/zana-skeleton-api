<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Features\IssueToken;

use Modules\Accounts\Application\Ports\Passwords;
use Modules\Accounts\Application\Ports\Tokens;
use Modules\Accounts\Domain\EmailAddress;
use Modules\Accounts\Domain\UserRepositoryInterface;
use Zana\Ddd\Domain\DomainException;
use Zana\Mediator\Attribute\Handles;

/**
 * Checks an e-mail address and password and issues a bearer token. The
 * answer never tells whether the address exists.
 */
#[Handles(IssueTokenCommand::class)]
final readonly class IssueTokenHandler
{
    public function __construct(
        private UserRepositoryInterface $users,
        private Passwords $passwords,
        private Tokens $tokens,
    ) {
    }

    /**
     * @return array{token: string, token_type: 'Bearer', expires_in: int}
     *
     * @throws InvalidCredentials
     */
    public function __invoke(IssueTokenCommand $command): array
    {
        try {
            $user = $this->users->findByEmail(new EmailAddress($command->email));
        } catch (DomainException) {
            $user = null;
        }
        // A password is checked either way, so the time taken does not tell which accounts exist.
        $valid = $this->passwords->verify($command->password, $user?->passwordHash ?? $this->dummyHash());
        if ($user === null || !$valid) {
            throw new InvalidCredentials();
        }

        if ($this->passwords->needsRehash($user->passwordHash)) {
            $user->changePasswordHash($this->passwords->hash($command->password));
            $this->users->add($user);
        }

        return $this->tokens->issue((string) $user->id());
    }

    private function dummyHash(): string
    {
        static $hash = null;

        return $hash ??= $this->passwords->hash(bin2hex(random_bytes(16)));
    }
}
