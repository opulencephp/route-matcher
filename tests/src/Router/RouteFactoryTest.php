<?php
namespace Opulence\Router;

use Opulence\Router\Builders\RouteBuilderRegistry;
use Opulence\Router\Caching\IRouteCache;

/**
 * Tests the route factory
 */
class RouteFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var IRouteCache|\PHPUnit_Framework_MockObject_MockObject The route cache to use */
    private $routeCache = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->routeCache = $this->createMock(IRouteCache::class);
    }

    /**
     * Tests that a cache hit returns the cached routes
     */
    public function testCacheHitReturnsCachedRoutes() : void
    {
        $callback = function (RouteBuilderRegistry $routes) {
            // Don't do anything
        };
        $routeCollection = new RouteCollection();
        $factory = new RouteFactory($callback, $this->routeCache);
        $this->routeCache->expects($this->once())
            ->method('get')
            ->willReturn($routeCollection);
        $this->assertSame($routeCollection, $factory->createRoutes());
    }

    /**
     * Tests that a cache miss calls the route builder registry and caches its results
     */
    public function testCacheMissCallsRouteBuilderRegistryAndCachesItsResults() : void
    {
        $callback = function (RouteBuilderRegistry $routes) {
            // Don't do anything
        };
        $routeBuilderRegistry = new RouteBuilderRegistry();
        $routeCollection = $routeBuilderRegistry->buildAll();
        $factory = new RouteFactory($callback, $this->routeCache, $routeBuilderRegistry);
        $this->routeCache->expects($this->once())
            ->method('get')
            ->willReturn(null);
        $this->routeCache->expects($this->once())
            ->method('set')
            ->with($routeCollection);
        $this->assertEquals($routeCollection, $factory->createRoutes());
    }

    /**
     * Tests not using the cache causes the callback to be called every time
     */
    public function testNotUsingCacheCallCallbackEveryTime() : void
    {
        $callback = function (RouteBuilderRegistry $routes) {
            // Don't do anything
        };
        $routeBuilderRegistry = new RouteBuilderRegistry();
        $routeCollection = $routeBuilderRegistry->buildAll();
        $factory = new RouteFactory($callback, null, $routeBuilderRegistry);
        $this->assertEquals($routeCollection, $factory->createRoutes());
    }
}