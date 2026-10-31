<?php

declare(strict_types=1);

namespace Modules\Accounts\Infrastructure\Persistence;

use Modules\Accounts\Domain\EmailAddress;
use Modules\Accounts\Domain\User;
use Modules\Accounts\Domain\UserId;
use Modules\Accounts\Domain\UserRepositoryInterface;
use Zana\Database\ConnectionInterface;
use Zana\Ddd\Domain\AggregateRoot;
use Zana\Ddd\Domain\DomainException;
use Zana\Ddd\Domain\EntityNotFoundException;

/**
 * Stores User aggregates in the users table. add() inserts a new one and
 * updates a stored one, refusing a change made to an outdated copy. To store
 * a property: add its column to the migration, then to row() and hydrate().
 */
final readonly class DatabaseUserRepository implements UserRepositoryInterface
{
    private const string TABLE = 'users';

    public function __construct(private ConnectionInterface $db)
    {
    }

    public function find(mixed $id): ?User
    {
        $row = $this->db->table(self::TABLE)->where('id', (string) $id)->first();

        return $row === null ? null : self::hydrate($row);
    }

    public function get(mixed $id): User
    {
        return $this->find($id) ?? throw new EntityNotFoundException(sprintf('There is no user %s.', (string) $id));
    }

    public function findByEmail(EmailAddress $email): ?User
    {
        $row = $this->db->table(self::TABLE)->where('email', $email->value)->first();

        return $row === null ? null : self::hydrate($row);
    }

    public function add(AggregateRoot $aggregate): void
    {
        $entity = self::entity($aggregate);
        $version = $entity->getVersion();
        $row = [...self::row($entity), 'version' => $version + 1];

        if ($version === 0) {
            $this->db->table(self::TABLE)->insert(['id' => (string) $entity->id(), ...$row]);
        } elseif ($this->db->table(self::TABLE)->where('id', (string) $entity->id())->where('version', $version)->update($row) !== 1) {
            throw new DomainException(sprintf('The user %s was changed in the meantime; load it again.', (string) $entity->id()));
        }
        $entity->incrementVersion();
    }

    public function remove(AggregateRoot $aggregate): void
    {
        $this->db->table(self::TABLE)->where('id', (string) self::entity($aggregate)->id())->delete();
    }

    /**
     * The stored columns, besides id and version.
     *
     * @return array<string, mixed>
     */
    private static function row(User $entity): array
    {
        return [
            'email' => $entity->email->value,
            'name' => $entity->name,
            'password_hash' => $entity->passwordHash,
            'roles' => json_encode($entity->roles, JSON_THROW_ON_ERROR),
        ];
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function hydrate(array $row): User
    {
        $text = static fn (string $key): string => is_scalar($row[$key] ?? null) ? (string) $row[$key] : '';
        $roles = json_decode($text('roles') ?: '[]', true);

        return User::restore(
            UserId::fromString($text('id')),
            (int) $text('version'),
            new EmailAddress($text('email')),
            $text('name'),
            $text('password_hash'),
            is_array($roles) ? array_values(array_filter($roles, is_string(...))) : [],
        );
    }

    private static function entity(AggregateRoot $aggregate): User
    {
        return $aggregate instanceof User
            ? $aggregate
            : throw new \InvalidArgumentException('Expected a ' . User::class . ', got ' . $aggregate::class . '.');
    }
}
