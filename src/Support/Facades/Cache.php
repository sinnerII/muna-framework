<?php

namespace Muna\Framework\Support\Facades;

class Cache extends Facade
{
	public static function getFacadeAccessor(): string
	{
		return 'cache';
	}
}
