<?php

declare(strict_types=1);

namespace Modules\Accounts\Presentation\Api;

use Modules\Accounts\Application\Features\IssueToken\InvalidCredentials;
use Modules\Accounts\Application\Features\IssueToken\IssueTokenCommand;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zana\Http\Exception\UnauthorizedHttpException;
use Zana\Http\ResponseFactory;
use Zana\Mediator\MediatorInterface;
use Zana\Routing\Attribute\Route;
use Zana\Security\Attribute\RateLimit;
use Zana\Security\Middleware\RateLimitMiddleware;

/**
 * POST /api/tokens with {"email": …, "password": …}: answers
 * {"token": …, "token_type": "Bearer", "expires_in": 3600}, 401 for a wrong
 * e-mail address or password, 422 for missing fields, and 429 after ten
 * attempts a minute from one client. Send the token as
 * "Authorization: Bearer <token>".
 */
final readonly class IssueTokenAction
{
    public function __construct(
        private ApiInput $input,
        private MediatorInterface $mediator,
        private ResponseFactory $responses,
    ) {
    }

    #[Route('/api/tokens', methods: ['POST'], name: 'accounts.tokens', middleware: [RateLimitMiddleware::class])]
    #[RateLimit(max: 10, per: 60)]
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return $this->responses->json($this->mediator->send($this->input->read($request, IssueTokenCommand::class)));
        } catch (InvalidCredentials $e) {
            throw new UnauthorizedHttpException($e->getMessage(), ['WWW-Authenticate' => 'Bearer'], $e);
        }
    }
}
