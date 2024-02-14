<?php

class Router
{
    protected $routes = [];

    /**
     * Add a new route
     */
    public function registerRoute(string $method, string $uri, string $controller): void
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
        ];
    }

    /**
     * Add a GET route.
     */
    public function get(string $uri, string $controller): void
    {
        $this->registerRoute('GET', $uri, $controller);
    }

    /**
     * Add a POST route.
     */
    public function post(string $uri, string $controller): void
    {
        $this->registerRoute('POST', $uri, $controller);
    }

    /**
     * Add a PATCH route.
     */
    public function patch(string $uri, string $controller): void
    {
        $this->registerRoute('PATCH', $uri, $controller);
    }

    /**
     * Add a PUT route.
     */
    public function put(string $uri, string $controller): void
    {
        $this->registerRoute('PUT', $uri, $controller);
    }

    /**
     * Add a DELETE route.
     */
    public function delete(string $uri, string $controller): void
    {
        $this->registerRoute('DELETE', $uri, $controller);
    }

    /**
     * Load error page
     */
    public function error(int $httpCode = 404): void
    {
        http_response_code($httpCode);
        loadView("errors/{$httpCode}");
        exit;
    }
    /**
     * Route the request.
     */
    public function route(string $uri, string $method): void
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                require basePath('App/' . $route['controller']);
                return;
            }
        }

        $this->error();
    }
}
