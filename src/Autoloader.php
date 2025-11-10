<?php

/* =======================================
	BelPHP autoloader
======================================= */

spl_autoload_register(function ($classname)
{
	// Get the root folder of the framework
	$root = __DIR__.'/';
	$namespace = 'NabuPHP';

	// Splits the path to the selected class
	$paths = explode('/', str_replace('\\', '/', $classname));

	if($paths[0] !== $namespace)
	{
		return;
	}

	// Removes the first item of the array
	array_shift($paths);

	$classPath = implode('/', $paths);

	// Searches for the file within GioPHP's folder
	$fullpath = $root.str_replace('\\', '/', $classPath).'.php';

	if(!file_exists($fullpath))
	{
		throw new Exception("Class '{$classname}' not found");
	}

	require $fullpath;
});

?>