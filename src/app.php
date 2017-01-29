<?php

$app = new Silex\Application();

$app->register(new Silex\Provider\CsrfServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../templates',
]);
$app->extend('twig', function (Twig_Environment $twig, $app) {
    $twig->addExtension(new \App\StudentTwigExtension($app['url_generator']));
    $logOutForm = $app['form.factory']->createBuilder(\App\Type\LogOutType::class)->getForm()->createView();
    $twig->addGlobal('logOutForm', $logOutForm);
    return $twig;
});

$app['twig.form.templates'] = ['bootstrap_3_layout.html.twig'];

$app->register(new Silex\Provider\ValidatorServiceProvider(), [
    'validator.validator_service_ids' => [
        \App\StudentEmailUniqueValidator::class => 'studentEmailUniqueValidator',
    ]
]);

$app->register(new \Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());

$app['studentEmailUniqueValidator'] = function ($app) {
    return new \App\StudentEmailUniqueValidator($app['studentGateway']);
};

$app['studentGateway'] = function ($app) {
    return new \App\StudentGateway($app['pdo']);
};

$app['authService'] = function ($app) {
    return new \App\CookieAuthService($app['studentGateway']);
};

$app['pdo'] = function () use ($config) {
    return new \PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8",
        $config['username'],
        $config['password'],
        [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8' COLLATE 'utf8_general_ci', sql_mode='STRICT_ALL_TABLES'"
        ]
    );
};

// Эти провайдеры нужны для дефолтных шаблонов твига
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), ['translator.domains' => []]);

return $app;
