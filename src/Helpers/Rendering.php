<?php

namespace NabuPHP\Helpers\Rendering;

function render_view (string $path, array $data = [])
{
	extract($data);

	ob_start();
	require $path;
	return ob_get_clean();
}

?>