<?php

use Symfony\Component\Validator\Constraints as Assert;

$app->error(function (\Exception $e, $code) use($app) {
    $app['monolog']->addError($e->getMessage());

    switch ($code) {
        case 404:
            $message = 'Запрос не найден :-(';
            break;
        default:
            $message = 'Произошло что-то ужасное!!!';
            break;
    }

    if ($app['debug']) {
        $message = $e->getMessage();
    }

    return $app['twig']->render('error.html.twig', array(
        'message' => $message,
        'code'    => $code,
        'debug'   => $app['debug'] ? $e->getTraceAsString() : ''
    ));
});
