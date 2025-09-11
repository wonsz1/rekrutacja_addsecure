<?php
require_once './vendor/autoload.php';

use App\Controller\VehicleController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\{
    Exception\MethodNotAllowedException,
    Exception\ResourceNotFoundException,
    Matcher\UrlMatcher,
    RequestContext,
    Route,
    RouteCollection,
};

try {
    $routes = new RouteCollection();
    $routes->add('index', new Route(
        path: '/vehicles',
        defaults: ['controller' => VehicleController::class, 'method' => 'index'],
        methods: ['GET'],
    ));
    $routes->add('list', new Route(
        path: '/vehicles/list',
        defaults: ['controller' => VehicleController::class, 'method' => 'list'],
        methods: ['GET'],
    ));
    $routes->add('save', new Route(
        path: '/vehicles/save/{id}',
        defaults: ['controller' => VehicleController::class, 'method' => 'save'],
        requirements: ['id' => '[0-9]+'],
        methods: ['POST'],
    ));
    $routes->add('delete', new Route(
        path: '/vehicles/delete/{id}',
        defaults: ['controller' => VehicleController::class, 'method' => 'delete'],
        requirements: ['id' => '[0-9]+'],
        methods: ['POST'],
    ));

    $request = Request::createFromGlobals();

    $context = new RequestContext();
    $context->fromRequest($request);

    $matcher = new UrlMatcher($routes, $context);
    $parameters = $matcher->match($context->getPathInfo());

    $controller = new $parameters['controller'];
    $action = $parameters['method'];
    $controller->$action(
        $parameters['id'] ?? null,
        $request->getMethod() === 'POST' ? $request : null,
    );
} catch (ResourceNotFoundException $e) {
    (new Response(content: $e->getMessage(), status: 404))->send();
} catch (MethodNotAllowedException $e) {
    (new Response(content: 'Method Not Allowed', status: 405))->send();
} catch (Throwable $e) {
    (new Response(content: $e->getMessage(), status: 500))->send();
}
