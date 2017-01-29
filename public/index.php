<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config_app.php';
$app = require __DIR__ . '/../src/app.php';
require __DIR__ . '/../src/routes.php';
$app['debug'] = false;

$app->run();