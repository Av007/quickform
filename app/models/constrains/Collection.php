<?php

namespace Quickform\Models\Constrains;

use Quickform\Models\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Collection
{
    /** @var Assert\NotBlank|null $constrain */
    protected $constrain;
    /** @var array $options */
    protected $options = array();
    /** @var Validation $validation */
    protected $validation;
    /** @var bool $jsValidation */
    protected $jsValidation = false;

    /**
     * @param Validation $validation
     * @param bool       $jsValidation
     */
    public function __construct($validation, $jsValidation = false)
    {
        $this->validation = $validation;

        $this->constrain = new Assert\Count(array(
            'max' => 4
        ));

        $this->options = array_merge($this->options, array('required' => $this->validation->getValue()));
    }

    /**
     * @return null|Assert\File
     */
    public function getConstrains()
    {
        return $this->constrain;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getJsValidation()
    {
        if (!$this->jsValidation) {
            return array();
        }

        return array(
            'field'      => $this->validation->getName(),
            'message'    => $this->constrain->message,
            'validation' => $this->validation->getKey(),
        );
    }
}
