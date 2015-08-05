<?php

namespace Quickform\Models;

use Symfony\Component\Form\FormFactory;
use \Symfony\Component\Form\FormBuilder as FormSymfonyBuilder;
use Symfony\Component\Validator\Constraints as Assert;

class FormBuilder
{
    /** @var FormFactory|null $form */
    protected $form;
    /** @var string $formName */
    protected $formName;
    /** @var array $jsValidation */
    protected $jsValidation = array();

    /**
     * Initialization
     *
     * @param FormFactory $formFactory
     * @param array|null $structure
     */
    public function __construct(FormFactory $formFactory, $structure)
    {
        $formOptions = array();
        if ('js' === $structure['form']['validation']) {
            $formOptions = array('attr' => array(
                'class'      => 'css-form',
                'novalidate' => 'novalidate',
                'ng-submit' => 'submit($event)'
            ));
        }

        $this->formName = $structure['form']['name'];

        /** @var FormSymfonyBuilder $formBuilder */
        $formBuilder = $formFactory->createNamedBuilder($this->formName, 'form', null, $formOptions);

        if ($structure && $structure['form']['show']) {
            foreach ($structure['form']['fields'] as $field) {

                if ($field['show']) {
                    // validations
                    $options = array();
                    $constrains = array();
                    if (isset($field['validation']) && $field['validation']) {

                        foreach ($field['validation'] as $key => $validation) {
                            $data = $this->setValidation(new Validation($key, $field['name'], $validation));
                            $constrains = array_merge($constrains, $data['constrains']);
                            $options = array_merge($options, $data['options']);;
                        }

                        if (count($constrains) > 0) {
                            $options['constraints'] = $constrains;
                        }
                    }

                    /**
                     * ng-minlength="{ number }"
                     * ng-maxlength="{ number }"
                     * ng-pattern="{ string }"
                     */

                    if ('js' === $structure['form']['validation']) {
                        $attr = array('ng-model' => $this->formName . '.' . $field['name']);
                        if (!isset($options['required']) || !$options['required']) {
                            $attr = array_merge($attr, array('ng-required' => 'false'));
                        }

                        /*if (isset($options['constraints'][3]->pattern)) {
                            $attr = array_merge($attr, array('ng-pattern' => $options['constraints'][3]->pattern));
                        }*/

                        $options = array_merge($options, array('attr' => $attr));
                    }

                    $formBuilder->add($field['name'], $field['type'], $options);
                }
            }

            $formBuilder->add('submit', 'submit', array(
                'attr' => array(
                    'class' => 'button right',
                )
            ));
        }

        $this->form = $formBuilder;
    }

    /**
     * Sets validation
     *
     * @param Validation $validationClass
     * @return array
     */
    protected function setValidation(Validation $validationClass)
    {
        $options = array();
        $constrains = array();

        if ('required' === $validationClass->getKey()) {
            $currentConstrain = new Assert\NotBlank(array('message' => $validationClass->getMessage()));
            $this->addJsValidation(array(
                'field'      => $validationClass->getName(),
                'message'    => $currentConstrain->message,
                'validation' => $validationClass->getKey(),
            ));
            $constrains = array_merge($constrains, array($currentConstrain));
            $options = array_merge($options, array('required' => $validationClass->getValue()));
        }

        if ($validationClass->getValue() && ('min' === $validationClass->getKey())) {
            $errorOptions = array('min' => $validationClass->getValue());

            if ($validationClass->getMessage()) {
                $errorOptions = array_merge($errorOptions, array('minMessage' => $validationClass->getMessage()));
            }
            $currentConstrain = new Assert\Length($errorOptions);
            $this->addJsValidation(array(
                'field'      => $validationClass->getName(),
                'message'    => $currentConstrain->minMessage,
                'validation' => $validationClass->getKey(),
            ));

            $constrains = array_merge($constrains, array($currentConstrain));
        }

        if ($validationClass->getValue() && ('max' == $validationClass->getKey())) {
            $errorOptions = array('max' => $validationClass->getValue());

            if ($validationClass->getMessage()) {
                $errorOptions = array_merge($errorOptions, array('maxMessage' => $validationClass->getMessage()));
            }
            $currentConstrain = new Assert\Length($errorOptions);
            $this->addJsValidation(array(
                'field'      => $validationClass->getName(),
                'message'    => $currentConstrain->maxMessage,
                'validation' => $validationClass->getKey(),
            ));

            $constrains = array_merge($constrains, array($currentConstrain));
        }

        if ('email' == $validationClass->getKey()) {
            $errorOptions = array();

            if ($validationClass->getMessage()) {
                $errorOptions = array_merge($errorOptions, array('message' => $validationClass->getMessage()));
            }
            $currentConstrain = new Assert\Email($errorOptions);
            $this->addJsValidation(array(
                'field'      => $validationClass->getName(),
                'message'    => $currentConstrain->message,
                'validation' => $validationClass->getKey(),
            ));

            $constrains = array_merge($constrains, array($currentConstrain));
        }

        if ($validationClass->getValue() && ('regexp' == $validationClass->getKey())) {
            $errorOptions = array('pattern' => $validationClass->getValue());

            if ($validationClass->getMessage()) {
                $errorOptions = array_merge($errorOptions, array('message' => $validationClass->getMessage()));
            }

            $currentConstrain = new Assert\Regex($errorOptions);
            $this->addJsValidation(array(
                'field'      => $validationClass->getName(),
                'message'    => $currentConstrain->message,
                'validation' => $validationClass->getKey(),
            ));
            $constrains = array_merge($constrains, array($currentConstrain));
        }

        return array(
            'options'    => $options,
            'constrains' => $constrains,
        );
    }

    /**
     * @param array $item
     */
    protected function addJsValidation($item)
    {
        $this->jsValidation[$this->formName . "[" . $item['field'] . "]"][] = $item;
    }

    /**
     * @return string
     */
    public function getJsValidation()
    {
        return json_encode(array($this->formName => $this->jsValidation));
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function getForm()
    {
        return $this->form->getForm();
    }
}
