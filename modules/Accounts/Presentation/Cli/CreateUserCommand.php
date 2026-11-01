<?php

declare(strict_types=1);

namespace Modules\Accounts\Presentation\Cli;

use Modules\Accounts\Application\Features\CreateUser\CreateUserCommand as CreateUser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zana\Console\Command;
use Zana\Ddd\Domain\DomainException;
use Zana\Mediator\MediatorInterface;

/**
 * php bin/console user:create ada@example.com "Ada Lovelace" [--role=admin]
 * asks for the password (or takes --password, e.g. in scripts). Invalid
 * input is refused by ValidationBehavior (config/mediator.php), which the
 * console shows field by field.
 */
#[AsCommand(name: 'user:create', description: 'Create a user who can sign in to the API')]
final class CreateUserCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'The password; asked for when left out')
            ->addOption('role', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'A role from config/security.php, e.g. admin');
    }

    public function handle(MediatorInterface $mediator, InputInterface $input, SymfonyStyle $io): int
    {
        /** @var list<string> $roles */
        $roles = $input->getOption('role');
        $password = $input->getOption('password') ?? $io->askHidden('Password (at least 10 characters)');
        $command = new CreateUser((string) $input->getArgument('email'), (string) $input->getArgument('name'), (string) $password, $roles);

        try {
            $id = $mediator->send($command);
        } catch (DomainException $e) {
            $io->error($e->getMessage());

            return self::FAILURE;
        }
        $io->success(sprintf('Created user %s (%s).', $command->email, is_string($id) ? $id : ''));

        return self::SUCCESS;
    }
}
