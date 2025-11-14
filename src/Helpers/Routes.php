<?php

namespace NabuPHP\Helpers\Routes;

use function NabuPHP\Helpers\Strings\constant_finder_replacer;

function get_route_settings (object $route, object $constants): array
{
	$keys = get_object_vars($route);

	$settings = [
		'content' => '',
		'content-type' => 'text/html',

		'view-path' => '',
		'view-vars' => [],
		'isView' => false,

		'controller' => '',
		'controllerCallMethod' => '',
		'isController' => false
	];

	foreach($keys as $key => $value):

		//echo $key.PHP_EOL;

		// Apply constant replacement if the value is a string
		if(gettype($value) === 'string' && !is_null($constants))
		{
			$value = constant_finder_replacer($value, $constants);
		}

		switch($key)
		{
			case 'html':
			case 'content':
			case 'html-content':
			case 'html_content':
				$settings['isController'] = false;
				$settings['content'] = $value;
				break;

			case 'render':
			case 'view':
				$settings['isController'] = false;
				$settings['isView'] = true;
				$settings['view-path'] = $value;
				break;

			case 'controller':
				$splitedString = explode('@', $value);
				$controller = $splitedString[0];
				$method = $splitedString[1];

				$settings['isController'] = true;
				$settings['controller'] = $controller;
				$settings['controllerCallMethod'] = $method;
				break;

			case 'content-type':
				$settings['content-type'] = $value;
				break;

			case "vars":
			case "viewdata":
			case "view-data":
				$settings['view-vars'] = (array) $value;
				break;

			default:
				break;
		}

	endforeach;

	return $settings;
}

function excavate_JSON_route_files (string $routesPath, object $constants): array
{
	$path = $routesPath;

	// Replaces constants in property strings
	if($constants)
	{
		$path = constant_finder_replacer($path, $constants);
	}

	// Get all JSON route files
	$jsonFiles = glob($path.'/*.json');

	return $jsonFiles;
}

?>