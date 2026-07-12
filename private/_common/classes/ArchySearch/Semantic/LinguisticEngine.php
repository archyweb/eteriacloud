<?php

require_once __DIR__.'/Dictionaries/StopWords.php';
require_once __DIR__.'/Dictionaries/EntityWords.php';
require_once __DIR__.'/Dictionaries/Operators.php';

class LinguisticEngine
{
    public static function normalize($input)
    {
        $text = mb_strtolower(trim((string)$input), 'UTF-8');
        $tokens = preg_split('/\s+/u', $text);
        $tokens = array_filter(array_map('trim', $tokens));

        $remove = array_merge(
			StopWords::all(),
			EntityWords::all(),
			array_keys(Operators::all())
		);

        $remove = array_flip($remove);

        $out = array();

        foreach ($tokens as $token) {
            $clean = trim($token, " \t\n\r\0\x0B.,;:!?()[]{}");

            if ($clean === '') {
                continue;
            }

            if (isset($remove[$clean])) {
                continue;
            }

            $out[] = $clean;
        }

        return implode(' ', $out);
    }
}