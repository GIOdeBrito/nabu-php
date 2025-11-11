<?php

namespace NabuPHP\Core;

use NabuPHP\Helpers\ObjectHelpers;

class Configuration
{
	private $configs;
	private $properties = NULL;
	private $constants = NULL;

	public function __construct ($configPath)
	{
		$this->getConfigurationData($configPath);
	}

	public function getConstants ()
	{
		return $this->constants;
	}

	public function getProperties ()
	{
		return $this->properties;
	}

	public function getProperty ($name)
	{
		if(!isset($this->properties->{$name}))
		{
			return NULL;
		}

		return $this->properties->{$name};
	}

	private function getConfigurationData ($configPath)
	{
		if(!file_exists($configPath))
		{
			$errMsg = "Config file: {$configPathAbs}, not found";
			error_log($errMsg);
			throw new \Exception($errMsg);
		}

		$configs = json_decode(file_get_contents($configPath));

		if(count(ObjectHelpers::getKeys($configs)) === 0)
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