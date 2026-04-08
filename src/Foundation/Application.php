<?php
namespace Muna\Framework\Foundation;

use Muna\Framework\Routing\Route;
use Muna\Framework\Routing\Router;
use Muna\Framework\Routing\RouteCollection;
use Muna\Framework\Http\Request;
use Muna\Framework\Config\Repository;

class Application 
{

	public static Repository  $config;
	public RouteCollection $routes;

	public protected(set)float $startTime;

	protected array $providers = [];
	protected static Request $request;
	protected static Router $router;

	private static Application $instance;


    public function __construct()
	{
		self::$instance = $this;
		$this->startTime = microtime(true);
		$this->routes = RouteCollection::create();
    }

	public function init()
	{
		self::$config = new Repository($this->loadConfig());

		foreach(self::$config->get('app.providers') as $provider) {
			$providerInstance = new $provider(self::$instance);
			$this->providers[] = $providerInstance;
			$providerInstance->boot();
		}

		self::$request = new Request();
		self::$router = Router::create(self::$request);
		self::$router->dispatch();
			
		dump($this->timeDuration);
	}

	public string $timeDuration {
		get => number_format(microtime(true) - $this->startTime,3, '.', '') . 'sec';
	}

	public static function request(): Request {
		return self::$request;
	}

	public static function router(): Router {
		return self::$router;
	}

	public function routes(): RouteCollection
	{
		return $this->routes;
	}

	public static function getInstance(): Application 
	{
		return self::$instance;
	}

	protected function loadConfig()
	{
		$result = [];
		$configFiles = glob($_SERVER['DOCUMENT_ROOT'] . '/config/*.php');
		
		foreach($configFiles as $file) {
			$key = preg_replace('/\.php/','',basename($file));
			$result[$key] = require $file;
		}

		return $result;		
	}
}
