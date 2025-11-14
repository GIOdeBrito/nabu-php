<?php

namespace NabuPHP\Core;

define('__ABSOLUTEPATH__', __DIR__.'/..');

// Import namespace'd helper files
require __ABSOLUTEPATH__.'/Helpers/Objects.php';
require __ABSOLUTEPATH__.'/Helpers/Strings.php';
require __ABSOLUTEPATH__.'/Helpers/Routes.php';
require __ABSOLUTEPATH__.'/Helpers/Controllers.php';
require __ABSOLUTEPATH__.'/Helpers/Rendering.php';
require __ABSOLUTEPATH__.'/Helpers/Directories.php';

use NabuPHP\Core\Configuration;
use NabuPHP\Routing\Router;

use function NabuPHP\Helpers\Strings\constant_finder_replacer;
use function NabuPHP\Helpers\Routes\excavate_JSON_route_files;
use function NabuPHP\Helpers\Directories\get_directories_recursive;

final class Application
{
	private ?object $configs = NULL;
	private array $routeFiles = [];

	public function __construct (string $configPath)
	{
		//var_dump(get_directories_recursive(__ABSOLUTEPATH__));
		//die();

		if(PHP_OS !== 'Linux')
		{
			http_response_code(500);
			echo "Stop right there. Use a decent OS.";
			die();
		}

		$this->configs = new Configuration($configPath);

		$routesPath = $this->configs->getProperty('routes-folder');
		$constants = $this->configs->getConstants();

		// Get all the JSON route files
		$this->routeFiles = excavate_JSON_route_files($routesPath, $constants);
	}

	public function run ()
	{
		$router = new Router($this->routeFiles, $this->configs);
		$router->call();
	}

	private function dumpConfigs ()
	{
		var_dump($this->configs);
		die();
	}
}

?>