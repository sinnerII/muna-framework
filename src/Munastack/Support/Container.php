<?php

namespace Munastack\Support;

class Container
{
	private array $services = [];
	
	public function set($key, $obj)
	{
		$this->services[$key] = $obj;
        return $obj;
	}

	public function get($key)
	{
		return $this->services[$key];
	}
}
