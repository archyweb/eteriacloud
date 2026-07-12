<?php

require_once __DIR__ . '/Expression.php';

class TokensExpression extends Expression
{
    protected $type = 'tokens';

    protected $values = array();
    protected $raw;

    public function __construct(array $values, $raw = '')
    {
        $this->values = $values;
        $this->raw    = $raw;
    }

    public function values()
    {
        return $this->values;
    }

    public function raw()
    {
        return $this->raw;
    }

    public function count()
    {
        return count($this->values);
    }
}