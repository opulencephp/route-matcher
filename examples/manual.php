<?php
use Opulence\Router\Route;
use Opulence\Router\RouteAction;
use Opulence\Router\Router;
use Opulence\Router\UriTemplates\RegexUriTemplate;

// Create a route manually
$route = new Route(
    ['GET'],
    new RouteAction(null, null, function ($request, $routeVars) {
        return "Hello, {$routeVars['userId']}";
    }),
    new RegexUriTemplate('#^http://foo\.com/users/(?P<userId>\d+)$#'),
    ['MyMiddlewareClass'],
    'MyProfile'
);

// Get the matched route
$router = new Router([$route]);
$matchedRoute = $router->route($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

// Use your library/framework of choice to dispatch $matchedRoute...
