<?php

namespace Muna\Framework\Routing;

use Muna\Framework\Foundation\Application;
use Muna\Framework\Http\Request;
use Muna\Framework\Routing\Route;
use Muna\Framework\Routing\RouteCollection;

class Router
{
	protected static ?Router $instance = null;
	protected Request $request;

	private function __construct(Request $req){
		$this->request = $req;
	}

	public static function create(Request $req): Router
	{
		if(self::$instance === null) {
			self::$instance = new self($req);
		} 
		
		return self::$instance;
	}

	protected function findRoute(): ?Route
	{
		foreach(Application::getInstance()->routes()->getRoutesByMethod($this->request->method) as $route) {
			if($this->matchRoute($route)) {
				return $route;
			}
		}

		return null;
	}

	protected function matchRoute(Route $route): bool
	{
		$ruri = $route->getUri();

		// 1 . Находим переменные части
		preg_match_all('#\{(([a-zA-Z_]+)(\??))\}#ui',$ruri,$varsName);

		foreach($varsName[2] as $key => $var) {
			if(array_key_exists($var, config('app.params'))) {
				//$find = '#\{'. $var . '\}#';
				$find = '/\b' . $var . '\b/';
				$replace = '('. config('app.params')[$var]. ')';
				$ruri = preg_replace($find, $replace, $ruri);
			}   
		}   

		$ruri = preg_replace(['#}#','#{#'],['',''], $ruri);
		$ruri = preg_replace('#\?\/#','?/?', $ruri);

		$originUri = rtrim(preg_replace('#/+#','/',$this->request->uri),'/');
		//$originUri = rtrim($originUri,'/');

		//if(preg_match('#^'. $ruri . '$#', $originUri,$matches)) {
		if(preg_match('#^'. $ruri .'$#' , $originUri,$matches)) {

			foreach($varsName[2] as $i => $key) {
				$route->setParam($key,$matches[$i + 1]);
			}

			return true;
		}

		return false;
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
}
