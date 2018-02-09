<?php

use App\FormType\LogOutType;
use App\Service\CookieAuthService;
use App\Service\StudentGateway;
use App\Service\StudentTwigExtension;
use App\Validation\StudentEmailUniqueValidator;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;

$app = new Silex\Application();

$app->register(new Silex\Provider\CsrfServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path'           => __DIR__ . '/../templates',
    'twig.form.templates' => ['bootstrap_3_layout.html.twig'],
]);

$app->extend('twig', function (Twig_Environment $twig, $app) {
    $twig->addExtension(new StudentTwigExtension($app['url_generator']));
    $logOutForm = $app['form.factory']->createBuilder(LogOutType::class)->getForm()->createView();
    $twig->addGlobal('logOutForm', $logOutForm);

    return $twig;
});

$app->register(new Silex\Provider\ValidatorServiceProvider(), [
    'validator.validator_service_ids' => [
        StudentEmailUniqueValidator::class => 'studentEmailUniqueValidator',
    ],
]);

$app->register(new SessionServiceProvider());
$app->register(new FormServiceProvider());

$app['studentEmailUniqueValidator'] = function ($app) {
    return new StudentEmailUniqueValidator($app['studentGateway']);
};

$app['studentGateway'] = function ($app) {
    return new StudentGateway($app['pdo']);
};

$app['authService'] = function ($app) {
    return new CookieAuthService($app['studentGateway']);
};

$app['pdo'] = function () use ($config) {
    return new \PDO(
        "pgsql:host={$config['host']};dbname={$config['dbname']}",
        $config['username'],
        $config['password'],
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
    );
};

// This providers are required by default twig's templates
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), ['translator.domains' => []]);

return $app;
