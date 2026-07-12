<?php

require_once __DIR__ . '/Expression.php';

class NotEmptyExpression extends Expression
{
    protected $type = 'notempty';
    protected $raw;

    public function __construct($raw = 'notempty')
    {
        $this->raw = $raw;
    }

    public function raw()
    {
        return $this->raw;
    }
}