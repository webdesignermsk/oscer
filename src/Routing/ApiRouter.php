<?php

namespace Bambamboole\LaravelCms\Routing;

use Bambamboole\LaravelCms\Auth\Http\Controllers\Api\IssueTokenController;
use Bambamboole\LaravelCms\Auth\Http\Middleware\Authenticate;
use Bambamboole\LaravelCms\Core\Http\Controllers\OpenApiController;
use Bambamboole\LaravelCms\Core\Http\Controllers\SwaggerUiController;
use Bambamboole\LaravelCms\Menus\Http\Controllers\MenuOrderController;
use Bambamboole\LaravelCms\Menus\Http\Controllers\MenusController;
use Bambamboole\LaravelCms\Options\Http\Controllers\OptionsController;
use Bambamboole\LaravelCms\Permissions\Http\Controllers\PermissionsController;
use Bambamboole\LaravelCms\Permissions\Http\Controllers\RolesController;
use Bambamboole\LaravelCms\Publishing\Http\Controllers\Api\PagesController;
use Bambamboole\LaravelCms\Users\Http\Controllers\ProfileAvatarController;
use Bambamboole\LaravelCms\Users\Http\Controllers\ProfileController;
use Bambamboole\LaravelCms\Users\Http\Controllers\UsersController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Router;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middlewares\PermissionMiddleware;

class ApiRouter
{
    protected Router $router;

    protected string $prefix = 'api/cms/';

    protected array $middleware = [
        'throttle:60,1',
        SubstituteBindings::class,
    ];

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function registerApiRoutes()
    {
        $this->router
            ->middleware($this->middleware)
            ->as('cms.api.')
            ->prefix($this->prefix)
            ->group(function (Router $router) {
                $router->post('/auth/token', IssueTokenController::class)->name('auth.token');
                $router->get('/swagger-ui', SwaggerUiController::class)->name('swagger-ui');
                $router->get('/open-api/reference/definition.yaml', [OpenApiController::class, 'reference'])->name('oas.reference');
                $router->get('/open-api/{folder}/{file}', [OpenApiController::class, 'file'])->name('oas.file');
            });
        $this->router->aliasMiddleware('permission', PermissionMiddleware::class);

        $this->registerProtectedApiRoutes();
    }

    protected function registerProtectedApiRoutes()
    {
        $this->router
            ->middleware([EnsureFrontendRequestsAreStateful::class, ...$this->middleware, Authenticate::class])
            ->as('cms.api.')
            ->prefix($this->prefix)
            ->group(function (Router $router) {
                // Pages
                $router->get('/pages', [PagesController::class, 'index'])->name('pages.index');
                $router->post('/pages', [PagesController::class, 'store'])->name('pages.store');
                $router->get('/pages/{id}', [PagesController::class, 'show'])->name('pages.index');
                $router->patch('/pages/{id}', [PagesController::class, 'update'])->name('pages.update');
                $router->delete('/pages/{id}', [PagesController::class, 'delete'])->name('pages.delete');

                // Users
                $router->get('/users', [UsersController::class, 'index'])->name('users.index');
                $router->post('/users', [UsersController::class, 'store'])->name('users.store');
                $router->get('/users/{user}', [UsersController::class, 'show'])->name('users.index');
                $router->patch('/users/{user}', [UsersController::class, 'update'])->name('users.update');
                $router->delete('/users/{user}', [UsersController::class, 'delete'])->name('users.delete');

                // Roles
                $router->get('/roles', [RolesController::class, 'index'])->name('roles.index');
                $router->get('/roles/{role}', [RolesController::class, 'show'])->name('roles.index');
//                $router->post('/roles', [RolesController::class, 'store'])->name('roles.store');
//                $router->patch('/roles/{role}', [RolesController::class, 'update'])->name('roles.update');
//                $router->delete('/roles/{role}', [RolesController::class, 'delete'])->name('roles.delete');

                // Permissions
                $router->get('/permissions', [PermissionsController::class, 'index'])->name('permissions.index');

                // Profile
                $router->post('/profile/avatar', [ProfileAvatarController::class, 'update'])->name('profile.avatar');
                $router->patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

                // Options
                $router->get('/options', [OptionsController::class, 'index'])->name('options.index');
                $router->post('/options', [OptionsController::class, 'store'])->name('options.store');

                // Menus
                $router->get('/menus', [MenusController::class, 'index'])->name('menus.index');
                $router->get('/menus/{name}', [MenusController::class, 'show'])->name('menus.show');
                $router->post('/menus/{name}/items', [MenusController::class, 'store'])->name('menus.store');
                $router->put('/menus/{name}/items/{id}', [MenusController::class, 'update'])->name('menus.update');
                $router->delete('/menus/{name}/items/{id}', [MenusController::class, 'delete'])->name('menus.delete');

                $router->post('/menus/{name}/save_order', [MenuOrderController::class, 'update'])->name('menus.save_order');
            });
        $this->router->aliasMiddleware('permission', PermissionMiddleware::class);
    }
}
