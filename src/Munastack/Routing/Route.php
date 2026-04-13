<?php

namespace Munastack\Routing;

use Closure;

class Route
{
	protected array $config = [];
    protected array $requestParams = [];

	public function __construct(
			protected string $uri,
			protected Closure|array $action)
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
        $resolveParams = $this->resolveParams($this->action, $this->requestParams);

		if($this->action instanceof Closure) {
            //call_user_func_array($this->action, $this->requestParams);
            call_user_func_array($this->action, $resolveParams);
        } elseif (is_array($this->action)) {
            [$controller, $method] = $this->action;
            $controllerInstance = new $controller();
            //$controllerInstance->$method(...$this->requestParams);
            $controllerInstance->$method(...$resolveParams);
        }
	}

    protected function resolveParams(callable|array $action, array $params): array
    {
        $reflection = is_array($action)
            ? new \ReflectionMethod($action[0], $action[1])
            : new \ReflectionFunction($action);

        $resolved = [];

        foreach ($reflection->getParameters() as $param) {
            $name  = $param->getName();
            $value = $params[$name] ?? null;

            if (($value === null || $value === '') && $param->isDefaultValueAvailable()) {
                $resolved[] = $param->getDefaultValue(); // берём дефолт из объявления
            } else {
                $resolved[] = $value;
            }
        }

        return $resolved;
    }
}
