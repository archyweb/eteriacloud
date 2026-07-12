<?php

require_once __DIR__.'/Field.php';

class FieldCollection implements IteratorAggregate, Countable
{
    protected $fields = array();

    public function add(Field $field)
    {
        $this->fields[$field->name()] = $field;

        return $this;
    }

    public function get($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    public function all()
    {
        return $this->fields;
    }

    public function count(): int
    {
        return count($this->fields);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->fields);
    }

    public function findByType($type)
    {
        return array_filter($this->fields, function(Field $field) use ($type) {
            return $field->type() === $type;
        });
    }

    public function findBySearchType($searchType)
    {
        return array_filter($this->fields, function(Field $field) use ($searchType) {
            return $field->searchType() === $searchType;
        });
    }
}