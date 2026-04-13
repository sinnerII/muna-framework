<?php

namespace Munastack\Routing;

use Closure;
use Muna\Framework\Foundation\Application;

class Route
{
	protected array $config = [];
    protected array $requestParams = [];

	public function __construct(
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

	public function name(string $name): Route
	{
		$this->config['name'] = $name;
		return $this;
	}

	public function execute():void
	{
		if($this->action instanceof Closure) {
            call_user_func_array($this->action, $this->requestParams);
        } elseif (is_array($this->action)) {
            [$controller, $method] = $this->action;
            $controllerInstance = new $controller();
            $controllerInstance->$method(...$this->requestParams);
        }
	}
}
