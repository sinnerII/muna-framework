<?php

use Muna\Framework\Foundation\Application;
use Muna\Framework\Http\Request;

if (!function_exists('app')) {
    function app(): Application
	{
        return Application::$instance;
    }
}

if (!function_exists('request')) {
    function request(): Request
	{
        return Application::$request;
    }
}

if (!function_exists('dump')) {
    function dump(mixed $var): void
	{
		
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
		$result = '<div style="background-color: black; color: lightgreen;padding:10px;border-radius:5px;font-weight: 700;margin-bottom: 10px;font-family: Ubuntu;">';
		$result .= '<h6 style="margin:0; padding:0; padding-bottom:5px;color: orange;">'.$trace[0]['file'].': '.$trace[0]['line'].'</h6>';
		$result .= '<pre style="margin: 5px 0px 5px 0px">';
		$result .= var_export($var, true);	
		$result .= '</pre>';
		$result .= "</div>";
		echo $result;
    }
}


