<?php

namespace Quickform\Models\Constrains;

use Quickform\Models\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Max
{
    /** @var Assert\Length|null $constrain */
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
        $errorOptions = array('max' => $validation->getValue());

        if ($validation->getMessage()) {
            $errorOptions = array_merge($errorOptions, array('maxMessage' => $translator->trans($validation->getMessage())));
        }

        if ($jsValidation) {
            $this->options['attr'] = array();
            $this->options['attr']['ng-maxlength'] = $validation->getValue();
        }

        $this->jsValidation = $jsValidation;
        $this->validation = $validation;
        $this->constrain = new Assert\Length($errorOptions);
    }

    /**
     * @return null|Assert\Length
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
            'message'    => str_replace('{{ limit }}', $this->validation->getValue(), $this->constrain->maxMessage),
            'validation' => $this->validation->getKey(),
        );
    }
}
