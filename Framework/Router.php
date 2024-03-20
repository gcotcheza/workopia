<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router
{
    protected $routes = [];

    /**
     * Add a new route
     */
    public function registerRoute(string $method, string $uri, string $action, array $middleware = []): void
    {
        list($controller, $controllerMethod) = explode('@', $action);

        $this->routes[] = [
            'method'           => $method,
            'uri'              => $uri,
            'controller'       => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware'       => $middleware,
        ];
    }

    /**
     * Add a GET route.
     */
    public function get(string $uri, string $controller, array $middleware = []): void
    {
        $this->registerRoute('GET', $uri, $controller, $middleware);
    }

    /**
     * Add a POST route.
     */
    public function post(string $uri, string $controller, array $middleware = []): void
    {
        $this->registerRoute('POST', $uri, $controller, $middleware);
    }

    /**
     * Add a PATCH route.
     */
    public function patch(string $uri, string $controller, array $middleware = []): void
    {
        $this->registerRoute('PATCH', $uri, $controller, $middleware);
    }

    /**
     * Add a PUT route.
     */
    public function put(string $uri, string $controller, array $middleware = []): void
    {
        $this->registerRoute('PUT', $uri, $controller, $middleware);
    }

    /**
     * Add a DELETE route.
     */
    public function delete(string $uri, string $controller, array $middleware = []): void
    {
        $this->registerRoute('DELETE', $uri, $controller, $middleware);
    }

    /**
     * Route the request.
     */
    public function route(string $uri): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Check for _mehod input
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            // Override the request method with the value of the _method
            $requestMethod = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {

            // Split the current uri into segment
            $uriSegments = explode('/', trim($uri, '/'));

            // Split the route URI into segments.
            $routeSegments = explode('/', trim($route['uri'], '/'));

            $match = true;

            // Check if the number of segments matches
            if (count($uriSegments) === count($routeSegments) && strtoupper($route['method'] === $requestMethod)) {
                $params = [];

                for ($i = 0; $i < count($uriSegments); $i++) {
                    // If the uri's do not match and there is no param.
                    if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                        $match = false;
                        break;
                    }

                    // Check for the param and add to $params array.
                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }

                if ($match) {

                    foreach($route['middleware'] as $middleware) {
                        (new Authorize())->handle($middleware);
                    }

                    // Extract controller and controller method
                    $controller       = 'App\\Controllers\\' . $route['controller'];
                    $controllerMethod =  $route['controllerMethod'];

                    // Instantiate the controller and call the method
                    $controllerInstance = new $controller();
                    $controllerInstance->$controllerMethod($params);
                    return;
                }
            }
        }

        ErrorController::notFound();
    }
}
