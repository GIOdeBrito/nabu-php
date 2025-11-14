<?php

namespace NabuPHP\Routing;

use NabuPHP\Http\Request;

use function NabuPHP\Helpers\Strings\constant_finder_replacer;
use function NabuPHP\Helpers\Routes\{ get_route_settings, get_route_response };

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

		$settings = get_route_settings($route, $this->configs->getConstants());
		$response = get_route_response($settings, $this->configs);

		$data = $response->getData();
		$contentType = $response->getContentType();

		$this->sendResponse(200, $data, $contentType);

		die();
	}

	private function sendResponse (int $code, string $content, string $contentType): void
	{
		header("Content-Type: ".$contentType);
		http_response_code(intval($code));
		echo $content;
	}

	private function routeLookup (string $method, string $path): ?object
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
		$controller = controller_instantiator($controllerName);

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

		// Render the view's body
		ob_start();
		include $path;
		$body = ob_get_clean();

		// Renders view with layout
		if(!is_null($this->configs->getProperty('layouts-folder')))
		{
			$layoutsFolder = $this->configs->getProperty('layouts-folder');
			$layout_folder = constant_finder_replacer($layoutsFolder, $this->configs->getConstants());

			ob_start();
			include $layout_folder.'/_layout.php';
			$body = ob_get_clean();
		}

		$this->sendResponse(200, $body, 'text/html');
	}
}

?>