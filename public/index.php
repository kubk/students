<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../src/app.php';
require __DIR__ . '/../src/routes.php';

$app->error(function (\Exception $e, Request $request, $code) {
    if ($code === 404) {
        $message = 'The requested page could not be found.';
    } else {
        error_log($e);
        $message = 'We are sorry, but something went terribly wrong.';
    }
    return new Response($message, Response::HTTP_SERVICE_UNAVAILABLE);
});

$app->run();