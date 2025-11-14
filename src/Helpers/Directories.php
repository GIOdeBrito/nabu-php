<?php

namespace NabuPHP\Helpers\Directories;

function get_directories (string $path): array
{
	$directories = array_map(function ($dir) use ($path)
	{
		if(is_dir($dir))
		{
			return trim(explode($path.'/', $dir)[1]);
		}
	}, glob($path.'/*'));

	return array_filter($directories);
}

function get_directories_recursive (string $base): array
{
	$dirArray = get_directories($base);
	$currentDirs = [];

	if(count($dirArray) > 0)
	{
		foreach($dirArray as $dir):

			$currentDirs[$dir] = get_directories_recursive($base.'/'.$dir);

		endforeach;
	}

	return $currentDirs;
}

?>