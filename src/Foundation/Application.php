<?php
namespace Muna\Framework\Foundation;


use Muna\Framework\Routing\Route;
use Muna\Framework\Config\Repository;

class Application {

    public static $instance;
    protected array $providers = [];
    public Repository $config;

    public function __construct() {
       self::$instance = $this;
       //$items = $this->loadConfigInitializers(); 
       //$items = [];
       //$this->config = new Repository($items);
    }

    //public static array $config = [];	

	public function init() {

        $providerClasses = \App\Providers\RouteServiceProvider::class;
        $this->providers[] = new $providerClasses($this);
        $this->providers[0]->boot();
		Route::dispatch();

	}

    protected function loadRoutes() {

        $basePath = dirname($_SERVER['SCRIPT_FILENAME'],2);
        $webRoutes = $basePath . '/routes/web.php';

        if(file_exists($webRoutes)) {
            require_once $webRoutes;
        }

    }

	public function render(mixed $output) {
		//file_put_contents("php://output", $output);		
	}

}
