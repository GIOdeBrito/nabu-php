<?php

namespace NabuPHP\Core;

use NabuPHP\Routing\Router;
use NabuPHP\Helpers\StringHelpers;

define('__ABSOLUTEPATH__', __DIR__);

final class Application
{
	private $configs;
	private $routeFiles = [];

	public function __construct ($configPath)
	{
		$configPathAbs = $configPath;

		if(!file_exists($configPathAbs))
		{
			$errMsg = "Config file: {$configPathAbs}, not found";
			error_log($errMsg);
			throw new \Exception($errMsg);
		}

		$this->configs = json_decode(file_get_contents($configPathAbs));

		// Get all the JSON route files
		$this->excavateJSONRouteFiles();
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
			$path = StringHelpers::constantFinderReplacer($path, $this->configs->constants);
		}

		// Get all JSON route files
		$jsonFiles = glob($path.'/*.json');

		$this->routeFiles = $jsonFiles;
	}
}

?>