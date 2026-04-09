<?php
namespace Muna\Framework\Foundation;

use Muna\Framework\Routing\Route;
use Muna\Framework\Routing\Router;
use Muna\Framework\Routing\RouteCollection;
use Muna\Framework\Http\Request;
use Muna\Framework\Http\Response;
use Muna\Framework\Config\Repository;
use Muna\Framework\Support\Container;

class Application 
{
	private static Application $instance;

	public protected(set) Repository $config;
	public protected(set) RouteCollection $routes;
	public protected(set) Request $request;
	public protected(set) Response $response;
	public protected(set) Container $container;
	public protected(set) float $startTime;

	public string $timeDuration {
		get => number_format(microtime(true) - $this->startTime,3, '.', '') . 'sec';
	}

    public function __construct()
	{
		self::$instance = $this;
		$this->container = new Container();
		$this->request = new Request();
		$this->response = new Response();
		$this->startTime = microtime(true);
		$this->routes = RouteCollection::create();
    }

	public static function getInstance(): Application 
	{
		return self::$instance;
	}
	
	public function init()
	{
		$this->loadConfig();
		$this->loadProviders();
		$this->container->get('router')->dispatch();
		dump($this->timeDuration);
	}

	private function loadConfig()
	{
		$this->config = new Repository($this->loadConfigFiles());
	}

	private function loadProviders()
	{
		foreach($this->config->get('app.providers') as $provider) {
			$providerInstance = new $provider(self::$instance);
			$this->providers[] = $providerInstance;
			$providerInstance->boot();
		}
	}

	protected function loadConfigFiles()
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
