<?php

namespace NabuPHP\Http;

class Request
{
	public function __construct ()
	{
		//var_dump($_SERVER);
		//die();
	}

	public function getMethod ()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	public function getPath ()
	{
		return $_SERVER['REQUEST_URI'];
	}
}

?>