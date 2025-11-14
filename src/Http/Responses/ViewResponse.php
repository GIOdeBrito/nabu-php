<?php

namespace NabuPHP\Http\Responses;

use NabuPHP\Interfaces\ResponseInterface;

class ViewResponse implements ResponseInterface
{
	private array $settings = [];

	public function __construct (array $settings)
	{
		$this->settings = $settings;
	}

	public function getData (): mixed
	{

	}

	public function isController (): bool
	{
		return $this->settings['isController'];
	}

	public function isView (): bool
	{
		return $this->settings['isView'];
	}

	public function getContentType (): string
	{
		return $this->settings['content-type'];
	}
}

?>