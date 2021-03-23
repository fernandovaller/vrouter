<?php

namespace FVCode\VRouter;

class Router extends Dispatch
{
    /**
     * Router constructor.
     *
     * @param string $projectUrl
     * @param null|string $separator
     */
    public function __construct($projectUrl, $separator = ":")
    {
        parent::__construct($projectUrl, $separator);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function post($route, $handler, $name = null)
    {
        $this->addRoute("POST", $route, $handler, $name);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function get($route, $handler, $name = null)
    {
        $this->addRoute("GET", $route, $handler, $name);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function put($route, $handler, $name = null)
    {
        $this->addRoute("PUT", $route, $handler, $name);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function patch($route, $handler, $name = null)
    {
        $this->addRoute("PATCH", $route, $handler, $name);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function delete($route, $handler, $name = null)
    {
        $this->addRoute("DELETE", $route, $handler, $name);
    }


    /**
     * @param string $route
     * @param $handler Controller::method
     * @param string|null $name
     */
    public function resource($route, $handler, $name = null)
    {
        $this->addRoute("GET", $route, "{$handler}:index", "{$name}.index");

        $this->addRoute("GET", "{$route}/create", "{$handler}:create", "{$name}.create");
        $this->addRoute("POST", "{$route}/store", "{$handler}:store", "{$name}.store");

        $this->addRoute("GET", "{$route}/{id}/edit", "{$handler}:edit", "{$name}.edit");
        $this->addRoute("POST", "{$route}/update", "{$handler}:update", "{$name}.update");

        $this->addRoute("GET", "{$route}/{id}/destroy", "{$handler}:destroy", "{$name}.destroy");
    }

    /**
     * @param array $methods
     * @param string $route
     * @param $handler Controller::method
     * @param string|null $name
     */
    public function any($methods, $route, $handler, $name = null)
    {
        if (!is_array($methods)) {
            throw new \Exception("Error method invalid!", 1);
        }

        foreach ($methods as $method) {
            $this->addRoute("{$method}", $route, $handler, $name);
        }
    }
}
