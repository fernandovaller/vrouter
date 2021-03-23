# Sistema de rotas simples para PHP 5.6

Esse projeto é baseado em um fork do projeto [https://github.com/robsonvleite/router] mas modificado para funcionar na versão do PHP 5.6

## Installation

Router is available via Composer:

```bash
composer require fernandovaller/vrouter
```

#### .htaccess in apache

```apacheconfig
RewriteEngine On
#Options All -Indexes

## ROUTER WWW Redirect.
#RewriteCond %{HTTP_HOST} !^www\. [NC]
#RewriteRule ^ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

## ROUTER HTTPS Redirect
#RewriteCond %{HTTP:X-Forwarded-Proto} !https
#RewriteCond %{HTTPS} off
#RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# ROUTER URL Rewrite
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=/$1 [L,QSA]
```

##### Routes

```php
<?php
require __DIR__ . "/../vendor/autoload.php";

use FVCode\VRouter\Router;

$router = new Router("https://www.youdomain.com");

/**
 * routes
 * The controller must be in the namespace Test\Controller
 * this produces routes for route, route/$id, route/{$id}/profile, etc.
 */
$router->SetNamespace("Test");

$router->get("/route", "Controller:method");
$router->post("/route/{id}", "Controller:method");
$router->put("/route/{id}/profile", "Controller:method");
$router->patch("/route/{id}/profile/{photo}", "Controller:method");
$router->delete("/route/{id}", "Controller:method");

// CRUD - [index, create, store, edit, update, destroy]
$router->resource("/usuarios", "UsuarioController", 'usuarios');

$router->any(['GET','POST']", /usuarios", "UsuarioController", 'usuarios');

/**
 * group by routes and namespace
 * this produces routes for /admin/route and /admin/route/$id
 * The controller must be in the namespace Dash\Controller
 */
$router->group("admin")->namespace("Dash");
$router->get("/route", "Controller:method");
$router->post("/route/{id}", "Controller:method");

/**
 * Group Error
 * This monitors all Router errors. Are they: 400 Bad Request, 404 Not Found, 405 Method Not Allowed and 501 Not Implemented
 */
$router->group("error")->namespace("Test");
$router->get("/{errcode}", "Coffee:notFound");

/**
 * This method executes the routes
 */
$router->dispatch();

/*
 * Redirect all errors
 */
if ($router->error()) {
    $router->redirect("/error/{$router->error()}");
}
```

##### Named

```php
<?php
require __DIR__ . "/../vendor/autoload.php";

use FVCode\VRouter\Router;

$router = new Router("https://www.youdomain.com");

/**
 * routes
 * The controller must be in the namespace Test\Controller
 */
$router->namespace("Test")->group("name");

$router->get("/", "Name:home", "name.home");
$router->get("/hello", "Name:hello", "name.hello");
$router->get("/redirect", "Name:redirect", "name.redirect");

/**
 * This method executes the routes
 */
$router->dispatch();

/*
 * Redirect all errors
 */
if ($router->error()) {
    $router->redirect("name.hello");
}
```

###### Named Controller Exemple

```php
class Name
{
    public function __construct($router)
    {
        $this->router = $router;
    }

    public function home()
    {
        echo "<h1>Home</h1>";
        echo "<p>", $this->router->route("name.home"), "</p>";
        echo "<p>", $this->router->route("name.hello"), "</p>";
        echo "<p>", $this->router->route("name.redirect"), "</p>";
    }

    public function redirect()
    {
        $this->router->redirect("name.hello");
    }
}
```

###### Named Params
````php
//route
$router->get("/params/{category}/page/{page}", "Name:params", "name.params");

//$this->route = return URL
//$this->redirect = redirect URL

$this->router->route("name.params", [
    "category" => 22,
    "page" => 2
]);

//result
https://www.{}/name/params/22/page/2

$this->router->route("name.params", [
    "category" => 22,
    "page" => 2,
    "argument1" => "most filter",
    "argument2" => "most search"
]);

//result
https://www.{}/name/params/22/page/2?argument1=most+filter&argument2=most+search
````
