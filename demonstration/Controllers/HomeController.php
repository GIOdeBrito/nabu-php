<?php

class HomeController
{
	public function __construct ()
	{

	}

	public function index ()
	{
		return [ 'status' => 200, 'content' => "<h1>Receba</h1>" ];
	}

	public function health ()
	{
		return [ 'status' => 200, 'content' => "<p>Healthy</p>" ];
	}
}

?>