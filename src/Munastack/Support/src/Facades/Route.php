<?php

namespace Munastack\Support\Facades;

class Route extends Facade
{
	public static function getFacadeAccessor(): string
	{
		return 'router';
	}
}
