<?php $form = $data['hidden_columns']; ?>
    
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="catalog-tab" data-bs-toggle="tab" href="#tab-catalog" role="tab" aria-controls="tab-catalog" aria-selected="true">Dati Scheda Catalogo</a></li>
    <li class="nav-item"><a class="nav-link" id="final-report-tab" data-bs-toggle="tab" href="#tab-final-report" role="tab" aria-controls="tab-final-report" aria-selected="false">Modello di stampa</a></li>
</ul>
<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-catalog" role="tabpanel" aria-labelledby="catalog-tab">
        <div class="accordion mt-2" id="catalog_accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_1" aria-expanded="true" aria-controls="catalog_accordion_1">CODICI IDENTIFICATIVI</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_1" aria-labelledby="heading_catalog_accordion_1" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <?php
                                echo $app::form_element($form['archy_art_catalog.codice'], "col-4");

                                echo $app::form_element($form['archy_art_catalog.serie'], "col-4");
                                echo $app::form_element($form['archy_art_catalog.collezione'], "col-4");

                                echo $app::form_element($form['archy_art_catalog.numero_elementi'], "col-4");

                                echo $app::form_element($form['archy_art_catalog.presente_in_archivio'], "col-4");
                                echo $app::form_element($form['archy_art_catalog.collocazione_geografica'], "col-4");
                                echo $app::form_element($form['archy_art_catalog.precedenti_archiviazioni'], "col-4");
                                echo $app::form_element($form['archy_art_catalog.certificato_archiviazione'], "col-4");
                                echo $app::form_element($form['archy_art_catalog.riferimento_catalogo_ragionato'], "col-4");
                                echo $app::form_element($form['archy_art_catalog.note_libere'], "col-4");
								echo $app::form_element($form['archy_art_catalog.tags'], "col-4");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_2" aria-expanded="true" aria-controls="catalog_accordion_2">DATI IDENTIFICATIVI</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_2" aria-labelledby="heading_catalog_accordion_2" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <?php
                                    echo $app::form_element($form['archy_art_catalog.nominativo_autore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_nascita_autore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.luogo_nascita_autore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_morte_autore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.luogo_morte_autore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.autori'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.collaboratori'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.titolo_opera'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.altri_titoli'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.courtesy'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.didascalia_courtesy'], "col-4");

                                    echo $app::form_element($form['archy_art_catalog.didascalia_foto'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.tipologia_opera'], "col-4");

                                    echo $app::form_element($form['archy_art_catalog.tipologia_documenti'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.tipologia_fonte_bibliografica'], "col-4");
                              
									
									 // --- SOTTOCAMPI TIPLOGIA OPERA (ordinati) ---
                                    // Assemblaggio
                                    echo $app::form_element($form['archy_art_catalog.parti_mobili'], "col-4");
                                    // Bozza
                                    echo $app::form_element($form['archy_art_catalog.tipo_bozza'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.opera_finita_id'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.stato_bozza'], "col-4");
                                    // Collage
                                    echo $app::form_element($form['archy_art_catalog.protezione'], "col-4");
                                    // Computer Art
                                    echo $app::form_element($form['archy_art_catalog.sottotipo_computer_art'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.formato_file'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.edizione'], "col-4");
                                    // Corrispondenza
                                    echo $app::form_element($form['archy_art_catalog.tipo_corrispondenza'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.mittente_destinatario'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.numero_pagine'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.originale_copia'], "col-4");
                                    // Dipinto
                                    echo $app::form_element($form['archy_art_catalog.cornice'], "col-4");
                                    // Dipinto murale
                                    echo $app::form_element($form['archy_art_catalog.luogo_permanente_murale'], "col-4");
                                    // Disegno
                                    echo $app::form_element($form['archy_art_catalog.colore_disegno'], "col-4");
                                    // Film
                                    echo $app::form_element($form['archy_art_catalog.durata'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.audio_presente'], "col-4");
                                    // Fotogramma filmico
                                    echo $app::form_element($form['archy_art_catalog.film_collegato_id'], "col-4");
                                    // Fotografia
                                    echo $app::form_element($form['archy_art_catalog.tecnica_fotografica'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_di_scatto'], "col-md-6");
                                    echo $app::form_element($form['archy_art_catalog.data_di_stampa'], "col-md-6");
                                    echo $app::form_element($form['archy_art_catalog.tipo_stampa_fotografia'], "col-4");
                                    // Frottage
                                    echo $app::form_element($form['archy_art_catalog.superficie_origine'], "col-4");
                                    // Installazione
                                    echo $app::form_element($form['archy_art_catalog.numero_componenti_installazione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.dimensioni'], "col-4");
                                    // Installazione ambientale
                                    echo $app::form_element($form['archy_art_catalog.spazio_minimo_richiesto'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.come_installazione'], "col-4");
                                    // Installazione audio
                                    echo $app::form_element($form['archy_art_catalog.canali'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.durata_loop'], "col-4");
                                    // Installazione video
                                    echo $app::form_element($form['archy_art_catalog.numero_schermi_proiezioni'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.risoluzione'], "col-4");
                                    // Installazione multimediale
                                    echo $app::form_element($form['archy_art_catalog.media_presenti'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.hardware_richiesto'], "col-4");
                                    // Installazione o Opera interattiva
                                    echo $app::form_element($form['archy_art_catalog.tipo_interazione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.necessita_rete'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.software_hardware_richiesto'], "col-4");
                                    // Libro
                                    //echo $app::form_element($form['archy_art_catalog.editore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_anno'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.isbn'], "col-4");
                                    // Libro d’artista
                                    echo $app::form_element($form['archy_art_catalog.unico_originale'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.rilegatura'], "col-4");
                                    // Mobile
                                    echo $app::form_element($form['archy_art_catalog.tipo_di_oggetto'], "col-4");
                                    //echo $app::form_element($form['archy_art_catalog.materiali'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.funzionale'], "col-4");
                                    // Multiplo
                                    echo $app::form_element($form['archy_art_catalog.tipo_multiplo'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.prove_multiplo'], "col-4");
                                    // Oggetto di design
                                    echo $app::form_element($form['archy_art_catalog.categoria_design'], "col-4");
                                    // Opera cinetico-visuale
                                    echo $app::form_element($form['archy_art_catalog.movimento_cinetico'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.alimentazione'], "col-4");
                                    // Performance
                                    echo $app::form_element($form['archy_art_catalog.data_luogo_prima_esecuzione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.ripetibile'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.documentazione_multi'], "col-4");
                                    // Progetto
                                    echo $app::form_element($form['archy_art_catalog.tipo_progetto'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.stato_progetto'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.output'], "col-4");
                                    // Realtà virtuale
                                    echo $app::form_element($form['archy_art_catalog.piattaforma'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.software'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.build_versione'], "col-4");
                                    // Ready made
                                    echo $app::form_element($form['archy_art_catalog.oggetto_originario'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.modifiche_ready_made'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.provenienza_note'], "col-4");
                                    // Scultura
                                    echo $app::form_element($form['archy_art_catalog.base_scultura'], "col-4");
                                    // Rivista
                                    echo $app::form_element($form['archy_art_catalog.titolo_periodico'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.numerazione_periodico'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.issn'], "col-4");
                                    // Stampa
                                    echo $app::form_element($form['archy_art_catalog.tecnica_stampa'], "col-4");
                                    // Audio
                                    //echo $app::form_element($form['archy_art_catalog.supporto'], "col-4");
                                    // Video arte
                                    echo $app::form_element($form['archy_art_catalog.modalita_video'], "col-4");
                                    // Calco
                                    echo $app::form_element($form['archy_art_catalog.derivazione'], "col-4");
                                    // Lastra di incisione
                                    echo $app::form_element($form['archy_art_catalog.stato_lastra_incisione'], "col-4");
                                    // Maschera
                                    echo $app::form_element($form['archy_art_catalog.materiale_maschera'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.indossabile'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.uso_maschera'], "col-4");
                                    // Modello 3D / Scultura digitale
                                    echo $app::form_element($form['archy_art_catalog.formato_3d'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.texture_presente'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.rig_presente'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.animazione_presente'], "col-4");
                                    // Render 3D (immagine)
                                    echo $app::form_element($form['archy_art_catalog.motore_render'], "col-4");
                                    // Animazione 3D
                                    echo $app::form_element($form['archy_art_catalog.fps'], "col-4");
                                    // Opera generativa (coding art)
                                    echo $app::form_element($form['archy_art_catalog.ambiente_generativo'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.variabile_generativo'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.seed_parametri'], "col-4");
                                    // AI Art
                                    echo $app::form_element($form['archy_art_catalog.tipo_ai'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.modello_ai'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.post_produzione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.software_post_produzione'], "col-4");
                                    // Realtà aumentata (AR)
                                    echo $app::form_element($form['archy_art_catalog.marker'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.file_ar'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.necessita_app'], "col-4");
                                    // Asset digitale / Oggetto virtuale
                                    echo $app::form_element($form['archy_art_catalog.tipo_asset'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.licenza_asset'], "col-4");
                                    // NFT / Crypto-art
                                    echo $app::form_element($form['archy_art_catalog.blockchain'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.contract_address'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.token_id'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.piattaforma_mint'], "col-4");
                                    // Net Art / Web-based artwork
                                    echo $app::form_element($form['archy_art_catalog.url_opera'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.tecnologia_web'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.hosting'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.versione_dipendenze'], "col-4");
                                    // Sound Art
                                    echo $app::form_element($form['archy_art_catalog.setup_sound_art'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.spazio_richiesto_sound_art'], "col-4");
                                    // --- fine sottocampi tipologia opera ---
                                    
                                    echo $app::form_element($form['archy_art_catalog.stato_stampa'], "col-4");
									
									
									// campi doppi commentati
                                    //echo $app::form_element($form['archy_art_catalog.tecnica_fotografica'], "col-4");
                                    //echo $app::form_element($form['archy_art_catalog.data_di_scatto'], "col-md-6");
                                    //echo $app::form_element($form['archy_art_catalog.data_di_stampa'], "col-md-6");
                                    echo $app::form_element($form['archy_art_catalog.titolo_volume'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.numero_volume'], "col-4");
                                    //echo $app::form_element($form['archy_art_catalog.titolo_periodico'], "col-4");
                                    //echo $app::form_element($form['archy_art_catalog.numerazione_periodico'], "col-4");
                                    /*echo $app::form_element($form['archy_art_catalog.titolo_mostra'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.curatore_mostra'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.sede_mostra'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.citta_mostra'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.stato_mostra'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.date_mostra'], "col-4");*/
									echo $app::form_element($form['archy_art_catalog.altra_tipologia'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.descrizione_fonte_bibliografica'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_specifica'], "col-4");

                                    echo $app::form_element($form['archy_art_catalog.data_pubblicazione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_inizio_realizzazione'], "col-md-6");
                                    echo $app::form_element($form['archy_art_catalog.data_fine_realizzazione'], "col-md-6");
                                    echo $app::form_element($form['archy_art_catalog.ulteriori_datazioni'], "col-4");

                                    echo $app::form_element($form['archy_art_catalog.cinquanta_anni'], "col-md-4");
                                    echo $app::form_element($form['archy_art_catalog.settanta_anni'], "col-md-4");
                                    echo $app::form_element($form['archy_art_catalog.luogo_produzione_opera'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.geolocalizzazione'], "col-4");
                                    //echo $app::form_element($form['archy_art_catalog.edizione'],"col-4");

                                    echo $app::form_element($form['archy_art_catalog.opera_edizione'], "col-4");

                                    echo $app::form_element($form['archy_art_catalog.numero_edizione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.editore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.stampatore'], "col-4");
                                    // echo $app::form_element($form['archy_art_catalog.riferimento_archivio_artista'],"col-4");
                                    echo $app::form_element($form['archy_art_catalog.foto_ufficiale'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.foto_ufficiale_secondaria'], "col-4");
                                    // echo $app::form_element($form['archy_art_catalog.crediti_foto'],"col-4");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_3">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_3" aria-expanded="true" aria-controls="catalog_accordion_3">COMMITTENZA</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_3" aria-labelledby="heading_catalog_accordion_3" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <?php
                                    echo $app::form_element($form['archy_art_catalog.committenza'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.finanziatore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.curatore'], "col-4");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_4">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_4" aria-expanded="true" aria-controls="catalog_accordion_4">DESCRIZIONE</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_4" aria-labelledby="heading_catalog_accordion_4" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <?php
                                    echo $app::form_element($form['archy_art_catalog.tipo_di_oggetto'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.supporto'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.materiali'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.tecnica'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.unita_di_misura_altezza'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.dimensioni_altezza'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.dimensioni_lunghezza'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.dimensioni_profondita'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.dimensioni_spessore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.dimensioni_ingombro'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.dimensioni_diametro'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.altre_misure'], "col-4");

                                    //echo $app::form_element($form['archy_art_catalog.numero_pagine'], "col-4");

                                    //echo $app::form_element($form['archy_art_catalog.durata'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.peso'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.firma'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.posizione_firma'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.segni_particolari'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.descrizione_retro'],"col-4");
                                    echo $app::form_element($form['archy_art_catalog.descrizione_narrativa'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.indicazioni_specifiche_contestuali'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.site_specific'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.time_specific'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.altre_condizioni'], "col-4");
                                    
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_5">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_5" aria-expanded="true" aria-controls="catalog_accordion_5">CONSERVAZIONE, ALLESTIMENTO, ESPOSIZIONE E MOVIMENTAZIONE</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_5" aria-labelledby="heading_catalog_accordion_5" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <?php
                                    echo $app::form_element($form['archy_art_catalog.stato_conservazione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.interventi_restauri'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.diagnostica'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.indicazioni_conservazione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.indicazioni_allestimento'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.indicazioni_imballaggio'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.ingombro_imballo'], "col-4");
									echo $app::form_element($form['archy_art_catalog.istruzioni_allestimento'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.indicazioni_condizioni_ambientali'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.sicurezza_spazi_espositivi'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.sicurezza_depositi'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.contatti'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.link_intervista'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.file_fotografico'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.condition_report'], "col-4");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_6">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_6" aria-expanded="true" aria-controls="catalog_accordion_6">PROVENIENZA, CONDIZIONE GIURIDICA, CIRCOLAZIONE E STIMA</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_6" aria-labelledby="heading_catalog_accordion_6" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <?php
                                    echo $app::form_element($form['archy_art_catalog.titolarita_attuale'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.storico_provenienza'], "col-4");
                                    // echo $app::form_element($form['archy_art_catalog.indicazione_aste'],"col-4");
                                    echo $app::form_element($form['archy_art_catalog.tipologia_proprieta'],"col-4");

                                    echo $app::form_element($form['archy_art_catalog.titolo_proprieta'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_acquisizione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.licenze_attestati_circolazione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.stima'], "col-md-6");
                                    echo $app::form_element($form['archy_art_catalog.valuta'], "col-md-3");
                                    echo $app::form_element($form['archy_art_catalog.data_di_stima'], "col-md-3");

                                    echo $app::form_element($form['archy_art_catalog.valore_assicurativo'], "col-md-6");

                                    echo $app::form_element($form['archy_art_catalog.certificato_assicurativo'], "col-md-6");

                                    echo $app::form_element($form['archy_art_catalog.ulteriori_informazioni'], "col-4");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_7">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_7" aria-expanded="true" aria-controls="catalog_accordion_7">DIRITTI DI UTILIZZAZIONE ECONOMICA</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_7" aria-labelledby="heading_catalog_accordion_7" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                             <?php
                                    echo $app::form_element($form['archy_art_catalog.diritti_immagine'], "col-4");

                                    echo $app::form_element($form['archy_art_catalog.diritti_patrimoniali'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.ulteriori_diritti'], "col-4");

                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_8">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_8" aria-expanded="true" aria-controls="catalog_accordion_8">FONTI E DOCUMENTI DI RIFERIMENTO</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_8" aria-labelledby="heading_catalog_accordion_8" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                             <?php
                                    echo $app::form_element($form['archy_art_catalog.documentazione_fotografica'], "col-6");
                                    echo $app::form_element($form['archy_art_catalog.descrizione_documentazione_fotografica'], "col-6");
                                    echo $app::form_element($form['archy_art_catalog.documentazione_grafica'], "col-6");
                                    echo $app::form_element($form['archy_art_catalog.descrizione_documentazione_grafica'], "col-6");
                                    echo $app::form_element($form['archy_art_catalog.documentazione_video'], "col-6");
                                    echo $app::form_element($form['archy_art_catalog.descrizione_documentazione_video'], "col-6");
                                    echo $app::form_element($form['archy_art_catalog.documentazione_audio'], "col-6");
                                    echo $app::form_element($form['archy_art_catalog.descrizione_documentazione_audio'], "col-6");
                                    echo $app::form_element($form['archy_art_catalog.fonti_documenti'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.pubblicazioni'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.bibliografia'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.esposizione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.storia_espositiva'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.aste'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.notizie_storiche'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.intervista'], "col-4");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_9">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_9" aria-expanded="true" aria-controls="catalog_accordion_9">COLLEGAMENTI</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_9" aria-labelledby="heading_catalog_accordion_9" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                             <?php
                                    echo $app::form_element($form['archy_art_catalog.documenti_progettuali'], "col-4");
                                    // echo $app::form_element($form['archy_art_catalog.documenti_interni'],"col-4");
                                    echo $app::form_element($form['archy_art_catalog.link_esterni'], "col-4");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_catalog_accordion_10">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#catalog_accordion_10" aria-expanded="true" aria-controls="catalog_accordion_10">COMPILAZIONE</button>
                </h2>
                <div class="accordion-collapse collapse" id="catalog_accordion_10" aria-labelledby="heading_catalog_accordion_10" data-bs-parent="#catalog_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                             <?php
                                    echo $app::form_element($form['archy_art_catalog.compilatore'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_compilazione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.data_revisione_compilazione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.status'], "col-4");
                                    ///echo $app::form_element($form['archy_art_catalog.numero_archiviazione'],"col-4");
                                    echo $app::form_element($form['archy_art_catalog.avviso_sospensione'], "col-4");
                                    echo $app::form_element($form['archy_art_catalog.diniego_archiviazione'], "col-4");
                                    // echo $app::form_element($form['archy_art_catalog.richiesta_aggiuntiva'],"col-4");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
            <?php echo $app::form_element($form['archy_art_catalog.model_template'], "col") ?>
        </div>
    </div>
</div>
<?php //$app->additional_fields('form'); ?>
                

<script type="text/javascript">
    
    document.addEventListener("DOMContentLoaded", function () {
        
        manageFields(
        "archy_art_catalog_tipologia_scheda", {
            1: [
                {id: "archy_art_catalog_data_pubblicazione", hide:true},
                {id: "archy_art_catalog_tipologia_fonte_bibliografica", hide:true},
                {id: "archy_art_catalog_descrizione_fonte_bibliografica", hide:true},
                {id: "archy_art_catalog_titolo_volume", hide:true},
                {id: "archy_art_catalog_numero_volume", hide:true}
            ],
            2: [
                {id: "archy_art_catalog_data_pubblicazione", hide:true},
                {id: "archy_art_catalog_tipologia_fonte_bibliografica", hide:true},
                {id: "archy_art_catalog_descrizione_fonte_bibliografica", hide:true},
                {id: "archy_art_catalog_titolo_volume", hide:true},
                {id: "archy_art_catalog_numero_volume", hide:true}
            ]
        }, {
        mode: "hide"
      });
        
        manageFields(
        "archy_art_catalog_tipologia_opera", {
            0: ["archy_art_catalog_parti_mobili"],
            1: ["archy_art_catalog_tipo_bozza","archy_art_catalog_opera_finita_id","archy_art_catalog_stato_bozza"], //'Bozza',
            2: ["archy_art_catalog_protezione"], //'Collage',
            3: ["archy_art_catalog_sottotipo_computer_art", "archy_art_catalog_formato_file", "archy_art_catalog_edizione"], //'Computer Art',
            4: ["archy_art_catalog_tipo_corrispondenza", "archy_art_catalog_mittente_destinatario", "archy_art_catalog_numero_pagine",    "archy_art_catalog_originale_copia"], //'Corrispondenza',
            5: ["archy_art_catalog_cornice"], //'Dipinto',
            6: ["archy_art_catalog_luogo_permanente_murale"], //'Dipinto murale',
            7: ["archy_art_catalog_colore_disegno"], //'Disegno',
            8: ["archy_art_catalog_durata",    "archy_art_catalog_audio_presente"], //'Film',
            9: ["archy_art_catalog_film_collegato_id"], //'Fotogramma filmico',
            10: ["archy_art_catalog_tecnica_fotografica","archy_art_catalog_stato_stampa", "archy_art_catalog_data_di_scatto", "archy_art_catalog_data_di_stampa", "archy_art_catalog_tipo_stampa_fotografia"], //'Fotografia',
            11: ["archy_art_catalog_superficie_origine"], //'Frottage',
            12: ["archy_art_catalog_numero_componenti_installazione",    "archy_art_catalog_dimensioni"], //'Installazione',
            13: ["archy_art_catalog_spazio_minimo_richiesto",    "archy_art_catalog_come_installazione"], //'Installazione ambientale',
            14: ["archy_art_catalog_canali",    "archy_art_catalog_durata_loop"], //'Installazione audio',
            15: ["archy_art_catalog_numero_schermi_proiezioni",    "archy_art_catalog_durata_loop",    "archy_art_catalog_formato_file",    "archy_art_catalog_risoluzione",    "archy_art_catalog_audio_presente"], //'Installazione video',
            16: ["archy_art_catalog_media_presenti",    "archy_art_catalog_hardware_richiesto"], //'Installazione multimediale',
            17: ["archy_art_catalog_tipo_interazione",    "archy_art_catalog_necessita_rete",    "archy_art_catalog_software_hardware_richiesto"], //'Installazione o Opera interattiva',
            18: ["archy_art_catalog_tipologia_documenti",    "archy_art_catalog_editore",    "archy_art_catalog_data_anno",    "archy_art_catalog_isbn",    "archy_art_catalog_numero_pagine", "archy_art_catalog_titolo_volume", "archy_art_catalog_numero_volume" , "archy_art_catalog_data_pubblicazione"], //'Libro',
            19: ["archy_art_catalog_unico_originale",    "archy_art_catalog_rilegatura",    "archy_art_catalog_numero_pagine", "archy_art_catalog_titolo_volume", "archy_art_catalog_numero_volume", "archy_art_catalog_data_pubblicazione"], //'Libro d’artista',
            20: ["archy_art_catalog_tipo_di_oggetto",    "archy_art_catalog_funzionale"], //'Mobile',
            21: ["archy_art_catalog_tipo_multiplo",    "archy_art_catalog_prove_multiplo"], //'Multiplo',
            22: ["archy_art_catalog_categoria_design",   "archy_art_catalog_funzionale"], //'Oggetto di design',
            23: ["archy_art_catalog_movimento_cinetico",    "archy_art_catalog_alimentazione",    "archy_art_catalog_parti_mobili"], //'Opera cinetico-visuale',
            24: ["archy_art_catalog_data_luogo_prima_esecuzione",    "archy_art_catalog_durata",    "archy_art_catalog_ripetibile",    "archy_art_catalog_documentazione_multi"], //'Performance',
            25: ["archy_art_catalog_tipo_progetto",    "archy_art_catalog_stato_progetto",    "archy_art_catalog_output"], //'Progetto',
            26: ["archy_art_catalog_piattaforma",    "archy_art_catalog_software",    "archy_art_catalog_build_versione"], //'Realtà virtuale',
            27: ["archy_art_catalog_oggetto_originario",    "archy_art_catalog_modifiche_ready_made",   "archy_art_catalog_provenienza_note"], //'Ready made',
            28: ["archy_art_catalog_base_scultura"], //'Scultura',
            29: ["archy_art_catalog_titolo_periodico",    "archy_art_catalog_numerazione_periodico",    "archy_art_catalog_editore",    "archy_art_catalog_data_anno",    "archy_art_catalog_numero_pagine",    "archy_art_catalog_issn", "archy_art_catalog_data_pubblicazione"], //'Rivista',
            30: ["archy_art_catalog_tecnica_stampa"], //'Stampa',
            31: ["archy_art_catalog_durata",    "archy_art_catalog_canali",    "archy_art_catalog_formato_file"], //'Audio',
            32: ["archy_art_catalog_durata",    "archy_art_catalog_formato_file",    "archy_art_catalog_risoluzione",    "archy_art_catalog_audio_presente",    "archy_art_catalog_modalita_video"], //'Video arte',
            33: ["archy_art_catalog_derivazione"], //'Calco',
            34: ["archy_art_catalog_stato_lastra_incisione"], //'Lastra di incisione',
            35: ["archy_art_catalog_materiale_maschera",    "archy_art_catalog_indossabile",    "archy_art_catalog_uso_maschera"], //'Maschera',
            36: [""], //'Pittura',
            37: ["archy_art_catalog_software",    "archy_art_catalog_formato_3d",    "archy_art_catalog_texture_presente",    "archy_art_catalog_rig_presente",    "archy_art_catalog_animazione_presente"], //'Modello 3D / Scultura digitale',
            38: ["archy_art_catalog_motore_render",    "archy_art_catalog_output",    "archy_art_catalog_risoluzione"], //'Render 3D (immagine)',
            39: ["archy_art_catalog_durata",    "archy_art_catalog_fps",    "archy_art_catalog_risoluzione",    "archy_art_catalog_formato_file",    "archy_art_catalog_audio_presente"], //'Animazione 3D',
            40: ["archy_art_catalog_ambiente_generativo",    "archy_art_catalog_output",    "archy_art_catalog_variabile_generativo",    "archy_art_catalog_seed_parametri"], //'Opera generativa (coding art)',
            41: ["archy_art_catalog_tipo_ai",    "archy_art_catalog_modello_ai",    "archy_art_catalog_post_produzione",    "archy_art_catalog_software_post_produzione"], //'AI Art',
            42: ["archy_art_catalog_piattaforma",    "archy_art_catalog_marker",    "archy_art_catalog_file_ar",    "archy_art_catalog_necessita_app"], //'Realtà aumentata (AR)',
            43: ["archy_art_catalog_tipo_asset",    "archy_art_catalog_piattaforma",    "archy_art_catalog_formato_3d",    "archy_art_catalog_licenza_asset"], //'Asset digitale / Oggetto virtuale',
            44: ["archy_art_catalog_blockchain",    "archy_art_catalog_contract_address",    "archy_art_catalog_token_id",    "archy_art_catalog_piattaforma_mint"], //'NFT / Crypto-art',
            45: ["archy_art_catalog_url_opera",    "archy_art_catalog_tecnologia_web",    "archy_art_catalog_hosting",    "archy_art_catalog_versione_dipendenze"], //'Net Art / Web-based artwork',
            46: ["archy_art_catalog_setup_sound_art",    "archy_art_catalog_durata_loop",    "archy_art_catalog_spazio_richiesto_sound_art"]//'Sound Art (non solo “Audio”)'
          });
        
        manageFields(
        "archy_art_catalog_opera_edizione", {
            0: [
                {id: "archy_art_catalog_editore", hide:true},
                {id: "archy_art_catalog_numero_edizione", hide:true},
                {id: "archy_art_catalog_edizione", hide:true},
                {id: "archy_art_catalog_stampatore", hide:true} 
            ]
            }, { mode: "hide" });
        manageFields(
        "archy_art_catalog_firma", {
          0: ["archy_art_catalog_posizione_firma"]  
        });
        
        
        
            if($('#archy_art_catalog_tipologia_scheda').val()!=''){ // controllo campo in editing
                check_fields_visibility('archy_art_catalog_tipologia_scheda',$('#archy_art_catalog_tipologia_scheda').val()); 
            }         
			
            // *** TIPOLOGIA DI SCHEDA OPERA *** //
            $('#archy_art_catalog_tipologia_scheda').change(function() {
                check_fields_visibility('archy_art_catalog_tipologia_scheda',$('#archy_art_catalog_tipologia_scheda').val());
            });
 	
      
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.archy-tags-input').forEach(function(input){

        input.addEventListener('keydown', function(e){

            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();

                var value = input.value.trim();

                if (value === '') {
                    return false;
                }

                if (!value.endsWith(',')) {
                    input.value = value + ',';
                }

                input.dispatchEvent(new Event('input', { bubbles: true }));

                setTimeout(function(){
                    input.value = input.value.replace(/,\s*$/, '');
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }, 50);

                return false;
            }

        });

    });

});
</script>