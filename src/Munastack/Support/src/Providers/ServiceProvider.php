<?php

namespace Munastack\Support\Providers;

use Munastack\Foundation\Application;
use Munastack\Support\Container;

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
