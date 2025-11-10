<?php

namespace NabuPHP\Helpers;

final class ControllerHelpers
{
	public static function controllerInstantiator ($controller)
	{
		return new $controller();
	}
}

?>