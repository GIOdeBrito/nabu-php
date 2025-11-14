<?php

namespace NabuPHP\Helpers\Strings;

use function NabuPHP\Helpers\Objects\object_get_keys;

function includes (string $string, array $matches = []): array
{
	$allMatches = [];

	foreach($matches as $value):

		if(strpos($string, $value) !== false)
		{
			$allMatches[] = $value;
		}

	endforeach;

	return $allMatches;
}

function constant_finder_replacer (string $string, object $constants): string
{
	$matches = includes($string, object_get_keys($constants));

	// If no match was found
	if(count($matches) === 0)
	{
		return $string;
	}

	// Replace the found matches only
	foreach($matches as $key):

		$string = str_replace('$'.$key, $constants->{$key}, $string);

	endforeach;

	return $string;
}

?>