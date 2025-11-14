<?php

namespace NabuPHP\Http\Responses;

use NabuPHP\Interfaces\ResponseInterface;

use function NabuPHP\Helpers\Rendering\render_view;
use function NabuPHP\Helpers\Strings\constant_finder_replacer;

class ViewResponse implements ResponseInterface
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
		$body = render_view($this->settings['view-path'], $this->settings['view-vars']);

		// Renders view with layout
		if(!is_null($this->configs->getProperty('layouts-folder')))
		{
			$layoutsFolder = $this->configs->getProperty('layouts-folder');
			$layoutFolderPath = constant_finder_replacer($layoutsFolder, $this->configs->getConstants());

			if(isset($this->settings['view-vars']['body']))
			{
				throw new \Exception("Error on view: 'body' is a reserved key-word.");
			}

			$this->settings['view-vars']['body'] = $body;

			$body = render_view($layoutFolderPath.'/_layout.php', $this->settings['view-vars']);
		}

		return $body;
	}

	public function getStatus (): int
	{
		return intval($this->settings['status']) ?? 200;
	}

	public function getContentType (): string
	{
		return 'text/html';
	}
}

?>