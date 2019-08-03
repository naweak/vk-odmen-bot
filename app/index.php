<?php
require_once "./vendor/autoload.php";
require_once "./src/helpers.php";

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

use \Slim\App;

$app = new App();

$app->post('/', 'Controllers\Bot');

$app->run();
