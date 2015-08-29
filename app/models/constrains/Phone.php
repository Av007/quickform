<?php

namespace Quickform\Models\Constrains;

use Quickform\Models\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Phone
{
    /** @var Assert\Regex|null $constrain */
    protected $constrain;
    /** @var array $options */
    protected $options = array();
    /** @var Validation $validation */
    protected $validation;
    /** @var bool $jsValidation */
    protected $jsValidation = false;

    /**
     * @param Validation        $validation
     * @param bool              $jsValidation
     * @param \Silex\Translator $translator
     */
    public function __construct($validation, $jsValidation = false, $translator)
    {
        $errorOptions = array('type' => 'integer');

        if ($validation->getMessage()) {
            $errorOptions = array_merge($errorOptions, array('message' => $translator->trans($validation->getMessage())));
        }

        if ($jsValidation) {
            $this->options['attr'] = array(
                'data-mask' => $validation->getValue(),
                'ng-pattern' => '/\d/'
            );
        }

        $this->jsValidation = $jsValidation;
        $this->validation = $validation;
        $this->constrain = new Assert\Type($errorOptions);
    }

    /**
     * @return null|Assert\Type
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
