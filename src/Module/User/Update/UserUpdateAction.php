<?php

namespace App\Module\User\Update;

use App\Core\Exception\ValidationException;
use App\Core\Responder\TemplateRenderer;
use App\Module\User\Data\UserData;
use App\Module\User\Read\UserReadFinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UserUpdateAction
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
        private UserUpdater $userUpdater,
        private UserReadFinder $userReadFinder,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args,
    ): ResponseInterface {
        $userId = (int)$args['user_id'];
        $userValues = (array)$request->getParsedBody();

        try {
            $this->userUpdater->updateUser($userId, $userValues);
        } catch (ValidationException $e) {
            // Build a UserData from submitted values so the form repopulates
            $user = new UserData(array_merge(['id' => $userId], $userValues));
            // Preserve timestamps from the database
            $existingUser = $this->userReadFinder->findUserById($userId);
            $user->createdAt = $existingUser->createdAt;
            $user->updatedAt = $existingUser->updatedAt;

            return $this->templateRenderer->render(
                $response->withStatus(422),
                'admin/users/partials/user-edit-form.php',
                [
                    'user' => $user,
                    'errors' => $e->validationErrors,
                ],
            );
        }

        // Fetch updated user and return the form partial
        $updatedUser = $this->userReadFinder->getUserById($userId);

        return $this->templateRenderer->render(
            $response->withHeader('HX-Trigger', json_encode([
                'showFlashMessage' => [
                    'type' => 'success',
                    'message' => 'User updated.',
                ],
            ])),
            'admin/users/partials/user-edit-form.php',
            ['user' => $updatedUser],
        );
    }
}
