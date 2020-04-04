<?php

namespace Bambamboole\LaravelCms\Api\Routing;

use Bambamboole\LaravelCms\Api\Http\Controllers\IssueTokenController;
use Bambamboole\LaravelCms\Api\Http\Controllers\OpenApiController;
use Bambamboole\LaravelCms\Api\Http\Controllers\ResourceController;
use Bambamboole\LaravelCms\Api\Http\Controllers\SwaggerUiController;
use Bambamboole\LaravelCms\Core\Http\Middleware\Authenticate;
use Bambamboole\LaravelCms\Menus\Http\Controllers\MenuOrderController;
use Bambamboole\LaravelCms\Menus\Http\Controllers\MenusController;
use Bambamboole\LaravelCms\Options\Http\Controllers\OptionsController;
use Bambamboole\LaravelCms\Publishing\Http\Controllers\Api\PagesController;
use Bambamboole\LaravelCms\Publishing\Http\Controllers\PostsController;
use Bambamboole\LaravelCms\Users\Http\Controllers\ProfileAvatarController;
use Bambamboole\LaravelCms\Users\Http\Controllers\ProfileController;
use Bambamboole\LaravelCms\Users\Http\Controllers\UsersController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Router;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

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

        $this->registerProtectedApiRoutes();
    }

    protected function registerProtectedApiRoutes()
    {
        $this->router
            ->middleware([EnsureFrontendRequestsAreStateful::class, ...$this->middleware, Authenticate::class])
            ->as('cms.api.')
            ->prefix($this->prefix)
            ->group(function (Router $router) {
                $router->get('/pages', [PagesController::class, 'index'])->name('pages.index');
                $router->post('/pages', [PagesController::class, 'store'])->name('pages.store');
                $router->get('/pages/{id}', [PagesController::class, 'show'])->name('pages.show');
                $router->patch('/pages/{id}', [PagesController::class, 'update'])->name('pages.update');
                $router->delete('/pages/{id}', [PagesController::class, 'delete'])->name('pages.delete');

                $router->get('/posts', [PostsController::class, 'index'])->name('posts.index');
                $router->post('/posts', [PostsController::class, 'store'])->name('posts.store');
                $router->get('/posts/{id}', [PostsController::class, 'show'])->name('posts.show');
                $router->patch('/posts/{id}', [PostsController::class, 'update'])->name('posts.update');
                $router->delete('/posts/{id}', [PostsController::class, 'delete'])->name('posts.delete');

                $router->post('/users', [UsersController::class, 'store'])->name('users.store');
                $router->patch('/users/{user}', [UsersController::class, 'update'])->name('users.update');

                $router->post('/profile/avatar', [ProfileAvatarController::class, 'update'])->name('profile.avatar');

                $router->patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

                $router->post('/menus/{name}/items', [MenusController::class, 'store'])->name('menus.store');
                $router->put('/menus/{name}/items/{id}', [MenusController::class, 'update'])->name('menus.update');

                $router->post('/menus/{name}/save_order', [MenuOrderController::class, 'update'])->name('menus.save_order');

                $router->get('/{resource}', [ResourceController::class, 'index'])->name('resources.index');
                $router->post('/{resource}', [ResourceController::class, 'store'])->name('resources.store');
                $router->get('/{resource}/{id}', [ResourceController::class, 'show'])->name('resources.show');
                $router->delete('/{resource}/{id}', [ResourceController::class, 'delete'])->name('resources.delete');
            });
    }
}
