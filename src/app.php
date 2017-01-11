<?php

$app = new Silex\Application();

$app->register(new Silex\Provider\CsrfServiceProvider());
$app->register(new \Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../templates',
]);
$app->extend('twig', function (Twig_Environment $twig, $app) {
    $twig->addExtension(new \App\StudentTwigExtension($app['url_generator']));
    return $twig;
});

$app['twig.form.templates'] = ['bootstrap_3_layout.html.twig'];

$app->register(new Silex\Provider\ValidatorServiceProvider(), [
    'validator.validator_service_ids' => [
        \App\UniqueEmailValidator::class => 'uniqueEmailValidator',
    ]
]);

$app->register(new Silex\Provider\FormServiceProvider());
// Эти провайдеры нужны для дефолтных шаблонов твига
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), ['translator.domains' => []]);

$app['uniqueEmailValidator'] = function ($app) {
    return new \App\UniqueEmailValidator($app['studentGateway']);
};

$app['studentGateway'] = function ($app) {
    return new \App\StudentGateway($app['pdo']);
};

$app['authService'] = function ($app) {
    return new \App\AuthService($app['studentGateway']);
};

$app['pdo'] = function () {
    $config = require __DIR__ . '/../config.php';
    return new \PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8",
        $config['username'],
        $config['password'],
        [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8', sql_mode='STRICT_ALL_TABLES'"
        ]
    );
};

return $app;
