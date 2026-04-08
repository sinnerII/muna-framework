<?php

namespace Muna\Framework\Routing;
use Muna\Framework\Foundation\Application;

class Route
{

	protected array $config = [];
    protected array $requestParams = [];

	public function __construct(
			protected string $method,
			protected string $uri,
			protected \Closure|array $action)
	{
	
	}

	public function getMethod()
	{
		return $this->method;	
	}

	public function getUri()
	{
		return $this->uri;
	}

	public function getRouteAction()
	{
		return $this->action;
	}

	public function setParam(string $key, mixed $value)
	{
		$this->requestParams[$key] = $value;
	}

	public static function get(string $uri, \Closure|array $action):static
	{
		return self::addRoute('GET', $uri, $action);
	}

	public static function post(string $uri, \Closure|array $action):static
	{
		return self::addRoute('POST', $uri, $action);
	}

	public static function put(string $uri, \Closure|array $action):static
	{
		return self::addRoute('PUT', $uri, $action);
	}

	public static function delete(string $uri, \Closure|array $action):static
	{
		return self::addRoute('DELETE', $uri, $action);
	}

	// Предусмотреть match метод
	protected static function addRoute(string|array $method, string $uri, \Closure|array $action ):static
	{
		$route = new static($method, $uri, $action);	
		//self::$routeList[] = $route;
		Application::getInstance()->routes->addRoute($method,$route);
		return $route;
		
	}

	public function name(string $name): static
	{
		$this->config['name'] = $name;
		return $this;
	}

	public function execute():void
	{
		if($this->action instanceof \Closure) {
            call_user_func_array($this->action, $this->requestParams);
        } elseif (is_array($this->action)) {
            [$controller, $method] = $this->action;
            $controllerInstance = new $controller();
            $controllerInstance->$method(...$this->requestParams);
        }
	}

	public static function getRoutes(): array
	{
		return self::$routeList;
	}

	public static function getRoutesByMethod(string $method): array
	{
		return array_filter(self::$routeList, fn($n) => $n->method === $method);
	}
}
