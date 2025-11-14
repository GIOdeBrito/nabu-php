<?php

namespace NabuPHP\Http;

class Request
{
	public function getMethod (): string
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	public function getPath (): string
	{
		return $_SERVER['REQUEST_URI'];
	}
}

?>