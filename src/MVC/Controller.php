<?php

namespace NabuPHP\MVC;

abstract class Controller
{
	public function __construct ()
	{

	}

	public function html ($code = 200, $data = [])
	{
		return [ 'status' => $code, 'data' => $data, 'content-type' => 'text/html' ];
	}

	public function json ($code = 200, $data = [])
	{
		return [ 'status' => $code, 'data' => json_encode($data), 'content-type' => 'application/json' ];
	}
}

?>