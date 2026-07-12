<?php

require_once __DIR__.'/ValueParser.php';

class ArchySearchSemanticAnalyzer
{
    protected $schema;

    public function __construct($schema, $db = null)
    {
        $this->schema = $schema;
    }

    public function analyze($input)
    {
        $input = trim((string)$input);
		$input = preg_replace('/\bdall[\'’]anno\b/iu', 'dal', $input);
		$input = preg_replace('/\ball[\'’]anno\b/iu', 'al', $input);
		$input = preg_replace('/\bdell[\'’]anno\b/iu', 'del', $input);

        if ($input === '') {
            return $input;
        }

        $parts = array();

        $singleYear = $this->detectSingleYear($input);
        if ($singleYear) {
            $parts[] = $singleYear;
        }

        $date = $this->detectDate($input);
        if ($date) {
            $parts[] = $date;
        }

        $implicitParts = $this->detectImplicitFields($input);
        foreach ($implicitParts as $part) {
            $parts[] = $part;
        }

        /*if (method_exists($this->schema, 'findValuesInText')) {
            foreach ($this->schema->findValuesInText($input) as $match) {
                if (!isset($match['field']) || !isset($match['value'])) {
                    continue;
                }

                $value = $match['value'];
                $value = strpos($value, ' ') !== false ? '"'.$value.'"' : $value;

                $parts[] = $match['field'].':'.$value;
            }
        }*/

        if (empty($parts)) {
            return $input;
        }

        return implode(' ', array_unique($parts));
    }

    protected function detectSingleYear($input)
    {
        $text = strtolower($input);

        if (preg_match('/(?:del|nel|anno|year)\s+([1-2][0-9]{3})(?![0-9])/u', $text, $m)) {
            return 'anno:'.$m[1];
        }

        return null;
    }


	protected function detectImplicitFields($input)
	{
		$parts = array();
		$matchedFields = array();

		if (!method_exists($this->schema, 'aliases')) {
			return $parts;
		}

		$aliases = $this->schema->aliases();

		uksort($aliases, function($a, $b) {

			$wa = substr_count($a, '_');
			$wb = substr_count($b, '_');

			if ($wa !== $wb) {
				return $wb - $wa;
			}

			return mb_strlen($b, 'UTF-8') - mb_strlen($a, 'UTF-8');
		});

		foreach ($aliases as $alias => $fieldName) {

			if (isset($matchedFields[$fieldName])) {
				continue;
			}

			foreach ($this->aliasVariants($alias) as $variant) {

				$value = $this->extractImplicitValue($input, $variant);

				if ($value === '') {
					continue;
				}

				$parsed = ArchySearchValueParser::parse($value);

				if (!isset($parsed['value']) || $parsed['value'] === '') {
					continue;
				}

				$finalValue = $parsed['value'];

				if ($parsed['type'] === 'text' && strpos($finalValue, ' ') !== false) {
					$finalValue = '"'.$finalValue.'"';
				}

				$parts[] = $fieldName.':'.$finalValue;
				$matchedFields[$fieldName] = true;
				break;
			}
		}

		return array_unique($parts);
	}


    protected function aliasVariants($alias)
    {
        $alias = trim((string)$alias);

        return array_unique(array_filter(array(
            str_replace('_', ' ', $alias),
            $alias
        )));
    }

    protected function extractImplicitValue($input, $alias)
    {
        $alias = trim((string)$alias);

        if ($alias === '') {
            return '';
        }

        $a = preg_quote($alias, '/');
		
		if (preg_match('/(?:senza|manca|mancante)\s+(?:il|lo|la|i|gli|le)?\s*'.preg_quote($alias,'/').'(?:\s|$)/iu',$input)) {
			return 'empty';
		}

		if (preg_match('/(?:con|presente|compilato|valorizzato)\s+(?:il|lo|la|i|gli|le)?\s*'.preg_quote($alias,'/').'(?:\s|$)/iu',$input)) {
			return 'notempty';
		}

        $patterns = array(
            '/(?:^|\s)'.$a.'\s*:\s*([a-zA-ZÀ-ÿ0-9_\-]+)(?=\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+((?:da|dal|from)\s+[0-9]+(?:[.,][0-9]+)?\s+(?:a|al|to)\s+[0-9]+(?:[.,][0-9]+)?)(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+((?:tra|between)\s+[0-9]+(?:[.,][0-9]+)?\s+(?:e|and)\s+[0-9]+(?:[.,][0-9]+)?)(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+([0-9]+(?:[.,][0-9]+)?\s*-\s*[0-9]+(?:[.,][0-9]+)?)(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+((?:da|dal|from)\s+[0-9]+(?:[.,][0-9]+)?)(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+((?:fino\s+a|entro|up\s+to)\s+[0-9]+(?:[.,][0-9]+)?)(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+((?:maggiore|superiore|oltre|minore|inferiore|sotto)\s+(?:di\s+|a\s+)?[0-9]+(?:[.,][0-9]+)?)(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+((?:>=|<=|>|<)\s*[0-9]+(?:[.,][0-9]+)?)(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+([0-9]+(?:[.,][0-9]+)?)(?:\s|$)/iu',
			'/(?:^|\s)'.$a.'\s+((?:empty|vuoto|vuota|assente|mancante))(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+((?:notempty|non\s+vuoto|nonvuoto|presente|compilato|valorizzato))(?:\s|$)/iu',
            '/(?:^|\s)'.$a.'\s+([a-zA-ZÀ-ÿ0-9_\-]+)(?:\s|$)/iu'
        );

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input, $m)) {
		$value = trim($m[1]);

		if (in_array(mb_strtolower($value, 'UTF-8'), array(
			'di',
			'del',
			'della',
			'delle',
			'dei',
			'degli',
			'archiviazione',
			'inventario'
		))) {
			continue;
		}

		return $value;               

			   return trim($m[1]);
            }
        }
		
		

        return '';
    }

    protected function detectDate($input)
    {
        $text = strtolower($input);

        if (preg_match('/(?:dal|da|from)\s+([1-2][0-9]{3})(?![0-9])\s+(?:al|a|to)\s+([1-2][0-9]{3})(?![0-9])/u', $text, $m)) {
            return 'anno:'.$m[1].'-'.$m[2];
        }

        if (preg_match('/(?:tra|between)\s+([1-2][0-9]{3})(?![0-9])\s+(?:e|and)\s+([1-2][0-9]{3})(?![0-9])/u', $text, $m)) {
            return 'anno:'.$m[1].'-'.$m[2];
        }

        if (preg_match('/(?:dal|da|from)\s+([1-2][0-9]{3})(?![0-9])/u', $text, $m)) {
            return 'anno:>='.$m[1];
        }

        if (preg_match('/(?:dopo|after)\s+(?:il\s+)?([1-2][0-9]{3})(?![0-9])/u', $text, $m)) {
            return 'anno:>'.$m[1];
        }

        if (preg_match('/(?:prima|before)\s+(?:del|di|the)?\s*([1-2][0-9]{3})(?![0-9])/u', $text, $m)) {
            return 'anno:<'.$m[1];
        }

        return null;
    }
}