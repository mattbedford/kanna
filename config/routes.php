<?php

use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    // Home page
    $app->get('/', \App\Module\Home\HomePageAction::class)->setName('home-page');
    $app->get('/home', \App\Module\Home\RedirectToHomePageAction::class);

    // Admin
    $app->get('/admin', \App\Module\Admin\Dashboard\AdminDashboardAction::class)->setName('admin-dashboard');

    // User action routes
    $app->group('/users', function (RouteCollectorProxy $group) {
        // User list page (server-rendered)
        $group->get('/list', \App\Module\User\List\UserListPageAction::class)
            ->setName('user-list-page');
        // Create form partial (HTMX fetches this into a modal)
        $group->get('/create', \App\Module\User\Create\UserCreateFormAction::class)
            ->setName('user-create-form');
        // Fetch user list for Ajax call
        $group->get('', \App\Module\User\List\UserFetchListAction::class)
            ->setName('user-list');
        // Submit user creation form
        $group->post('', \App\Module\User\Create\UserCreateAction::class)
            ->setName('user-create-submit');
        // User read page
        $group->get('/{user_id:[0-9]+}', \App\Module\User\Read\UserReadPageAction::class)
            ->setName('user-read-page');
        // Submit user update form
        $group->put('/{user_id:[0-9]+}', \App\Module\User\Update\UserUpdateAction::class)
            ->setName('user-update-submit');
        // Submit delete user
        $group->delete('/{user_id:[0-9]+}', \App\Module\User\Delete\UserDeleteAction::class)
            ->setName('user-delete-submit');
    });

    // API routes
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/users', \App\Module\User\List\ApiUserFetchListAction::class)->setName(
            'api-fetch-users-list'
        );
    })// Cross-Origin Resource Sharing (CORS) middleware. Allow another domain to access '/api' routes.
    // If an error occurs, the CORS middleware will not be executed and the exception caught and a response
    // sent without the appropriate access control header. I don't know how to execute a certain middleware
    // added to a route group only before the error middleware which is added last in the middleware.php file.
    ->add(\App\Core\Middleware\CorsMiddleware::class);

    /**
     * Catch-all route to serve a 404 Not Found page if none of the routes match
     * This route must be defined last.
     */
    $app->map(
        ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
        '/{routes:.+}',
        function ($request, $response) {
            // Throw exception 404 with route that was not found.
            throw new HttpNotFoundException(
                $request,
                'Route "' . $request->getUri()->getHost() . $request->getUri()->getPath() . '" not found.'
            );
        }
    );
};
