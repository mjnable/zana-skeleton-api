<?php

declare(strict_types=1);

namespace Modules\Accounts\Tests;

use Modules\Accounts\Domain\EmailAddress;
use Modules\Accounts\Domain\User;
use Modules\Accounts\Domain\UserRepositoryInterface;
use Zana\Ddd\Domain\AggregateRoot;
use Zana\Ddd\Domain\EntityNotFoundException;

/**
 * Users in memory, for the handler tests.
 */
final class InMemoryUsers implements UserRepositoryInterface
{
    /** @var array<string, User> */
    public array $users = [];

    public function find(mixed $id): ?User
    {
        return $this->users[(string) $id] ?? null;
    }

    public function get(mixed $id): User
    {
        return $this->find($id) ?? throw new EntityNotFoundException("There is no user {$id}.");
    }

    public function findByEmail(EmailAddress $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->email->equals($email)) {
                return $user;
            }
        }

        return null;
    }

    public function add(AggregateRoot $aggregate): void
    {
        assert($aggregate instanceof User);
        $this->users[(string) $aggregate->id()] = $aggregate;
    }

    public function remove(AggregateRoot $aggregate): void
    {
        assert($aggregate instanceof User);
        unset($this->users[(string) $aggregate->id()]);
    }
}
