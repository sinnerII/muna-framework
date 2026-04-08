<?php

namespace Muna\Framework\Config;

class Repository
{

    protected array $items = [];

    public function __construct(array $items = [])
	{
        $this->items = $items;
    }

    public function get(string $key, $default = null)
	{
		$array = $this->items;
		foreach(explode('.',$key) as $segment) {
			$array = $this->arrayWalk($segment, $array);
			//$array = fn($segment, $array) => array_key_exists($segment,$array) && return $array[$key];
		}

       	return $array;
    }

	protected function arrayWalk($key, $array)
	{
		if(array_key_exists($key, $array)) {
			return $array[$key];
		}
	}
}
