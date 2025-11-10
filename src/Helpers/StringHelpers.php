<?php

namespace NabuPHP\Helpers;

use NabuPHP\Helpers\ObjectHelpers;

class StringHelpers
{
	public static function includes ($string, $matches = [])
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

	public static function constantFinderReplacer ($string, $constants)
	{
		$matches = self::includes($string, ObjectHelpers::getKeys($constants));

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
}

?>