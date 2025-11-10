<?php

namespace NabuPHP\Helpers;

class RouteHelpers
{
	public static function getRouteSettings ($route)
	{
		$keys = get_object_vars($route);

		$settings = [
			'content' => '',
			'content-type' => 'text/html',

			'isController' => false,
			'controller' => '',
			'controllerCallMethod' => ''
		];

		foreach($keys as $key => $value):

			//echo $key.PHP_EOL;

			switch ($key)
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
					$settings['content'] = file_get_contents($value);
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

				default:
					break;
			}

		endforeach;

		return $settings;
	}
}

?>