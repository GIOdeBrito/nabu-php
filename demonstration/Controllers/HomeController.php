<?php

use NabuPHP\MVC\Controller;

class HomeController extends Controller
{
	public function controls ()
	{
		return $this->json(200, [ 'controls' => 'working' ]);
	}
}

?>