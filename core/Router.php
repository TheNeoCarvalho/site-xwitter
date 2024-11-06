<?php

namespace Core;

class Router
{
    public function run()
    {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $url = trim($url, '/');
        $segments = explode('/', $url);

        $controllerName = isset($segments[0]) && $segments[0] !== '' 
            ? 'App\Controllers\\' . ucfirst($segments[0]) . 'Controller' 
            : 'App\Controllers\HomeController';

        $actionName = isset($segments[1]) && $segments[1] !== '' 
            ? $segments[1] 
            : 'index';

        if (class_exists($controllerName)) {
            $controller = new $controllerName();

            if (method_exists($controller, $actionName)) {
                
                $params = array_slice($segments, 2);  
                
                call_user_func_array([$controller, $actionName], $params);
            } else {
                echo "Método '$actionName' não encontrado no controller '$controllerName'!";
            }
        } else {
            echo "Controller '$controllerName' não encontrado!";
        }
    }
}
