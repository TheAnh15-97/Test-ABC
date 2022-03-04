<?php

namespace Core;

use Exception;

class Router
{

    protected array $routes = [];

    protected array $params = [];

    public function add(string $route, array $params = [])
    {
        if (isset($_POST['_method']) && $_POST['_method'] != $params['method']) {
            return;
        }
        if (isset($params['method']) && !isset($_POST['_method']) && $_SERVER['REQUEST_METHOD'] !== $params['method']) {
            return;
        }

        $route = preg_replace('/\//', '\\/', $route);

        $route = preg_replace('/{([a-z]+)}/', '(?P<\1>[a-z-]+)', $route);

        $route = preg_replace('/{([a-z]+):([^}]+)}/', '(?P<\1>\2)', $route);

        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    public function match($url): bool
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controllerObject = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (preg_match('/action$/i', $action) == 0) {
                    $controllerObject->$action();
                } else {
                    throw new Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
                }
            } else {
                throw new Exception("Controller class $controller not found");
            }
        } else {
            throw new Exception('No route matched.', 404);
        }
    }

    protected function convertToStudlyCaps($string): array|string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    protected function convertToCamelCase($string): string
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    protected function removeQueryStringVariables(string $url): string
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (!str_contains($parts[0], '=')) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    protected function getNamespace(): string
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }
}
