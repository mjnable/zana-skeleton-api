<?php

declare(strict_types=1);

namespace Modules\Accounts\Presentation\Api;

use Modules\Accounts\Application\Features\ShowProfile\ShowProfileQuery;
use Psr\Http\Message\ResponseInterface;
use Zana\Http\Exception\UnauthorizedHttpException;
use Zana\Http\ResponseFactory;
use Zana\Mediator\MediatorInterface;
use Zana\Routing\Attribute\Route;
use Zana\Security\Attribute\RequiresAuthentication;
use Zana\Security\Middleware\AuthorizationMiddleware;
use Zana\Security\SecurityContext;

/**
 * GET /api/me with a bearer token: {"data": {"id", "email", "name", "roles"}}
 * of the signed-in user; 401 without a valid token.
 */
final readonly class ShowProfileAction
{
    public function __construct(
        private SecurityContext $security,
        private MediatorInterface $mediator,
        private ResponseFactory $responses,
    ) {
    }

    #[Route('/api/me', methods: ['GET'], name: 'accounts.me', middleware: [AuthorizationMiddleware::class])]
    #[RequiresAuthentication]
    public function __invoke(): ResponseInterface
    {
        $profile = $this->mediator->send(new ShowProfileQuery($this->security->requireUser()->identifier()))
            ?? throw new UnauthorizedHttpException('The user of this token no longer exists.', ['WWW-Authenticate' => 'Bearer']);

        return $this->responses->json(['data' => $profile]);
    }
}
