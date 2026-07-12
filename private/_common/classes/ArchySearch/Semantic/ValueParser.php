<?php

class ArchySearchValueParser
{
    public static function parse($value)
    {
        $raw = trim((string)$value);
        $clean = mb_strtolower($raw, 'UTF-8');

        if ($raw === '') {
            return array('type' => 'empty', 'value' => '');
        }
		
		if (in_array($clean, array('empty', 'vuoto', 'vuota', 'assente', 'mancante'))) {
			return array('type' => 'empty', 'value' => 'empty');
		}

		if (in_array($clean, array('notempty', 'nonvuoto', 'non vuoto', 'presente', 'compilato', 'valorizzato'))) {
			return array('type' => 'notempty', 'value' => 'notempty');
		}

        if (preg_match('/^(?:da|dal|from)\s+([0-9]+(?:[.,][0-9]+)?)\s+(?:a|al|to)\s+([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $m)) {
            return array('type' => 'range', 'value' => self::num($m[1]).'-'.self::num($m[2]));
        }

        if (preg_match('/^(?:tra|between)\s+([0-9]+(?:[.,][0-9]+)?)\s+(?:e|and)\s+([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $m)) {
            return array('type' => 'range', 'value' => self::num($m[1]).'-'.self::num($m[2]));
        }

        if (preg_match('/^([0-9]+(?:[.,][0-9]+)?)\s*-\s*([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $m)) {
            return array('type' => 'range', 'value' => self::num($m[1]).'-'.self::num($m[2]));
        }

        if (preg_match('/^(?:da|dal|from)\s+([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $m)) {
            return array('type' => 'comparison', 'value' => '>='.self::num($m[1]));
        }
		
		if (preg_match('/^(?:fino\s+a|entro|up\s+to)\s+([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $m)) {
			return array('type' => 'comparison', 'value' => '<='.self::num($m[1]));
		}

        if (preg_match('/^(>=|<=|>|<)\s*([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $m)) {
            return array('type' => 'comparison', 'value' => $m[1].self::num($m[2]));
        }

        if (preg_match('/^(?:maggiore|superiore|oltre|after)\s+(?:di|a|than)?\s*([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $m)) {
            return array('type' => 'comparison', 'value' => '>'.self::num($m[1]));
        }

        if (preg_match('/^(?:minore|inferiore|sotto|before)\s+(?:di|a|than)?\s*([0-9]+(?:[.,][0-9]+)?)$/u', $clean, $m)) {
            return array('type' => 'comparison', 'value' => '<'.self::num($m[1]));
        }

        if (preg_match('/^[0-9]+(?:[.,][0-9]+)?$/u', $clean)) {
            return array('type' => 'number', 'value' => self::num($clean));
        }

        return array('type' => 'text', 'value' => $raw);
    }

    protected static function num($value)
    {
        return str_replace(',', '.', trim((string)$value));
    }
}