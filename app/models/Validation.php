<?php

namespace Quickform\Models;

class Validation
{
    /** @var string $name */
    protected $name;
    /** @var string $message */
    protected $message;
    /** @var string $key */
    protected $key;
    /** @var string $value */
    protected $value;
    /** @var array $data */
    protected $data;

    /**
     * @param string $key
     * @param string $name
     * @param array  $data
     */
    public function __construct($key, $name, array $data)
    {
        $this->name = $name;
        $this->key = $key;
        $this->message = isset($data['message']) ? $data['message'] : null;
        $this->value = isset($data['value']) ? $data['value'] : null;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
