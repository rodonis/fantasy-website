<?php
declare(strict_types=1);

class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void  { $this->add('GET',  $path, $handler); }
    public function post(string $path, array $handler): void { $this->add('POST', $path, $handler); }

    private function add(string $method, string $path, array $handler): void {
        $pattern = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $path);
        $this->routes[] = compact('method', 'path', 'pattern', 'handler');
    }

    public function dispatch(string $uri, string $method): void {
        $path = strtok(parse_url($uri, PHP_URL_PATH), '?');
        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) continue;
            if (!preg_match('#^' . $route['pattern'] . '$#', $path, $m)) continue;
            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            [$class, $action] = $route['handler'];
            (new $class())->$action($params);
            return;
        }
        http_response_code(404);
        require __DIR__ . '/views/404.php';
    }
}
