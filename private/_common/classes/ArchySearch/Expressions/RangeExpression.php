<?php

require_once __DIR__ . '/Expression.php';

class RangeExpression extends Expression
{
    protected $type = 'range';

    protected $from;
    protected $to;
    protected $raw;

    public function __construct($from, $to, $raw = '')
    {
        $this->from = $from;
        $this->to   = $to;
        $this->raw  = $raw;
    }

    public function from()
    {
        return $this->from;
    }

    public function to()
    {
        return $this->to;
    }

    public function raw()
    {
        return $this->raw;
    }
}