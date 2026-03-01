<?php

namespace App\Module\User\Create;

use App\Core\Exception\ValidationException;
use App\Core\Responder\TemplateRenderer;
use App\Module\User\Data\UserData;
use App\Module\User\Read\UserReadFinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final readonly class UserCreateAction
{
    public function __construct(
        private LoggerInterface $logger,
        private TemplateRenderer $templateRenderer,
        private UserCreator $userCreator,
        private UserReadFinder $userReadFinder,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $userValues = (array)$request->getParsedBody();

        try {
            $insertId = $this->userCreator->createUser($userValues);
        } catch (ValidationException $e) {
            // 422 → HTMX v4 swaps into #modal-container via hx-status:422 on the form
            return $this->templateRenderer->render(
                $response->withStatus(422),
                'admin/users/partials/user-form.php',
                [
                    'user' => new UserData($userValues),
                    'errors' => $e->validationErrors,
                ],
            );
        }

        $this->logger->info('User "' . $userValues['email'] . '" created. ID: ' . $insertId);

        // 201 → HTMX appends this <tr> to #user-table-body (the form's hx-target)
        $newUser = $this->userReadFinder->getUserById($insertId);

        return $this->templateRenderer->render(
            $response->withStatus(201)
                ->withHeader('HX-Trigger', json_encode([
                    'showFlashMessage' => [
                        'type' => 'success',
                        'message' => 'User created.',
                    ],
                    'closeModal' => true,
                ])),
            'admin/users/partials/user-row.php',
            ['user' => $newUser],
        );
    }
}
