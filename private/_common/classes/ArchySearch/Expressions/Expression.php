<?php

abstract class Expression
{
    /**
     * Tipo dell'espressione.
     *
     * @var string
     */
    protected $type;

    /**
     * Restituisce il tipo.
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Restituisce l'espressione come array.
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Restituisce JSON.
     */
    public function toJson()
    {
        return json_encode(
            $this->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }
}