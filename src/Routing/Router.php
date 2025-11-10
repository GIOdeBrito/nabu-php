<?php

namespace NabuPHP\Routing;

use NabuPHP\Http\Request;
use NabuPHP\Helpers\ControllerHelpers;
use NabuPHP\Helpers\RouteHelpers;

class Router
{
	private $routeFiles = [];
	private $parsedRoutes = [];

	private $configs;
	private $constants = NULL;

	public function __construct ($routeFiles, $configs)
	{
		if(!is_array($routeFiles) || empty($routeFiles))
		{
			throw new \Exception("Routes are empty or are not an array");
		}

		$this->routeFiles = $routeFiles;

		$this->configs = $configs;

		if(isset($this->configs->constants))
		{
			$this->constants = $this->configs->constants;
		}
	}

	public function call ()
	{
		$req = new Request();
		$route = $this->routeLookup($req->getMethod(), $req->getPath());

		if(is_null($route))
		{
			throw new \Exception("No proper route was found");
		}

		$settings = RouteHelpers::getRouteSettings($route, $this->constants);

		// Controller instructions
		if($settings['isController'] === true)
		{
			$this->controllerResponse($settings['controller'], $settings['controllerCallMethod']);
			return;
		}

		$this->sendResponse(200, $settings['content'], $settings['content-type']);
	}

	private function sendResponse ($code, $content, $contentType)
	{
		header("Content-Type: ".$contentType);
		http_response_code($code);

		// Encodes to JSON if not a string
		if($contentType === 'application/json' && gettype($content) !== 'string')
		{
			$content = json_encode($content);
		}

		echo $content;
	}

	private function routeLookup ($method, $path)
	{
		foreach($this->routeFiles as $file):

			$obj = json_decode(file_get_contents($file));

			if(!isset($obj->method, $obj->path))
			{
				continue;
			}

			if($obj->method === $method && $obj->path === $path)
			{
				return $obj;
			}

		endforeach;

		return NULL;
	}

	private function controllerResponse ($controllerName, $callMethod)
	{
		$code = 200;
		$controller = ControllerHelpers::controllerInstantiator($controllerName);

		$controlerMethodResponse = $controller->{$callMethod}();

		// If the key 'status' was set
		if(isset($controlerMethodResponse['status']))
		{
			$code = intval($controlerMethodResponse['status']);
		}

		$content = $controlerMethodResponse['data'];
		$contentType = $controlerMethodResponse['content-type'];

		$this->sendResponse($code, $content, $contentType);
	}
}

?>