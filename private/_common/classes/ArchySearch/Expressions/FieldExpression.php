<?php

require_once __DIR__ . '/Expression.php';

class FieldExpression extends Expression
{
    protected $type = 'field';
    protected $field;
    protected $expression;
    protected $raw;

    public function __construct($field, Expression $expression, $raw = '')
    {
        $this->field = $field;
        $this->expression = $expression;
        $this->raw = $raw;
    }

    public function field()
    {
        return $this->field;
    }

    public function expression()
    {
        return $this->expression;
    }

    public function raw()
    {
        return $this->raw;
    }

    public function toArray()
    {
        return array(
            'type' => $this->type,
            'field' => $this->field,
            'expression' => $this->expression->toArray(),
            'raw' => $this->raw
        );
    }
}