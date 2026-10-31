<?php

declare(strict_types=1);

namespace Modules\Accounts\Application\Features\ShowProfile;

use Modules\Accounts\Domain\UserRepositoryInterface;
use Zana\Mediator\Attribute\Handles;

#[Handles(ShowProfileQuery::class)]
final readonly class ShowProfileHandler
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    /**
     * @return array{id: string, email: string, name: string, roles: list<string>}|null
     */
    public function __invoke(ShowProfileQuery $query): ?array
    {
        $user = $this->users->find($query->userId);

        return $user === null ? null : [
            'id' => (string) $user->id(),
            'email' => $user->email->value,
            'name' => $user->name,
            'roles' => $user->roles,
        ];
    }
}
