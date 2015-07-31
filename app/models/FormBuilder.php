<?php

namespace Quickform\Entity;

use Symfony\Component\Form\FormFactory;
use \Symfony\Component\Form\FormBuilder as FormSymfonyBuilder;
use Symfony\Component\Validator\Constraints as Assert;

class FormBuilder
{
    /** @var FormFactory|null $form */
    protected $form;
    /** @var string $formName */
    protected $formName;

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
                'novalidate' => 'novalidate'
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

                            if ('required' === $key) {
                                $constrains = array_merge($constrains, array(new Assert\NotBlank(array('message' => $validation['message']))));
                                $options = array_merge($options, array('required' => $validation['value']));
                            }

                            if ($validation['value'] && ('min' === $key)) {
                                $errorOptions = array('min' => $validation['value']);

                                if ($validation['message']) {
                                    $errorOptions = array_merge($errorOptions, array('minMessage' => $validation['message']));
                                }

                                $constrains = array_merge($constrains, array(new Assert\Length($errorOptions)));
                            }

                            if ($validation['value'] && ('max' == $key)) {
                                $errorOptions = array('max' => $validation['value']);

                                if ($validation['message']) {
                                    $errorOptions = array_merge($errorOptions, array('maxMessage' => $validation['message']));
                                }

                                $constrains = array_merge($constrains, array(new Assert\Length($errorOptions)));
                            }

                            if ('email' == $key) {
                                $errorOptions = array();

                                if ($validation['message']) {
                                    $errorOptions = array_merge($errorOptions, array('message' => $validation['message']));
                                }

                                $constrains = array_merge($constrains, array(new Assert\Email($errorOptions)));
                            }

                            if ($validation['value'] && ('regexp' == $key)) {
                                $errorOptions = array('pattern' => $validation['value']);

                                if ($validation['message']) {
                                    $errorOptions = array_merge($errorOptions, array('message' => $validation['message']));
                                }

                                $constrains = array_merge($constrains, array(new Assert\Regex($errorOptions)));
                            }
                        }

                        if (count($constrains) > 0) {
                            $options['constraints'] = $constrains;
                        }
                    }

                    $options = array_merge($options, array('attr' => array('ng-model' => $this->formName . '.' . $field['name'])));
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
     * @return \Symfony\Component\Form\Form
     */
    public function getForm()
    {
        return $this->form->getForm();
    }
}
