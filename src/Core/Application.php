<?php

namespace NabuPHP\Core;

use NabuPHP\Core\Configuration;
use NabuPHP\Routing\Router;
use NabuPHP\Helpers\StringHelpers;
use NabuPHP\Helpers\RouteHelpers;

define('__ABSOLUTEPATH__', __DIR__);

final class Application
{
	private $configs;
	private $routeFiles = [];

	public function __construct ($configPath)
	{
		$this->configs = new Configuration($configPath);

		$routesPath = $this->configs->getProperty('routes-folder');
		$constants = $this->configs->getConstants();

		// Get all the JSON route files
		$this->routeFiles = RouteHelpers::excavateJSONRouteFiles($routesPath, $constants);
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

	private function excavateJSONRouteFiles ()
	{
		if(!isset($this->configs->properties->{'routes-folder'}))
		{
			throw new \Exception("Routes property were not set");
		}

		$path = $this->configs->properties->{'routes-folder'};

		// Replaces constants in property strings
		if(isset($this->configs->constants))
		{
			$path = StringHelpers::constantFinderReplacer($path, $this->configs->getConstants());
		}

		// Get all JSON route files
		$jsonFiles = glob($path.'/*.json');

		$this->routeFiles = $jsonFiles;
	}
}

?>