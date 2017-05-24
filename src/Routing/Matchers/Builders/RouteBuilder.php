<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/route-matcher/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Matchers\Builders;

use Closure;
use InvalidArgumentException;
use LogicException;
use Opulence\Routing\Matchers\ClosureRouteAction;
use Opulence\Routing\Matchers\MethodRouteAction;
use Opulence\Routing\Matchers\Middleware\MiddlewareBinding;
use Opulence\Routing\Matchers\Route;
use Opulence\Routing\Matchers\UriTemplates\UriTemplate;

/**
 * Defines the route builder
 */
class RouteBuilder
{
    /** @var array The list of HTTP methods to match on */
    private $httpMethods = [];
    /** @var Closure The action the route takes */
    private $action = null;
    /** @var UriTemplate The URI template */
    private $uriTemplate = null;
    /** @var array The mapping of custom route attribute names => values */
    private $attributes = [];
    /** @var MiddlewareBinding[] The list of middleware bindings on this route */
    private $middlewareBindings = [];
    /** @var string|null The name of this route */
    private $name = null;

    /**
     * @param array $httpMethods The list of HTTP methods the route matches on
     * @param UriTemplate $uriTemplate The URI template the route matches on
     */
    public function __construct(array $httpMethods, UriTemplate $uriTemplate)
    {
        $this->httpMethods = $httpMethods;
        $this->uriTemplate = $uriTemplate;
    }

    /**
     * Builds a route object from all the settings in this builder
     *
     * @return Route The built route
     * @throws LogicException Thrown if no controller was specified
     */
    public function build() : Route
    {
        if ($this->action === null) {
            throw new LogicException('No controller specified for route');
        }

        return new Route(
            $this->httpMethods,
            $this->uriTemplate,
            $this->action,
            $this->middlewareBindings,
            $this->name,
            $this->attributes
        );
    }

    /**
     * Binds the controller the route uses to be a closure
     *
     * @param Closure $controller The closure the route uses
     * @return self For chaining
     */
    public function toClosure(Closure $controller) : self
    {
        $this->action = new ClosureRouteAction($controller);

        return $this;
    }

    /**
     * Binds the controller the route uses to be a method
     *
     * @param string $controllerClassName The name of the class the route goes to
     * @param string $controllerMethodName The name of the method the route goes to
     * @return self For chaining
     */
    public function toMethod(string $controllerClassName, string $controllerMethodName) : self
    {
        $this->action = new MethodRouteAction($controllerClassName, $controllerMethodName);

        return $this;
    }

    /**
     * Binds a custom attribute to the route
     * This is useful for custom route constraint matching
     *
     * @param string $name The name of the attribute
     * @param mixed $value The value of the attribute
     * @return self For chaining
     */
    public function withAttribute(string $name, $value) : self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Binds many custom attributes to the route
     * This is useful for custom route constraint matching
     *
     * @param array $attributes The mapping of custom attribute names => values
     * @return self For chaining
     */
    public function withManyAttributes(array $attributes) : self
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * Binds many middleware bindings to the route
     *
     * @param MiddlewareBinding[]|string[] $middlewareBindings The list of middleware bindings to add, or a single
     *      class name without properties
     * @return self For chaining
     * @throws InvalidArgumentException Thrown if the middleware bindings are not the correct type
     */
    public function withManyMiddleware(array $middlewareBindings) : self
    {
        foreach ($middlewareBindings as $middlewareBinding) {
            if (is_string($middlewareBinding)) {
                $this->middlewareBindings[] = new MiddlewareBinding($middlewareBinding);
            } elseif ($middlewareBinding instanceof MiddlewareBinding) {
                $this->middlewareBindings[] = $middlewareBinding;
            } else {
                throw new InvalidArgumentException(
                    'Middleware binding must either be a string or an instance of ' . MiddlewareBinding::class
                );
            }
        }

        return $this;
    }

    /**
     * Binds a single middleware class to the route
     *
     * @param string $middlewareClassName The name of the middleware class to bind
     * @param array $middlewareProperties Any properties this method relies on
     * @return self For chaining
     */
    public function withMiddleware(string $middlewareClassName, array $middlewareProperties = []) : self
    {
        $this->middlewareBindings[] = new MiddlewareBinding($middlewareClassName, $middlewareProperties);

        return $this;
    }

    /**
     * Binds a name to the route
     *
     * @param string $name The name of the route
     * @return self For chaining
     */
    public function withName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }
}