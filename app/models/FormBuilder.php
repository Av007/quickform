<?php

namespace Quickform\Models;

use Quickform\Models\Constrains;
use Symfony\Component\Form\FormFactory;
use \Symfony\Component\Form\FormBuilder as FormSymfonyBuilder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class FormBuilder
{
    /** @var FormFactory|null $form */
    protected $form;

    /** @var string $formName */
    protected $formName;

    /** @var array $jsValidation */
    protected $jsValidation = array();

    /** @var bool $validationType */
    protected $validationType = false;

    /** @var \Silex\Translator $translator */
    protected $translator;

    /**
     * Initialization
     *
     * @param FormFactory $formFactory
     * @param array|null $structure
     * @param mixed $translator
     */
    public function __construct(FormFactory $formFactory, $structure, $translator)
    {
        $this->validationType = $structure['form']['validation'];
        $this->translator = $translator;

        $formOptions = array();
        if ('js' === $this->validationType) {
            $formOptions = array('attr' => array(
                'class'         => 'css-form',
                'ng-controller' => 'FormCtrl',
                'novalidate'    => 'novalidate',
                'ng-submit'     => 'submit($event)'
            ));
        }

        // sets defaults
        $defaults = array();
        foreach ($structure['form']['fields'] as $field) {
            if ($field['type'] == 'collection' && $field['show']) {
                $defaults = array('attachment' => array(array('file' => new File('%kernel.root_dir%/../web/', false))));
            }
        }

        $this->formName = $structure['form']['name'];

        /** @var FormSymfonyBuilder $formBuilder */
        $formBuilder = $formFactory->createNamedBuilder($this->formName, 'form', $defaults, $formOptions);

        if ($structure && $structure['form']['show']) {
            foreach ($structure['form']['fields'] as $field) {

                if ($field['show']) {
                    // validations
                    $options = array();
                    $constrains = array();
                    $attributes = array();
                    if (isset($field['validation']) && $field['validation']) {

                        if (isset($field['label']) && $field['label']) {
                            $options['label'] = $field['label'];
                        }

                        foreach ($field['validation'] as $key => $validation) {
                            $data = $this->setValidation(new Validation($key, $field['name'], $validation));
                            $options = array_merge_recursive($options, $data->getOptions());;
                            $constrains = array_merge($constrains, array($data->getConstrains()));
                            $attributes[] = $data->getJsValidation();
                        }

                        if ('js' === $this->validationType) {
                            $options['attr'] = isset($options['attr']) ? $options['attr'] : array();
                            $options['attr'] = array_merge($options['attr'], array(
                                'ng-model'    => $this->formName . '.' . $field['name']
                            ));

                            $options['attr']['ng-required'] = isset($options['attr']['ng-required']) ? $options['attr']['ng-required'] : 'false';
                        }

                        if (count($constrains) > 0) {
                            $options['attr']        = array_merge($options['attr'], array('data-validation' => json_encode($attributes)));
                            $options['constraints'] = $constrains;
                        }
                    }

                    // collection field
                    if ($field['type'] == 'collection') {
                        $options['type']         = new FileType($validation);
                        $options['allow_add']    = true;
                        $options['allow_delete'] = true;
                    }

                    $formBuilder->add($field['name'], $field['type'], $options);
                }
            }

            $formBuilder->add('submit', 'submit', array(
                'attr' => array('class' => 'button right')
            ));
        }

        $this->form = $formBuilder;
    }

    /**
     * Sets validation
     *
     * @param Validation $validationClass
     * @return mixed
     */
    protected function setValidation(Validation $validationClass)
    {
        if ('required' === $validationClass->getKey()) {
            return new Constrains\Required($validationClass, 'js' === $this->validationType, $this->translator);
        } elseif ($validationClass->getValue() && ('min' === $validationClass->getKey())) {
            return new Constrains\Min($validationClass, 'js' === $this->validationType, $this->translator);
        } elseif ($validationClass->getValue() && ('max' == $validationClass->getKey())) {
            return new Constrains\Max($validationClass, 'js' === $this->validationType, $this->translator);
        } elseif ('email' == $validationClass->getKey()) {
            return new Constrains\Email($validationClass, 'js' === $this->validationType, $this->translator);
        } elseif ($validationClass->getValue() && ('regexp' == $validationClass->getKey())) {
            return new Constrains\Regexp($validationClass, 'js' === $this->validationType, $this->translator);
        } elseif ($validationClass->getValue() && ('phone' == $validationClass->getKey())) {
            return new Constrains\Phone($validationClass, 'js' === $this->validationType, $this->translator);
        } elseif ('file' == $validationClass->getKey()) {
            return new Constrains\Collection($validationClass);
        }

        throw new \LogicException('Can\'t find form type');
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function getForm()
    {
        return $this->form->getForm();
    }
}
