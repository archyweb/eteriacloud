<?php

require_once __DIR__.'/Token.php';
require_once __DIR__.'/TokenType.php';

class ArchySearchTokenizer
{
    public function tokenize($input)
    {
        $input = trim((string)$input);
        $length = strlen($input);
        $tokens = array();
        $i = 0;

        while ($i < $length) {
            $char = $input[$i];

            if (ctype_space($char)) {
                $i++;
                continue;
            }

            if ($char === '(') {
                $tokens[] = new ArchySearchToken(ArchySearchTokenType::LPAREN, $char);
                $i++;
                continue;
            }

            if ($char === ')') {
                $tokens[] = new ArchySearchToken(ArchySearchTokenType::RPAREN, $char);
                $i++;
                continue;
            }

            if ($char === ':') {
                $tokens[] = new ArchySearchToken(ArchySearchTokenType::COLON, $char);
                $i++;
                continue;
            }

            if ($char === '"' || $char === '“') {
                $quote = $char;
                $endQuote = ($char === '“') ? '”' : '"';
                $i++;
                $value = '';

                while ($i < $length && $input[$i] !== $endQuote) {
                    $value .= $input[$i];
                    $i++;
                }

                if ($i < $length && $input[$i] === $endQuote) {
                    $i++;
                }

                $tokens[] = new ArchySearchToken(ArchySearchTokenType::VALUE, $value);
                continue;
            }

            if (in_array($char, array('>', '<', '!'))) {
                $operator = $char;
                $i++;

                if ($i < $length && $input[$i] === '=') {
                    $operator .= '=';
                    $i++;
                }

                $tokens[] = new ArchySearchToken(ArchySearchTokenType::OPERATOR, $operator);
                continue;
            }

            $word = '';

            while (
                $i < $length &&
                !ctype_space($input[$i]) &&
                !in_array($input[$i], array(':', '(', ')', '>', '<', '!', '"'))
            ) {
                $word .= $input[$i];
                $i++;
            }

            $upper = strtoupper($word);

            if ($upper === 'AND') {
                $tokens[] = new ArchySearchToken(ArchySearchTokenType::AND_TOKEN, $word);
            } elseif ($upper === 'OR') {
                $tokens[] = new ArchySearchToken(ArchySearchTokenType::OR_TOKEN, $word);
            } elseif ($upper === 'NOT') {
                $tokens[] = new ArchySearchToken(ArchySearchTokenType::NOT_TOKEN, $word);
            } else {
                $tokens[] = new ArchySearchToken(ArchySearchTokenType::VALUE, $word);
            }
        }

        return $tokens;
    }
}