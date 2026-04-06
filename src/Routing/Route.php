<?php

namespace Muna\Framework\Routing;

class Route {

	protected static array 	$routeList = [];
	protected static array  $http;
    protected static array  $params = [
        'id' => '\d+',
        'uid' => '\d+',
        'slug' => '[a-z]+'
    ];

	protected array $config = [];
    protected array $requestParams = [];


	public function __construct(
			protected string $method,
			protected string $uri,
			protected \Closure|array $action) {}


	public static function get(string $uri, \Closure|array $action):static {
		return self::addRoute('GET', $uri, $action);
	}

	public static function post(string $uri, \Closure|array $action):static {
		return self::addRoute('POST', $uri, $action);
	}

	public static function put(string $uri, \Closure|array $action):static {
		return self::addRoute('PUT', $uri, $action);
	}

	public static function delete(string $uri, \Closure|array $action):static {
		return self::addRoute('DELETE', $uri, $action);
	}

	protected static function addRoute(string $method, string $uri, \Closure|array $action ):static {
		$route = new static($method, $uri, $action);	
		self::$routeList[] = $route;
		return $route;
		
	}

	public function name(string $name): static {
		$this->config['name'] = $name;
		return $this;
	}


	protected static function httpInit() {
		self::$http['method'] = $_SERVER['REQUEST_METHOD'];	
		self::$http['uri']    = $_SERVER['REQUEST_URI'];
		self::$http['query']  = $_SERVER['QUERY_STRING'];
	}

	protected static function getAction():?Route {
	
		$routes = array_filter(self::$routeList, fn($n) => $n->method === self::$http['method']);
		return self::selectActiveRoute($routes);
	}

	protected static function selectActiveRoute(array $routes):?Route {
		
        $ruri = '';

		foreach($routes as $route) {

            $ruri = $route->uri;

            // Алгоритм работы
            // 1. Выявление переменной части в маршруте
            // 2. Определение переменных из маршрута
            // 3. Формирование списка переменных

            // Получение всех переменных из uri маршрута
            preg_match_all('#\{(([a-zA-Z_]+)(\??))\}#ui',$ruri,$varsName);

                foreach($varsName[2] as $key => $var) {

                    if(array_key_exists($var, self::$params)) {
                        //$find = '#\{'. $var . '\}#';
                        $find = '/\b' . $var . '\b/';
                        $replace = '('. self::$params[$var]. ')';
                        $ruri = preg_replace($find, $replace, $ruri);

                    }
                }

                $ruri = preg_replace(['#}#','#{#'],['',''], $ruri);
                $ruri = preg_replace('#\?\/#','?/?', $ruri);


                $originUri = preg_replace('#/+#','/',self::$http['uri']);

                if(preg_match('#^'. $ruri . '$#', $originUri,$matches)) {


                    foreach($varsName[2] as $i => $key) {
                        $route->requestParams[$key] = $matches[$i + 1];
                    }
                    
                    return $route;
                }
		}

		return null;
	}
	
	public static function dispatch():void {
		self::httpInit();

        $closure = self::getAction();

		echo "<pre>";	
		print_r($closure);
		echo "</pre>";

        if($closure->action instanceof \Closure) {
            echo "CLOSURE";
            call_user_func_array($closure->action, $closure->requestParams);
        } elseif (is_array($closure->action)) {
            echo "CONTROLLER";
            [$controller, $method] = $closure->action;
            $controllerInstance = new $controller();
            $controllerInstance->$method(...$closure->requestParams);
        }
	}
}
