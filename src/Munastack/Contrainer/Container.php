<?php
namespace Munastack\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
	private array $services = [];
    private array $bindings = [];
    private array $singletons = [];
    private array $instances = [];

	
	public function set($key, $obj)
	{
		$this->services[$key] = $obj;
        return $obj;
	}

	public function get($key)
	{
		return $this->services[$key];
	}

    public function has(string $id): bool
    {
        return true;
    }
}
