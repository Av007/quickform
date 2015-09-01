<?php

use Quickform\Models\FormBuilder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

$app->match('/', function(Request $request) use ($app) {
    // read config file
    $yml = new Parser();
    /** @var array|null $config */
    $config = $yml->parse(file_get_contents(MAIN_PATH . '/form.yml'));

    // build form
    $formBuilder = new FormBuilder($app['form.factory'], $config, $app['translator']);
    $form = $formBuilder->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = array_filter($form->getData());
        $files = $data['attachment'];
        unset($data['attachment']);
        $date = new \DateTime();

        $f = array();

        foreach ($files as $file) {
            if (isset($file['file']) && is_array($file['file'])) {
                foreach ($file['file'] as $singleFile) {
                    $f[] = $singleFile;
                }
            } else {
                $f[] = $file;
            }
        }
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $size = 0;
        foreach (array_filter($f) as $file) {
            $size += $file->getSize();
        }

        if (20000 < $size) {
            return $app['twig']->render('form.html.twig', array(
                'title'     => $config['form']['title'],
                'form'      => $form->createView(),
                'sizeError' => true
            ));
        }

        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $app['db'];
        $isSaved = $connection->insert('form_data', array(
            'data'       => json_encode($data),
            'files'      => json_encode($files),
            'user_agent' => $request->headers->get('User-Agent'),
            'created_at' => $date->format('Y-m-d H:i:s'),
            'ip'         => $request->getClientIp()
        ));

        $message = Swift_Message::newInstance()
            ->setSubject('Quickform')
            ->setFrom(array('noreply@quickform.com' => 'Quickform corp.'))
            ->setTo($config['emails'])
            ->setBody($app['twig']->render('mail.html.twig', array(
                    'data' => $data
                )
            ))
        ;

        foreach ($files as $file) {
            if (isset($file['file']) && is_array($file['file'])) {
                foreach ($file['file'] as $singleFile) {
                    $message->attach(Swift_Attachment::newInstance($singleFile));
                }
            } else {
                $message->attach(Swift_Attachment::newInstance($file));
            }
        }

        /** @var Swift_Mailer $mailer */
        $mailer = $app['mailer'];
        $mailer->send($message);

        //$sql = "SELECT * FROM form_data";
        //echo '<pre>'; var_dump($connection->fetchAll($sql), $post); die;

        // redirect somewhere
        return $app->redirect('/');
    }

    return $app['twig']->render('form.html.twig', array(
        'title' => $config['form']['title'],
        'form'  => $form->createView(),
    ));
})->bind('form');

// change locale
$app->get('/locale', function(Request $request) use ($app) {
    // setup language
    $lang = $app['locale_fallback'];
    $lang = $lang == 'en' ? 'ru' : 'en';
    /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
    $session = $app['session'];
    $app['locale_fallback'] = $lang;
    $app['locale'] = $lang;
    $session->set('locale', $lang);

    return $app->redirect(str_replace('/locale', '', $request->getRequestUri()));
})->bind('change_lang');

$app->get('/create', function(Request $request) use ($app) {
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $app['db'];
    $connection->exec(file_get_contents(MAIN_PATH . '/schema.sql'));

    return $app->redirect('/');
})->bind('create');