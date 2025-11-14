<?php

namespace NabuPHP\Http\Responses;

use NabuPHP\Interfaces\ResponseInterface;

class JSONResponse implements ResponseInterface
{
	private array $settings = [];
	private object $configs;

	public function __construct (array $settings, object $configs)
	{
		$this->settings = $settings;
		$this->configs = $configs;
	}

	public function getData (): string
	{
		$data = $this->settings['content'];

		return json_encode($data);
	}

	public function getStatus (): int
	{
		return intval($this->settings['status']) ?? 200;
	}

	public function getContentType (): string
	{
		return $this->settings['content-type'];
	}
}

?>