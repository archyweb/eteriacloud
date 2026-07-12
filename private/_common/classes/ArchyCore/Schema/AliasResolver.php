<?php

class AliasResolver
{
    protected $aliases = array();

    public function register($alias, $fieldName)
    {
        $key = $this->normalize($alias);

        if ($key !== '') {
            $this->aliases[$key] = $fieldName;
        }

        return $this;
    }

    public function resolve($alias)
    {
        $key = $this->normalize($alias);

        return isset($this->aliases[$key])
            ? $this->aliases[$key]
            : null;
    }

    public function all()
    {
        return $this->aliases;
    }

    protected function normalize($value)
    {
        $value = strtolower(trim((string)$value));

        $value = str_replace(
            array('à','è','é','ì','ò','ù'),
            array('a','e','e','i','o','u'),
            $value
        );

        $value = preg_replace('/[^a-z0-9]+/u', '_', $value);
        $value = trim($value, '_');

        return $value;
    }
}