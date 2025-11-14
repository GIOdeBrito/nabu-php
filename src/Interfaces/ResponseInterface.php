<?php

namespace NabuPHP\Interfaces;

interface ResponseInterface
{
	function __construct (array $settings);

	function getData(): mixed;

	function isController(): bool;

	function isView(): bool;

	function getContentType(): string;
}

?>