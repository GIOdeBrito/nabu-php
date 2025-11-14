<?php

namespace NabuPHP\Http\Responses;

use NabuPHP\Interfaces\ResponseInterface;

use function NabuPHP\Helpers\Controllers\controller_instantiator;

class ControllerResponse implements ResponseInterface
{
	private array $settings = [];
	private object $configs;

	private int|string $status = 200;
	private ?string $contentType = NULL;

	public function __construct (array $settings, object $configs)
	{
		$this->settings = $settings;
		$this->configs = $configs;
	}

	public function getData (): string
	{
		$controllerName = $this->settings['controller'];
		$callMethod = $this->settings['controllerCallMethod'];

		$controller = controller_instantiator($controllerName);

		$controlerMethodResponse = $controller->{$callMethod}();

		// If the key 'status' was set
		if(isset($controlerMethodResponse['status']))
		{
			$this->status = intval($controlerMethodResponse['status']);
		}

		$content = $controlerMethodResponse['data'];
		$this->contentType = $controlerMethodResponse['content-type'];

		return $content;
	}

	public function getStatus (): int
	{
		return intval($this->status) ?? 200;
	}

	public function getContentType (): string
	{
		return $this->contentType ?? $this->settings['content-type'];
	}
}

?>