<?php

use Quickform\Models\FormBuilder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

$app->match('/', function(Request $request) use ($app) {

    // read config file
    $yml = new Parser();
    /** @var array|null $config */
    $config = $yml->parse(file_get_contents(MAIN_PATH . '/config.yml'));

    // build form
    $formBuilder = new FormBuilder($app['form.factory'], $config);
    $form = $formBuilder->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        echo '<pre>'; var_dump($data); die;

        // redirect somewhere
        return $app->redirect('...');
    }

    return $app['twig']->render('form.html.twig', array(
        'title'        => $config['form']['title'],
        'form'         => $form->createView(),
        'jsValidation' => $formBuilder->getJsValidation(),
    ));
})->bind('form');

/*$app->post('/admin/login_check', function(Request $request) use ($app) {
    // will handle event dispatcher
})->bind('check_path');*/
