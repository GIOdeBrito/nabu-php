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

	public function __construct ($routeFiles, $configs)
	{
		if(!is_array($routeFiles) || empty($routeFiles))
		{
			throw new \Exception("Routes are empty or are not an array");
		}

		$this->routeFiles = $routeFiles;
		$this->configs = $configs;
	}

	public function call ()
	{
		$req = new Request();
		$route = $this->routeLookup($req->getMethod(), $req->getPath());

		if(is_null($route))
		{
			$notfoundPath = $this->configs->getProperty('notfound-redirect');
			$notfoundRoute = $this->routeLookup('GET', $notfoundPath);

			if(!is_null($notfoundRoute))
			{
				header('Location: '.$notfoundPath);
				return;
			}

			throw new \Exception("No proper route was found");
		}

		$settings = RouteHelpers::getRouteSettings($route, $this->configs->getConstants());

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
		if(is_null($path))
		{
			return NULL;
		}

		foreach($this->routeFiles as $file):

			$obj = json_decode(file_get_contents($file));

			if(!isset($obj->method, $obj->path))
			{
				continue;
			}

			if(mb_strtoupper($obj->method) === mb_strtoupper($method) && $obj->path === $path)
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
			$layoutsFolder = $this->configs->getProperty('layouts-folder');
			$layout_folder = StringHelpers::constantFinderReplacer($layoutsFolder, $this->configs->getConstants());

			ob_start();
			include $layout_folder.'/_layout.php';
			$body = ob_get_clean();
		}

		$this->sendResponse(200, $body, 'text/html');
	}
}

?>