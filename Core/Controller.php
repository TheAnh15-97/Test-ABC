<?php

namespace Core;

use Exception;
use JetBrains\PhpStorm\NoReturn;

abstract class Controller
{
    protected array $routeParams = [];
    protected Request $request;

    public function __construct($routeParams)
    {
        $this->routeParams = $routeParams;
        $this->request = new Request();
    }

    #[NoReturn] function redirect($url, $statusCode = 302): void
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }

    /**
     * @throws Exception
     */
    public function __call($name, $args)
    {
        $method = $name;

        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else {
            throw new Exception("Method $method not found in controller " . get_class($this));
        }
    }
}
