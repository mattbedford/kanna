<?php

namespace App\Module\User\Delete;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Interfaces\RouteParserInterface;

final readonly class UserDeleteAction
{
    public function __construct(
        private LoggerInterface $logger,
        private UserDeleter $userDeleter,
        private RouteParserInterface $routeParser,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args,
    ): ResponseInterface {
        $userIdToDelete = (int)$args['user_id'];

        $this->userDeleter->deleteUser($userIdToDelete);

        $this->logger->info('User deleted. ID: ' . $userIdToDelete);

        $flashTrigger = json_encode([
            'showFlashMessage' => [
                'type' => 'success',
                'message' => 'User deleted.',
            ],
        ]);

        // When deleting from the edit page, redirect back to the list
        if ($request->getHeaderLine('X-Delete-Context') === 'edit-page') {
            return $response
                ->withStatus(200)
                ->withHeader('HX-Redirect', $this->routeParser->urlFor('user-list-page'))
                ->withHeader('HX-Trigger', $flashTrigger);
        }

        // From the list page: return empty body so HTMX removes the <tr>
        return $response
            ->withStatus(200)
            ->withHeader('HX-Trigger', $flashTrigger);
    }
}
