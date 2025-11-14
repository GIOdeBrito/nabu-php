<?php

namespace NabuPHP\Helpers\Objects;

function object_get_keys (object $obj): array
{
	return array_keys(get_object_vars($obj));
}

?>