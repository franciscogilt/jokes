<?php

namespace Ninja;

class EntryPoint
{
    private $route;
    private $method;
    private $routes;

    public function __construct($route, $method, \Ninja\Routes $routes)
    {
        $this->route = $route;
        $this->method = $method;
        $this->routes = $routes;
        $this->checkUrl();
    }

    private function checkUrl()
    {
        if ($this->route !== strtolower($this->route)) {
            http_response_code(301);
            header('location: index.php?route=' . strtolower($this->route));
        }
    }

    private function loadTemplate($templateFileName, $variables = [])
    {
        extract($variables);
        ob_start();
        include __DIR__ . '/../../templates/' . $templateFileName;
        return ob_get_clean();
    }

    public function run()
    {
        $routes = $this->routes->getRoutes();
        $authentication = $this->routes->getAuthentication();

        if (
            isset($routes[$this->route]['login']) &&
            $routes[$this->route]['login'] == 'true' &&
            !$authentication->isLoggedIn()
        ) {
            header('location: index.php?route=login/error');
        } elseif (
            isset($routes[$this->route]['permissions']) &&
            !$this->routes->checkPermission($routes[$this->route]['permissions'])
        ) {
            header('location: index.php?route=login/error');
        } else {
            $controller = $routes[$this->route][$this->method]['controller'];
            $action = $routes[$this->route][$this->method]['action'];
            $page = $controller->$action();

            $title = $page['title'];
            if (isset($page['variables'])) {
                $output = $this->loadTemplate($page['template'], $page['variables']);
            } else {
                $output = $this->loadTemplate($page['template']);
            }
            // include __DIR__ . '/../../templates/layout.html.php';
            echo $this->loadTemplate('layout.html.php', [
                'loggedIn' => $authentication->isLoggedIn(),
                'output' => $output,
                'title' => $title
            ]);
        }
    }
}
