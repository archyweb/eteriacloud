<?php

require_once __DIR__ . '/Parser.php';
require_once __DIR__ . '/Query.php';

class ArchySearchEngine
{
    public static function parse($search)
    {
        $parser = new ArchySearchParser();

        return $parser->parse($search);
    }

    public static function search($search, array $context = array())
    {
        /*
        |--------------------------------------------------------------------------
        | Archy Search Engine
        |--------------------------------------------------------------------------
        |
        | Per ora search() restituisce solo la query parsata.
        | Nelle prossime versioni farà:
        |
        | Parser -> Query -> Planner -> Builder -> Provider -> Results
        |
        */

        return self::parse($search);
    }
}