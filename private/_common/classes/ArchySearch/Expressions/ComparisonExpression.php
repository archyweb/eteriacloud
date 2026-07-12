<?php

require_once __DIR__ . '/Expression.php';

class ComparisonExpression extends Expression
{
    protected $type = 'comparison';

    protected $operator;
    protected $value;
    protected $raw;

    public function __construct($operator, $value, $raw = '')
    {
        $this->operator = $operator;
        $this->value    = $value;
        $this->raw      = $raw;
    }

    public function operator()
    {
        return $this->operator;
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