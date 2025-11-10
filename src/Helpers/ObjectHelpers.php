<?php

namespace NabuPHP\Helpers;

class ObjectHelpers
{
	public static function getKeys ($obj)
	{
		return array_filter(array_keys(get_object_vars($obj)));
	}
}

?>