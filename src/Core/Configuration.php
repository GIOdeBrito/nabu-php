<?php

namespace NabuPHP\Core;

use function NabuPHP\Helpers\Objects\object_get_keys;

class Configuration
{
	private ?object $configs = NULL;
	private ?object $properties = NULL;
	private ?object $constants = NULL;

	public function __construct ($configPath)
	{
		$this->getConfigurationData($configPath);
	}

	public function getConstants (): object
	{
		return $this->constants;
	}

	public function getProperties (): object
	{
		return $this->properties;
	}

	public function getProperty (string $name): string
	{
		if(!isset($this->properties->{$name}))
		{
			return NULL;
		}

		return $this->properties->{$name};
	}

	private function getConfigurationData (string $configPath): void
	{
		if(!file_exists($configPath))
		{
			$errMsg = "Config file: {$configPathAbs}, not found";
			error_log($errMsg);
			throw new \Exception($errMsg);
		}

		$configs = json_decode(file_get_contents($configPath));

		if(count(object_get_keys($configs)) === 0)
		{
			throw new \Exception("Configuration object is empty");
		}

		$this->configs = $configs;
		$this->properties = $configs->properties;

		if(isset($configs->constants))
		{
			$this->constants = $configs->constants;
		}
	}
}

?>