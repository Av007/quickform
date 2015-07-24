<?php
defined('MAIN_PATH') || define('MAIN_PATH', realpath(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\Translation\Loader\XliffFileLoader;

$app = new Silex\Application();
$app['debug'] = true;

if ($app['debug']) {
    ini_set('display_errors', 1);
}

$app->register(new ValidatorServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback'    => 'ru',
    'locale'             => 'ru',
    'translator.domains' => array(),
));
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../logs/app.log',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'    => array(__DIR__ . '/views'),
    //'twig.options' => array('cache' => __DIR__ . '/../cache/twig'),
));
$app->before(function () use ($app) {
    $app['translator']->addLoader('xlf', new XliffFileLoader());
    $app['translator']->addResource('xlf', __DIR__ . '/../vendor/symfony/validator/Symfony/Component/Validator/Resources/translations/validators.ru.xlf', 'ru', 'validators');
});

$app['swiftmailer.options'] = array(
    'host'       => 'smtp.gmail.com',
    'port'       => 465,
    'username'   => 'grandglory7@gmail.com',
    'password'   => 'moldcell',
    'encryption' => 'ssl',
    'auth_mode'  => 'login'
);

require_once MAIN_PATH . '/controllers/error.php';
require_once MAIN_PATH . '/models/Field.php';

$app->run();