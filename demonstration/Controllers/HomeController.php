<?php

use NabuPHP\MVC\Controller;

class HomeController extends Controller
{
	public function __construct ()
	{

	}

	public function index ()
	{
		return $this->html(200, "<h1>Receba</h1>");
	}

	public function health ()
	{
		return $this->json(200, [ 'message' => "Healthy" ]);
	}
}

?>