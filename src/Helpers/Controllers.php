<?php

namespace NabuPHP\Helpers\Controllers;

function controller_instantiator (string $controller): object
{
	return new $controller();
}

?>