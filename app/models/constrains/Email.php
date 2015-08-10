<?php

namespace Quickform\Models\Constrains;

use Quickform\Models\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Email
{
    /** @var Assert\Email|null $constrain */
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
        $errorOptions = array();

        if ($validation->getMessage()) {
            $errorOptions = array_merge($errorOptions, array('message' => $validation->getMessage()));
        }

        $this->jsValidation = $jsValidation;
        $this->validation = $validation;
        $this->constrain = new Assert\Email($errorOptions);
    }

    /**
     * @return null|Assert\Email
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
