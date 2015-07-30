<?php

use Quickform\Entity\FormBuilder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

$app->match('/', function(Request $request) use ($app) {

    // read config file
    $yml = new Parser();
    /** @var array|null $config */
    $config = $yml->parse(file_get_contents(MAIN_PATH . '/config.yml'));
    /** @var Symfony\Component\Form\FormBuilder $formSymfonyBuilder */
    $formSymfonyBuilder = $app['form.factory']->createBuilder('form', $config);

    // build form
    $formBuilder = new FormBuilder($formSymfonyBuilder, $config);
    $form = $formBuilder->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        echo '<pre>'; var_dump($data); die;

        // redirect somewhere
        return $app->redirect('...');
    }

    return $app['twig']->render('form.html.twig', array(
        'seo'   => $config['form']['seo'],
        'title' => $config['form']['title'],
        'form'  => $form->createView(),
    ));
})->bind('form');

/*$app->post('/admin/login_check', function(Request $request) use ($app) {
    // will handle event dispatcher
})->bind('check_path');*/
