<?php

namespace App\Module\User\Create;

use App\Core\Responder\TemplateRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UserCreateFormAction
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args,
    ): ResponseInterface {
        return $this->templateRenderer->render($response, 'admin/users/partials/user-form.php');
    }
}
