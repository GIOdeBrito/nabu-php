<?php

define('__ABSPATH__', __DIR__);

require '../src/Autoloader.php';

include 'Controllers/HomeController.php';

use NabuPHP\Core\Application;

$app = new Application(__ABSPATH__."/confs.json");
$app->run();

?>