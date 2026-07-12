<?php

require_once __DIR__ . '/Expressions/Expression.php';

class SearchQuery
{
    protected $intent;
    protected $expressions = array();

    public function __construct($intent = 'simple', array $expressions = array())
    {
        $this->intent = $intent;

        foreach ($expressions as $expression) {
            $this->add($expression);
        }
    }

    public function intent()
    {
        return $this->intent;
    }

    public function setIntent($intent)
    {
        $this->intent = $intent;
        return $this;
    }

    public function add($expression)
    {
        if ($expression instanceof Expression) {
            $this->expressions[] = $expression;
        }

        return $this;
    }

    public function expressions()
    {
        return $this->expressions;
    }

    public function all()
    {
        return $this->expressions();
    }

    public function first()
    {
        return isset($this->expressions[0]) ? $this->expressions[0] : null;
    }

    public function last()
    {
        if (empty($this->expressions)) {
            return null;
        }

        return $this->expressions[count($this->expressions) - 1];
    }

    public function count()
    {
        return count($this->expressions);
    }

    public function isEmpty()
    {
        return $this->count() === 0;
    }

    public function isSimple()
    {
        return $this->intent === 'simple';
    }

    public function isCompound()
    {
        return $this->intent === 'compound';
    }

    public function toArray()
    {
        return array(
            'intent' => $this->intent,
            'expressions' => array_map(function ($expression) {
                return $expression->toArray();
            }, $this->expressions)
        );
    }

    public function toJson()
    {
        return json_encode(
            $this->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }
	
	public static function parse($input)
	{
		require_once __DIR__ . '/Parser.php';

		$parser = new ArchySearchParser();

		return $parser->parse($input);
	}
}