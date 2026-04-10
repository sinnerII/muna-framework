<?php

namespace Muna\Framework\Support\Providers;

use Muna\Framework\Foundation\Application;
use Muna\Framework\Support\Container;

abstract class ServiceProvider
{
    protected Application $app;

    public function __construct(Application $app)
	{
        $this->app = $app;
    }

	abstract public function register(): void;
    abstract public function boot(): void;
}
