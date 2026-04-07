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

        foreach(explode('.', $key) as $segment) {
            if(!isset([$segment]))
                return $default;

            $array = $array[$segment];
        }
        return $array;
    } 
}
