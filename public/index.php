<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require_once __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../src/app.php';
require __DIR__ . '/../src/routes.php';

$app->error(function (\Exception $e, Request $request, $code) {
    if ($e instanceof NotFoundHttpException && $code === Response::HTTP_NOT_FOUND) {
        $message = 'The requested page could not be found.';
    } else {
        error_log($e);
        $message = 'We are sorry, but something went terribly wrong.';
        $code = Response::HTTP_SERVICE_UNAVAILABLE;
    }
    return new Response($message, $code);
});

$app->run();