<?php

namespace FVCode\VRouter;

/**
 * Class Dispatch
 *
 * @author Fernando Valler <https://github.com/fernandovaller>
 * @package FVCode\Router
 */
abstract class Dispatch
{
    use RouterTrait;

    /** @var null|array */
    protected $route;

    /** @var bool|string */
    protected $projectUrl;

    /** @var string */
    protected $separator;

    /** @var null|string */
    protected $namespace;

    /** @var null|string */
    protected $group;

    /** @var null|array */
    protected $data;

    /** @var int */
    protected $error;

    /** @const int Bad Request */
    const BAD_REQUEST = 400;

    /** @const int Not Found */
    const NOT_FOUND = 404;

    /** @const int Method Not Allowed */
    const METHOD_NOT_ALLOWED = 405;

    /** @const int Not Implemented */
    const NOT_IMPLEMENTED = 501;

    /**
     * Dispatch constructor.
     *
     * @param string $projectUrl
     * @param null|string $separator
     */
    public function __construct($projectUrl, $separator = ":")
    {
        $this->projectUrl = (substr($projectUrl, "-1") == "/" ? substr($projectUrl, 0, -1) : $projectUrl);
        $this->patch = ($path = filter_input(INPUT_GET, "route", FILTER_DEFAULT)) ? $path : '/';
        $this->separator = ($separator ? $separator : ':');
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [$this->patch, $this->httpMethod, $this->routes];
    }

    /**
     * @param null|string $namespace
     * @return Dispatch
     */
    public function setNamespace($namespace)
    {
        $this->namespace = ($namespace ? ucwords($namespace) : null);
        return $this;
    }

    /**
     * @param null|string $group
     * @return Dispatch
     */
    public function group($group)
    {
        $this->group = ($group ? str_replace("/", "", $group) : null);
        return $this;
    }

    /**
     * @return null|array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @return null|int
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function dispatch()
    {
        if (empty($this->routes) || empty($this->routes[$this->httpMethod])) {
            $this->error = self::NOT_IMPLEMENTED;
            return false;
        }

        $this->route = null;
        foreach ($this->routes[$this->httpMethod] as $key => $route) {
            if (preg_match("~^" . $key . "$~", $this->patch, $found)) {
                $this->route = $route;
            }
        }

        return $this->execute();
    }

    /**
     * @return bool
     */
    private function execute()
    {
        if ($this->route) {
            if (is_callable($this->route['handler'])) {
                call_user_func($this->route['handler'], ($this->route['data'] ? $this->route['data'] : []));
                return true;
            }

            $controller = $this->route['handler'];
            $method = $this->route['action'];

            if (class_exists($controller)) {
                $newController = new $controller($this);
                if (method_exists($controller, $method)) {
                    $newController->$method(($this->route['data'] ? $this->route['data'] : []));
                    return true;
                }

                $this->error = self::METHOD_NOT_ALLOWED;
                return false;
            }

            $this->error = self::BAD_REQUEST;
            return false;
        }

        $this->error = self::NOT_FOUND;
        return false;
    }
}
