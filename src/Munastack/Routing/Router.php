<?php

namespace Munastack\Routing;

use Closure;
use Munastack\Http\Request;
use Munastack\Http\Response;

class Router
{
	protected static ?Router $instance = null;
	protected Request $request;
	protected Response $response;
	protected RouteCollection $routes;
	

	private function __construct(Request $request, Response $response){

		$this->routes = RouteCollection::create();
        $this->request = $request;
        $this->response = $response;

        //dump(app()::getInstance()->container->get('response'));
	}

    public function get(string $uri, Closure|array $action): Route
    {
        return $this->addRoute(['GET'], $uri, $action);
    }

    public function post(string $uri, Closure|array $action): Route
    {
        return $this->addRoute(['POST'], $uri, $action);
    }

    public function put(string $uri, Closure|array $action): Route
    {
        return $this->addRoute(['PUT'], $uri, $action);
    }

    public function delete(string $uri, Closure|array $action): Route
    {
        return $this->addRoute(['DELETE'], $uri, $action);
    }

    public function match(array $methods, string $uri, Closure|array $action): Route
    {
        return $this->addRoute($methods, $uri, $action);
    }

    protected function addRoute(string|array $httpMethod, string $uri, Closure|array $action ): Route
    {
        $httpMethod = array_map(fn($n) => strtoupper($n),$httpMethod);
        $route = new Route($uri, $action);

        foreach($httpMethod as $method) {
			$this->routes->addRoute($method, $route);
        }

        return $route;

    }

	public static function create(Request $request, Response $response): Router
	{
		if(self::$instance === null) {
			self::$instance = new self($request, $response);
		} 
       
		return self::$instance;
	}

	protected function findRoute(): ?Route
	{
		foreach($this->routes->getRoutesByMethod($this->request->method) as $route) {
			if($this->matchRoute($route)) {
				return $route;
			}
		}

		return null;
	}

	protected function matchRoute(Route $route): bool
	{
		$ruri = $route->getUri();

        dump($ruri);
        //$uriWitchParams = $this->prepareParameters($route);

		//preg_match_all('#\{([a-zA-Z_]+)(\??))\}#ui',$ruri,$varsName);
		preg_match_all('#{(([a-zA-Z_]+)(\??))}#ui',$ruri,$varsName);
        

		foreach($varsName[2] as $key => $var) {
			if(array_key_exists($var, config('app.params'))) {
				$find = '/\b' . $var . '\b/';
                //dump("FIND: " . $find);
				$replace = '('. config('app.params')[$var]. ')';
				$ruri = preg_replace($find, $replace, $ruri);
			}   
		}   

		$ruri = preg_replace(['#}#','#{#'],['',''], $ruri);
		$ruri = preg_replace('#\?\/#','?/?', $ruri);



		//$originUri = rtrim(preg_replace('#/+#','/',$this->request->uri),'/');
		$originUri = $this->request->uri;
        $ruri = rtrim($ruri, '/');

        //dump($ruri);

        
		//if(preg_match('#^'. $ruri .'$#' , $originUri,$matches)) {
        $match = '#^' . $ruri . '$#';
        //dump("RURI: " . $ruri);
        //dump("MATCH: " . $match);
        //dump("ORIGIN: ". $originUri);
		if(preg_match($match , $originUri,$matches)) {

            //dump($varsName[2]);
			foreach($varsName[2] as $i => $key) {
				$route->setParam($key,$matches[$i + 1]);
				//$route->setParam($key,$matches[$i]);
			}
            //dump($route);
			return true;
		}

		return false;
	}

    protected function prepareParameters(Route $route): void
    {
        dump($route->getUri());
		preg_match_all('#\{(([a-zA-Z_]+)(\??))\}#ui',$route->getUri(),$varsName);
        dump($varsName);

        $ruri = $route->getUri();
		foreach($varsName[2] as $key => $var) {
			if(array_key_exists($var, config('app.params'))) {
				$find = '/\b' . $var . '\b/';
				$replace = '('. config('app.params')[$var]. ')';
				$ruri = preg_replace($find, $replace, $ruri);
			}   
		}   


		$ruri = preg_replace(['#}#','#{#'],['',''], $ruri);
		$ruri = preg_replace('#\?\/#','?/?', $ruri);

        //dump("RESULT:");
        //dump($ruri);

        if(preg_match('#^' .$ruri. '$#', $route->getUri(), $matches)){
            //dump($matches);
        }

        //dump($ruri);

    }


	public function dispatch()
	{
		$route = $this->findRoute();
		if($route) {
			$route->execute();
		}
		return null;
	}

	private function __clone() {}

	public function __call($method, $args) {

		dump("Execute method: $method");
		dump($args);

	}
}
