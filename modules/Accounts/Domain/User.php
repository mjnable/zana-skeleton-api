<?php

declare(strict_types=1);

namespace Modules\Accounts\Domain;

use Zana\Ddd\Domain\AggregateRoot;

/**
 * Someone who can call the API: identified by an e-mail address and a
 * password (kept only as a hash), with roles that grant permissions
 * (config/security.php).
 */
final class User extends AggregateRoot
{
    /**
     * @param list<string> $roles
     */
    private function __construct(
        UserId $id,
        public private(set) EmailAddress $email,
        public private(set) string $name,
        public private(set) string $passwordHash,
        public private(set) array $roles,
    ) {
        parent::__construct($id);
    }

    /**
     * @param list<string> $roles
     */
    public static function create(EmailAddress $email, string $name, string $passwordHash, array $roles = []): self
    {
        return new self(UserId::generate(), $email, trim($name), $passwordHash, array_values(array_unique($roles)));
    }

    /**
     * Rebuild a stored User (used by its repository); records no event.
     *
     * @param list<string> $roles
     */
    public static function restore(UserId $id, int $version, EmailAddress $email, string $name, string $passwordHash, array $roles): self
    {
        $entity = new self($id, $email, $name, $passwordHash, $roles);
        $entity->version = $version;

        return $entity;
    }

    public function changePasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function id(): UserId
    {
        assert($this->id instanceof UserId);

        return $this->id;
    }
}
