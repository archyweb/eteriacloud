<?php

class AliasDictionary
{
    public static function forIntent($intentName)
    {
        if ($intentName === 'archy_art_catalog' || $intentName === 'datatable') {
            return self::catalog();
        }

        return array();
    }

    protected static function catalog()
    {
        return array(
            'data' => array(
                'anno',
                'year',
                'date',
                'datazione',
                'periodo',
                'period'
            ),

            'titolo_opera' => array(
                'titolo',
                'title',
                'titolo opera',
                'artwork title',
                'work title',
                'opera',
                'work',
                'artwork'
            ),

            'nominativo_autore' => array(
                'autore',
                'artista',
                'author',
                'artist',
                'creator',
                'autore opera'
            ),

            'tecnica' => array(
                'tecnica',
                'tecnica esecutiva',
                'technique',
                'medium',
                'execution technique'
            ),

            'supporto' => array(
                'supporto',
                'support',
                'surface',
                'base',
                'medium support'
            ),

            'materiali' => array(
                'materiali',
                'materiale',
                'materials',
                'material'
            ),

            'numero_archiviazione' => array(
                'numero archiviazione',
                'numero',
                'codice',
                'archive number',
                'catalogue number',
                'catalog number'
            ),

            'codice' => array(
                'inventario',
                'numero inventario',
                'inventory',
                'inventory number'
            ),

            'stato_conservazione' => array(
                'stato conservazione',
                'conservazione',
                'condition',
                'conservation state'
            ),

            'esposizione' => array(
                'esposizione',
                'esposizioni',
                'mostra',
                'mostre',
                'exhibition',
                'exhibitions'
            ),

            'pubblicazioni' => array(
                'pubblicazione',
                'pubblicazioni',
                'bibliografia',
                'publication',
                'publications',
                'bibliography'
            ),

            'provenienza_note' => array(
                'provenienza',
                'provenance'
            ),

            'collocazione_geografica' => array(
                'collocazione',
                'ubicazione',
                'location',
                'placement'
            ),

            'dimensioni_altezza' => array(
                'altezza',
                'height'
            ),

            'dimensioni_lunghezza' => array(
                'larghezza',
                'lunghezza',
                'width',
                'length'
            ),

            'dimensioni_profondita' => array(
                'profondita',
                'profondità',
                'depth'
            ),

            'peso' => array(
                'peso',
                'weight'
            ),

            'firma' => array(
                'firma',
                'signed',
                'signature'
            )
        );
    }
}