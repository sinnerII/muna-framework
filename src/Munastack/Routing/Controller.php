<?php

namespace Munastack\Routing;

abstract class Controller
{
    
    public function __call($method, $parameters)
    {
        throw new \Exception(sprintf(
            'Method %s::%s does not exist.', static::class, $method));
    }
}
