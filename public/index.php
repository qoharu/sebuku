<?php
if (PHP_SAPI == 'cli-server') {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';
session_start();
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
// $app->response->headers->set('Content-Type', 'application/json');
function srcloader($filename)
{
    require __DIR__ . "/../src/".$filename;
    return true;
}
function tojson($res,$val){
	$res->getBody()->write(json_encode($val));
	return $res->withHeader('Content-type','application/json;charset=utf-8');
}

require __DIR__ . '/../src/db_handler.php';
require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/routes.php';

$app->run();
