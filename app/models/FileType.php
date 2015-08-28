<?php

namespace Quickform\Models;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FileType extends AbstractType
{
    /** @var array $defaults */
    protected $defaults = array();

    public function __construct($defaults = array())
    {
        $this->defaults = $defaults;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array(
            'multiple'    => true,
            'constraints' => array(
                new Assert\All(
                    array('constraints' => new Assert\File(array(
                            'maxSize'   => $this->defaults['maxSize'],
                            'mimeTypes' => $this->getMimeTypes($this->defaults['mimeTypes'])
                        ))
                    )
                )
            )
        ));
    }

    /**
     * @param array $mimeTypes
     * @return array
     * @throws \Exception
     */
    protected function getMimeTypes($mimeTypes = array())
    {
        $rule = array(
            'jpg'  => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'png'  => 'image/png',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'rtf'  => 'text/rtf',
            'xls'  => 'application/excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf'  => 'application/pdf'
        );
        $results = array();
        foreach (explode(';', $mimeTypes) as $mimeType) {
            if (isset($rule[$mimeType])) {
                $results[] = $rule[$mimeType];
            } else {
                throw new \Exception('Undefined mimeType');
            }
        }

        return $results;
    }

    public function getName()
    {
        return 'file';
    }
}
