<?php

namespace Munastack\Http;

class Request
{
	public string $uri
	{
		get => explode("?", $_SERVER['REQUEST_URI'])[0] ?? '/';
	}

	public ?string $query
	{
		get => explode("?", $_SERVER['REQUEST_URI'])[1] ?? null;
	}

	public string $method
	{
		get => strtoupper($_SERVER['REQUEST_METHOD']);
	}

	public string $ip
	{
		get => $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'];
	}

	public function __construct() {}

	public function isMethod(string $method): bool
	{
		return strtoupper($method) === $this->method;
	}

	public function input(string $key): string|null
	{
		if($this->isMethod('get') || $this->isMethod('post')){
			$params = [...$_GET, ...$_POST];
			if(array_key_exists($key, $params))	{
				return $params[$key];
			}
		}
		return null;
	}

	public function params(): array|null
	{
		if($this->isMethod('get') || $this->isMethod('post')){
			return [...$_GET, ...$_POST];	
		}
		return null;
	}
}
