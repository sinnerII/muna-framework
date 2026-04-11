<?php

namespace Munastack\Routing;

class RouteCollection {
	
	private array $routes = [];
	private static ?RouteCollection $instance = null;

	private function __construct()
	{
	
	}

	public static function create()
	{

		if(self::$instance === null){
			self::$instance = new self();
		}

		return self::$instance;
		
	}

	public function addRoute(string $httpMethod, Route $route)
	{
		$this->routes[$httpMethod][] = $route;
	}

	public function getAllRoutes()
	{
		return $this->routes;
	}

	public function getRoutesByMethod(string $httpMethod)
	{
		return $this->routes[strtoupper($httpMethod)];
	}

	public function findRoute(): Route
	{

	}
}
