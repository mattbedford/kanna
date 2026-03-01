<?php

namespace App\Module\Admin\Dashboard;

use App\Core\Responder\TemplateRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AdminDashboardAction
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->templateRenderer->render(
            $response,
            'admin/dashboard.php',
        );
    }
}
