<?php

declare(strict_types=1);

namespace Modules\Accounts;

use Modules\Accounts\Application\Features\CreateUser\CreateUserHandler;
use Modules\Accounts\Application\Features\IssueToken\IssueTokenHandler;
use Modules\Accounts\Application\Features\ShowProfile\ShowProfileHandler;
use Modules\Accounts\Application\Ports\Passwords;
use Modules\Accounts\Application\Ports\Tokens;
use Modules\Accounts\Domain\UserRepositoryInterface;
use Modules\Accounts\Infrastructure\Persistence\DatabaseUserRepository;
use Modules\Accounts\Infrastructure\Security\SecurityPasswords;
use Modules\Accounts\Infrastructure\Security\SecurityTokens;
use Modules\Accounts\Infrastructure\Security\UserProvider;
use Modules\Accounts\Presentation\Api\ApiInput;
use Modules\Accounts\Presentation\Api\IssueTokenAction;
use Modules\Accounts\Presentation\Api\ShowProfileAction;
use Modules\Accounts\Presentation\Cli\CreateUserCommand;
use Zana\Console\ConsoleKernel;
use Zana\Container\Contracts\ContainerInterface;
use Zana\Container\Contracts\ServiceCollectionInterface;
use Zana\Core\Module;
use Zana\Mediator\HandlerMap;
use Zana\Routing\Router;
use Zana\Security\UserProviderInterface;

/**
 * The users of the API: POST /api/tokens signs in with an e-mail address and
 * a password and returns a bearer token, GET /api/me shows the signed-in user,
 * and user:create adds a user from the console.
 */
final class AccountsModule extends Module
{
    public function name(): string
    {
        return 'Accounts';
    }

    /**
     * @return list<class-string<Module>>
     */
    public function dependsOn(): array
    {
        return [];
    }

    public function register(ServiceCollectionInterface $services): void
    {
        $services
            ->scoped(UserRepositoryInterface::class, DatabaseUserRepository::class)
            ->singleton(Passwords::class, SecurityPasswords::class)
            ->singleton(Tokens::class, SecurityTokens::class)
            // Scoped like the repository it reads (FE-40); AuthenticationMiddleware uses it on every request.
            ->scoped(UserProviderInterface::class, UserProvider::class)
            ->singleton(CreateUserCommand::class)->tag(ConsoleKernel::TAG, CreateUserCommand::class);

        $services->scoped(CreateUserHandler::class);
        $services->scoped(IssueTokenHandler::class);
        $services->scoped(ShowProfileHandler::class);
        $services->singleton(ApiInput::class);
        $services->transient(IssueTokenAction::class);
        $services->transient(ShowProfileAction::class);
        // zana:maker services (the make: commands add lines above; keep this line)
    }

    public function boot(ContainerInterface $container): void
    {
        $container->get(HandlerMap::class)->register(CreateUserHandler::class, IssueTokenHandler::class, ShowProfileHandler::class);
        $container->get(Router::class)->loadAttributes(IssueTokenAction::class, ShowProfileAction::class);
        // zana:maker boot (the make: commands add lines above; keep this line)
    }
}
