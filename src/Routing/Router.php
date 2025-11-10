<?php

namespace NabuPHP\Routing;

use NabuPHP\Http\Request;

class Router
{
	private $routeFiles = [];
	private $parsedRoutes = [];

	public function __construct ($routeFiles)
	{
		if(!is_array($routeFiles) || empty($routeFiles))
		{
			throw new \Exception("Routes are empty or are not an array");
		}

		$this->routeFiles = $routeFiles;
	}

	public function call ()
	{
		$req = new Request();
		$route = $this->routeLookup($req->getMethod(), $req->getPath());

		if(is_null($route))
		{
			throw new \Exception("No proper route was found");
		}

		$definitions = $this->getRouteDefinitions($route);
		$content = $definitions['content'];
		$code = 200;

		// TODO: Create a function for this logic later (separation of concerns)

		// Controller instructions
		if($definitions['isController'] === true)
		{
			$callMethod = $content[1];
			$controller = $this->controllerInstantiator($content[0]);

			$controlerMethodResponse = $controller->{$callMethod}();

			$code = $controlerMethodResponse['status'];
			$content = $controlerMethodResponse['content'];
		}

		http_response_code($code);
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

	private function getRouteDefinitions ($route)
	{
		$keys = get_object_vars($route);

		$definitionsObj = [
			'content' => NULL,
			'isController' => false,
		];

		foreach($keys as $key => $value):

			//echo $key.PHP_EOL;

			switch ($key)
			{
				case 'html':
				case 'content':
				case 'html-content':
				case 'html_content':
					$definitionsObj['isController'] = false;
					$definitionsObj['content'] = $value;
					break;

				case 'render':
				case 'view':
					$definitionsObj['isController'] = false;
					$definitionsObj['content'] = file_get_contents($value);
					break;

				case 'controller':
					$splitedString = explode('@', $value);
					$controller = $splitedString[0];
					$method = $splitedString[1];

					$definitionsObj['isController'] = true;
					$definitionsObj['content'] = [ $controller, $method ];
					break;

				default:
					break;
			}

		endforeach;

		return $definitionsObj;
	}

	private function controllerInstantiator ($controller)
	{
		return new $controller();
	}
}

?>