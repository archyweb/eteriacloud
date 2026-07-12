<?php

require_once __DIR__ . '/Expression.php';

class EmptyExpression extends Expression
{
    protected $type = 'empty';
    protected $raw;

    public function __construct($raw = 'empty')
    {
        $this->raw = $raw;
    }

    public function raw()
    {
        return $this->raw;
    }
}