<?php
namespace App\Core;

/**
 * Simple Router / Application class
 */
class App
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function run(string $uri, string $method): void
    {
        $method = strtoupper($method);

        if (isset($this->routes[$method][$uri])) {
            $action = $this->routes[$method][$uri];
            $this->dispatch($action);
            return;
        }

        // 404
        http_response_code(404);
        echo '<h1>404 — Página não encontrada</h1>';
        echo '<p><a href="/login">Voltar ao login</a></p>';
    }

    private function dispatch(string $action): void
    {
        list($controllerName, $methodName) = explode('@', $action);
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Controller {$controllerName} não encontrado.";
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "Método {$methodName} não existe em {$controllerName}.";
            return;
        }

        $controller->$methodName();
    }
}
