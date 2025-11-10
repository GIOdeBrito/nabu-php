<?php

namespace NabuPHP\Routing;

use NabuPHP\Http\Request;
use NabuPHP\Helpers\ControllerHelpers;
use NabuPHP\Helpers\RouteHelpers;
use NabuPHP\Helpers\StringHelpers;

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

		if($settings['isView'] === true)
		{
			$this->viewResponse($settings['view-path'], $settings['view-vars']);
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

	private function viewResponse ($path, $vars = [])
	{
		extract($vars);

		ob_start();
		include $path;
		$body = ob_get_clean();

		// Renders with layout
		if(isset($this->configs->properties->{'layouts-folder'}))
		{
			$layout_folder = StringHelpers::constantFinderReplacer($this->configs->properties->{'layouts-folder'}, $this->constants);

			ob_start();
			include $layout_folder.'/_layout.php';
			$body = ob_get_clean();
		}

		$this->sendResponse(200, $body, 'text/html');
	}
}

?>