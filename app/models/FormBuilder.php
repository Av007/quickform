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

                            if ('min' === $key) {
                                $constrains = array_merge($constrains, array(
                                    new Assert\Length(array(
                                        'min'        => $validation['value'],
                                        'minMessage' => $validation['message'],
                                    ))
                                ));
                            }

                            if ('max' == $key) {
                                $constrains = array_merge($constrains, array(
                                    new Assert\Length(array(
                                        'max'        => $validation['value'],
                                        'maxMessage' => $validation['message'],
                                    ))
                                ));
                            }

                            if ('email' == $key) {
                                $constrains = array_merge($constrains, array(
                                    new Assert\Email(array(
                                        'message' => $validation['message']
                                    ))
                                ));
                            }

                            if ('regexp' == $key) {
                                $constrains = array_merge($constrains, array(
                                    new Assert\Regex(array(
                                        'pattern' => $validation['value'],
                                        'message' => $validation['message']
                                    ))
                                ));
                            }


                        }

                        if (count($constrains) > 0) {
                            $options['constraints'] = $constrains;
                        }
                    }

                    $formSymfonyBuilder->add($field['name'], $field['type'], $options);

                }
            }
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
