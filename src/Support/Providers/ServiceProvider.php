<?php

namespace Muna\Framework\Support\Providers;

use Muna\Framework\Foundation\Application;
use Muna\Framework\Support\Container;

abstract class ServiceProvider
{
    protected Application $app;
	//protected Container $container;

    public function __construct(Application $app)
	{
        $this->app = $app;
		//$this->container = $this->app::$container;
    }

    abstract public function boot();
}
