<?php

class Operators
{
    public static function all()
    {
        return array(

            "dal"=>"from",
            "da"=>"from",
            "from"=>"from",

            "al"=>"to",
            "a"=>"to",
            "fino"=>"to",
            "fino a"=>"to",
            "entro"=>"to",
            "to"=>"to",
            "up to"=>"to",

            "tra"=>"between",
            "fra"=>"between",
            "between"=>"between",

            "e"=>"and",
            "and"=>"and",

            "prima"=>"before",
            "before"=>"before",

            "dopo"=>"after",
            "after"=>"after",

            "maggiore"=>">",
            "superiore"=>">",
            "oltre"=>">",
            "sopra"=>">",

            "minore"=>"<",
            "inferiore"=>"<",
            "sotto"=>"<",

            "uguale"=>"=",
            "pari"=>"=",

            "contiene"=>"contains",
            "include"=>"contains",
            "comprende"=>"contains",

            "senza"=>"not",
            "non"=>"not",
            "escluso"=>"not",

            "con"=>"with",
            "presente"=>"present",
            "assente"=>"absent"

        );
    }
}