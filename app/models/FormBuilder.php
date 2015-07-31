<?php

namespace Quickform\Entity;

use \Symfony\Component\Form\FormBuilder as FormSymfonyBuilder;
use Symfony\Component\Validator\Constraints as Assert;

class FormBuilder
{
    /** @var FormSymfonyBuilder|null $form */
    protected $form;

    /**
     * Initialization
     *
     * @param FormSymfonyBuilder $formSymfonyBuilder
     * @param array|null $structure
     */
    public function __construct(FormSymfonyBuilder $formSymfonyBuilder, $structure)
    {
        if ($structure && $structure['form']['show']) {
            foreach ($structure['form']['fields'] as $field) {

                if ($field['show']) {

                    // validations
                    $options = array();
                    $constrains = array();
                    if ($field['validation']) {
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

                    $formSymfonyBuilder->add($field['name'], $field['type'], $options);
                }
            }

            $formSymfonyBuilder->add('submit', 'submit', array(
                'attr' => array('class' => 'button right')
            ));
        }

        $this->form = $formSymfonyBuilder;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function getForm()
    {
        return $this->form->getForm();
    }
}
