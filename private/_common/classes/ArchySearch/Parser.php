<?php

require_once __DIR__ . '/Query.php';

require_once __DIR__ . '/Expressions/EmptyExpression.php';
require_once __DIR__ . '/Expressions/NotEmptyExpression.php';
require_once __DIR__ . '/Expressions/ComparisonExpression.php';
require_once __DIR__ . '/Expressions/RangeExpression.php';
require_once __DIR__ . '/Expressions/ExactExpression.php';
require_once __DIR__ . '/Expressions/TokensExpression.php';
require_once __DIR__ . '/Expressions/FieldExpression.php';
require_once __DIR__ . '/Lexer/Tokenizer.php';

class ArchySearchParser
{
    protected $rules = array(
		'parseField',
		'parseEmpty',
		'parseNotEmpty',
		'parseExact',
		'parseRange',
		'parseComparison',
		'parseTokens'
	);

    public function parse($search)
    {
        $raw = trim((string)$search);

        $query = new SearchQuery('simple');

        if ($raw === '') {
            return $query;
        }
		
		$fieldExpressions = $this->parseMultipleFields($raw);

		if (!empty($fieldExpressions)) {
			foreach ($fieldExpressions as $expression) {
				$query->add($expression);
			}

			return $query;
		}

        foreach ($this->rules as $rule) {
            $expression = $this->$rule($raw);

            if ($expression instanceof Expression) {
                $query->add($expression);
                return $query;
            }
        }

        return $query;
    }

	protected function parseMultipleFields($raw)
	{
		$tokenizer = new ArchySearchTokenizer();
		$tokens = $tokenizer->tokenize($raw);

		$expressions = array();
		$count = count($tokens);
		$i = 0;

		while ($i < $count) {

			if (
				$i + 2 < $count &&
				$tokens[$i]->type() === ArchySearchTokenType::VALUE &&
				$tokens[$i + 1]->type() === ArchySearchTokenType::COLON
			) {
				$field = trim($tokens[$i]->value());
				$valueParts = array();

				$j = $i + 2;

				while ($j < $count) {

					if (
						$j + 1 < $count &&
						$tokens[$j]->type() === ArchySearchTokenType::VALUE &&
						$tokens[$j + 1]->type() === ArchySearchTokenType::COLON
					) {
						break;
					}

					$valueParts[] = $tokens[$j]->value();
					$j++;
				}

				$value = trim(implode(' ', $valueParts));

				if ($field !== '' && $value !== '') {
					$expression = $this->parseField($field.':'.$value);

					if ($expression instanceof Expression) {
						$expressions[] = $expression;
					}
				}

				$i = $j;
				continue;
			}

			$i++;
		}

		return $expressions;
	}

    protected function parseField($raw)
    {
        if (!preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\s*:\s*(.+)$/u', trim($raw), $matches)) {
            return false;
        }

        $field = strtolower(trim($matches[1]));
        $value = trim($matches[2]);

        if ($field === '' || $value === '') {
            return false;
        }

        $rules = array(
            'parseEmpty',
            'parseNotEmpty',
            'parseExact',
            'parseRange',
            'parseComparison',
            'parseTokens'
        );

        foreach ($rules as $rule) {
            $expression = $this->$rule($value);

            if ($expression instanceof Expression) {
                return new FieldExpression($field, $expression, $raw);
            }
        }

        return false;
    }

    protected function parseEmpty($raw)
    {
        $clean = strtolower(trim($raw));

        if ($clean === 'empty' || $clean === '[empty]' || $clean === 'null') {
            return new EmptyExpression($raw);
        }

        return false;
    }

    protected function parseNotEmpty($raw)
    {
        $clean = strtolower(trim($raw));

        if ($clean === 'notempty') {
            return new NotEmptyExpression($raw);
        }

        return false;
    }

    protected function parseExact($raw)
    {
        if (preg_match('/^["“](.+)["”]$/u', trim($raw), $matches)) {
            return new ExactExpression(trim($matches[1]), $raw);
        }

        return false;
    }

    protected function parseRange($raw)
	{
		$clean = strtolower(trim($raw));

		if (preg_match('/^(?:da|dal|from)\s+([0-9]+(?:[.,][0-9]+)?)\s+(?:a|al|to)\s+([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $matches)) {
			return new RangeExpression(
				$this->normalizeNumber($matches[1]),
				$this->normalizeNumber($matches[2]),
				$raw
			);
		}

		if (preg_match('/^([0-9]+(?:[.,][0-9]+)?)\s*-\s*([0-9]+(?:[.,][0-9]+)?)$/', $clean, $matches)) {
			return new RangeExpression(
				$this->normalizeNumber($matches[1]),
				$this->normalizeNumber($matches[2]),
				$raw
			);
		}

		return false;
	}

    protected function parseComparison($raw)
    {
        $clean = strtolower(trim($raw));

        if (preg_match('/^(>=|<=|>|<)\s*([0-9]+(?:[.,][0-9]+)?)$/', $clean, $matches)) {
            return new ComparisonExpression(
                $matches[1],
                $this->normalizeNumber($matches[2]),
                $raw
            );
        }

        return false;
    }

    protected function parseTokens($raw)
    {
        $tokens = preg_split('/\s+/', trim($raw));
        $tokens = array_values(array_filter(array_map('trim', $tokens)));

        if (empty($tokens)) {
            return false;
        }

        return new TokensExpression($tokens, $raw);
    }

    protected function normalizeNumber($value)
    {
        $value = str_replace(',', '.', $value);

        return strpos($value, '.') !== false
            ? (float)$value
            : (int)$value;
    }
}