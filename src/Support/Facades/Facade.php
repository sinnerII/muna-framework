<?php

namespace Muna\Framework\Support\Facades;

abstract class Facade
{
	protected static $container;

	public static function setContainer($container): void
	{
		static::$container = $container;
	}

	public abstract static function getFacadeAccessor(): string;

	public static function __callStatic(string $method, array $args)
	{
		$instance = static::$container->get(static::getFacadeAccessor());	

		if(!$instance) {
			throw new \Exception("Service not found in container");
		}

		return $instance->$method(...$args);
	}	
}
