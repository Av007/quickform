<?php
defined('MAIN_PATH') || define('MAIN_PATH', realpath(__DIR__ . '/../app'));
require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Parser;

// read config file
$yml = new Parser();
/** @var array|null $config */
$config = $yml->parse(file_get_contents(MAIN_PATH . '/config.yml'));

$app = new Silex\Application();
$app['debug'] = $config['debug'];

if ($app['debug']) {
    ini_set('display_errors', 1);
}

$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new SwiftmailerServiceProvider(), $config['mail']);
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback'    => $config['locale'],
    'locale'             => $config['locale'],
    'translator.domains' => array(),
));

// enable localization
$app['translator'] = $app->share($app->extend('translator', function(Silex\Translator $translator) {
    $translator->addLoader('yaml', new YamlFileLoader());
    $translator->addResource('yaml', MAIN_PATH . '/locales/en.yml', 'en');
    $translator->addResource('yaml', MAIN_PATH . '/locales/ru.yml', 'ru');
    return $translator;
}));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => MAIN_PATH . '/logs/app.log',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'    => array(MAIN_PATH . '/views'),
    //'twig.options' => array('cache' => MAIN_PATH . '/cache/twig'),
));
$app->before(function () use ($app) {
    $app['translator']->addLoader('xlf', new XliffFileLoader());
    $app['translator']->addResource('xlf', __DIR__ . '/../vendor/symfony/validator/Resources/translations/validators.ru.xlf', 'ru', 'validators');
});

// apply localization
$app->before(function () use ($app, $config) {
    $config['app']['lang'] = $config['locale'];
    $app['lang'] = $config['locale'];
    $app['locale'] = $config['locale'];
    return $app;
});


require_once MAIN_PATH . '/controllers/error.php';
require_once MAIN_PATH . '/controllers/form.php';
require_once MAIN_PATH . '/models/FormBuilder.php';
require_once MAIN_PATH . '/models/Validation.php';
require_once MAIN_PATH . '/models/constrains/Email.php';
require_once MAIN_PATH . '/models/constrains/Phone.php';
require_once MAIN_PATH . '/models/constrains/Regexp.php';
require_once MAIN_PATH . '/models/constrains/Required.php';
require_once MAIN_PATH . '/models/constrains/Max.php';
require_once MAIN_PATH . '/models/constrains/Min.php';
require_once MAIN_PATH . '/models/constrains/Collection.php';
require_once MAIN_PATH . '/models/FileType.php';

$app->run();
