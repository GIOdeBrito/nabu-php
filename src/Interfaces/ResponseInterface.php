<?php

namespace NabuPHP\Interfaces;

interface ResponseInterface
{
	function __construct (array $settings, object $configs);

	function getData(): mixed;

	function getStatus(): int;

	function getContentType(): string;
}

?>