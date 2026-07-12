<?php

class Synonyms
{
    public static function all()
    {
        return array(

            'opera' => array(
                'opera',
                'opere',
                'lavoro',
                'lavori',
                'scheda',
                'schede'
            ),

            'autore' => array(
                'autore',
                'autori',
                'artista',
                'artisti',
                'pittore',
                'scultore',
                'creator',
                'artist',
                'author'
            ),

            'titolo' => array(
                'titolo',
                'title',
                'nome opera',
                'denominazione'
            ),

            'anno' => array(
                'anno',
                'data',
                'periodo',
                'epoca',
                'year',
                'date'
            ),

            'tecnica' => array(
                'tecnica',
                'tecniche',
                'medium',
                'technique',
                'realizzata con',
                'eseguita con'
            ),

            'supporto' => array(
                'supporto',
                'supporti',
                'base',
                'su tela',
                'on canvas',
                'support'
            ),

            'materiali' => array(
                'materiale',
                'materiali',
                'materials',
                'material'
            ),

            'soggetto' => array(
                'soggetto',
                'tema',
                'subject',
                'raffigura',
                'rappresenta'
            ),

            'dimensioni_lunghezza' => array(
                'lunghezza',
                'larghezza',
                'width',
                'length'
            ),

            'dimensioni_altezza' => array(
                'altezza',
                'height'
            ),

            'dimensioni_profondita' => array(
                'profondità',
                'profondita',
                'depth'
            ),

            'dimensioni_diametro' => array(
                'diametro',
                'diameter'
            ),
			
			'foto_ufficiale'=>array(
				'foto',
				'foto ufficiale',
				'immagine',
				'immagine ufficiale'
			),

            'peso' => array(
                'peso',
                'weight'
            ),

            'stima' => array(
                'stima',
                'valore stimato',
                'estimate'
            ),

            'valore_assicurativo' => array(
                'valore assicurativo',
                'assicurazione',
                'insurance value'
            ),

            'firma' => array(
                'firma',
                'firmata',
                'firmato',
                'signed',
                'signature'
            ),

            'luogo_produzione_opera' => array(
                'luogo',
                'luogo produzione',
                'realizzata a',
                'prodotta a',
                'place'
            ),

            'geolocalizzazione' => array(
                'ubicazione',
                'collocazione',
                'location',
                'located'
            ),

            'pubblicazioni' => array(
                'pubblicazione',
                'pubblicazioni',
                'pubblicata',
                'pubblicato',
                'published'
            ),

            'esposizione' => array(
                'esposizione',
                'esposizioni',
                'mostra',
                'mostre',
                'esposta',
                'esposto',
                'exhibition',
                'exhibited'
            ),
			
			'numero_archiviazione'=>array(
			'numero archiviazione',
			'numero di archiviazione',
			'codice archiviazione',
			'numero opera'
			),
			
			'tags' => array(
			'tag',
			'tags',
			'etichette',
			'parole chiave',
			'keyword',
			'keywords'
		),
			
			'codice'=>array(
			'numero inventario',
			'numero di inventario',
			'codice inventario',
			'codice opera'
			)			
			

        );
    }
}