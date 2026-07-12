<?php

require_once __DIR__ . '/Expression.php';

class ExactExpression extends Expression
{
    protected $type = 'exact';

    protected $value;
    protected $raw;

    public function __construct($value, $raw = '')
    {
        $this->value = $value;
        $this->raw   = $raw;
    }

    public function value()
    {
        return $this->value;
    }

    public function raw()
    {
        return $this->raw;
    }
}