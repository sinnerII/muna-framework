<?php

namespace Muna\Framework\Support\Facades;

abstract class Facade
{
	protected static $app;

	public static function setFacadeApplication($app)
	{
		static::$app = $app;
	}

	public static function getFacadeApplication()
	{
		return static::$app;
	}

	public abstract static function getFacadeAccessor(): string;

	public static function __callStatic(string $method, array $args)
	{
		$instance = static::$app->container->get(static::getFacadeAccessor());	

		if(!$instance) {
			throw new \Exception("Service not found in container");
		}

		return $instance->$method(...$args);
	}	
}
