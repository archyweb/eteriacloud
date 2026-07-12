<?php

class ValueIndex
{
    protected $values = array();

    public function add($value, $fieldName)
    {
        $key = $this->normalize($value);

        if ($key !== '') {
            $this->values[$key][] = array(
                'field' => $fieldName,
                'value' => $value
            );
        }
    }

    public function findInText($text)
    {
        $found = array();
        $textNorm = $this->normalize($text);

        foreach ($this->values as $key => $items) {
            if ($key !== '' && $this->containsTerm($textNorm, $key)) {
                foreach ($items as $item) {
                    $found[] = $item;
                }
            }
        }

        return $found;
    }

    protected function normalize($value)
    {
        $value = mb_strtolower(trim((string)$value), 'UTF-8');

        $value = str_replace(
            array('à','è','é','ì','ò','ù'),
            array('a','e','e','i','o','u'),
            $value
        );

        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);
        $value = trim($value);

        return $value;
    }
	
	protected function containsTerm($text, $term)
	{
		return preg_match('/(^| )'.preg_quote($term, '/').'($| )/u', $text) === 1;
	}
}