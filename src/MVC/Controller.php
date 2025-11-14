<?php

namespace NabuPHP\MVC;

abstract class Controller
{
	public function html (int $code = 200, array $data = []): array
	{
		return [ 'status' => $code, 'data' => $data, 'content-type' => 'text/html' ];
	}

	public function json (int $code = 200, array $data = []): array
	{
		return [ 'status' => $code, 'data' => json_encode($data), 'content-type' => 'application/json' ];
	}
}

?>