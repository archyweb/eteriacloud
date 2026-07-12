<?php

class ArchySearchToken
{
    protected $type;
    protected $value;

    public function __construct($type, $value = '')
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function type()
    {
        return $this->type;
    }

    public function value()
    {
        return $this->value;
    }

    public function toArray()
    {
        return array(
            'type' => $this->type,
            'value' => $this->value
        );
    }
}