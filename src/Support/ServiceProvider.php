<?php

namespace Muna\Framework\Support;

use Muna\Framework\Foundation\Application;

abstract class ServiceProvider {
    protected Application $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    abstract public function boot();
}
