<?php
$form=$data['hidden_columns'];
?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Contratto</a></li>
    <li class="nav-item"><a class="nav-link" id="final-report-tab" data-bs-toggle="tab" href="#tab-final-report" role="tab" aria-controls="tab-final-report" aria-selected="false">Modello contratto</a></li>
</ul>
<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-meeting" role="tabpanel" aria-labelledby="meeting-tab">
        <div class="accordion mt-2" id="meeting_accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_1" aria-expanded="true" aria-controls="meeting_accordion_1">DATI ARCHIVIO</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_1" aria-labelledby="heading_meeting_accordion_1" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                        <?php
echo $app::form_element($form['archy_art_contratto_valutazione.nominativo_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.indirizzo_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.cap_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.comune_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.provincia_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.stato_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.cod_fiscale_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.p_iva_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.tipologia_documento_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.numero_documento_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.telefono_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.fax_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.email_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.pec_ente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.presidente'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.scientifico_autentiche'],"col-md-4");
               ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_2" aria-expanded="true" aria-controls="meeting_accordion_2">DATI RICHIEDENTE</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_2" aria-labelledby="heading_meeting_accordion_2" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                        <?php
echo $app::form_element($form['archy_art_contratto_valutazione.nominativo_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.indirizzo_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.cap_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.comune_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.provincia_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.stato_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.cod_fiscale_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.p_iva_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.tipologia_documento_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.numero_documento_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.telefono_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.fax_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.email_controparte'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.pec_controparte'],"col-md-4");
				?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_3">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_3" aria-expanded="true" aria-controls="meeting_accordion_3">DATI RAPPRESENTANTE</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_3" aria-labelledby="heading_meeting_accordion_3" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                        <?php
echo $app::form_element($form['archy_art_contratto_valutazione.nominativo_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.indirizzo_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.cap_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.comune_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.provincia_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.stato_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.cod_fiscale_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.p_iva_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.tipologia_documento_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.numero_documento_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.telefono_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.fax_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.email_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_contratto_valutazione.pec_rappresentante'],"col-md-4");
				?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_4">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_4" aria-expanded="true" aria-controls="meeting_accordion_4">DATI CONTRATTUALI</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_4" aria-labelledby="heading_meeting_accordion_4" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                        <div class="col-lg-12 testo">
					<p>Premesse e dati dell'Opera</p>
			</div>
               <?php
			    echo $app::form_element($form['archy_art_contratto_valutazione.scopo_archivio'],"col-md-12");
			    echo $app::form_element($form['archy_art_contratto_valutazione.nome_artista'],"col-md-6");
				echo $app::form_element($form['archy_art_contratto_valutazione.titolo_opera'],"col-md-6");
				echo $app::form_element($form['archy_art_contratto_valutazione.anno_opera'],"col-md-6");
				echo $app::form_element($form['archy_art_contratto_valutazione.tecnica_dimensioni'],"col-md-6");
				echo $app::form_element($form['archy_art_contratto_valutazione.proprieta_opera'],"col-md-6");
               ?>
			   
			   
			<div class="col-lg-12 testo">
					<p>Dati Contrattuali</p>
			</div>
               <?php
			    echo $app::form_element($form['archy_art_contratto_valutazione.somma_per_valutazione'],"col-md-6");
				echo $app::form_element($form['archy_art_contratto_valutazione.penale'],"col-md-6");
				echo $app::form_element($form['archy_art_contratto_valutazione.giorni_comunicazione'],"col-md-6");
				echo $app::form_element($form['archy_art_contratto_valutazione.importo_forfettario'],"col-md-6");
               ?>  
			   
			   
			<div class="col-lg-12 testo">
					<p>Legge applicabile e Foro competente</p>
			</div>
               <?php
			    echo $app::form_element($form['archy_art_contratto_valutazione.foro'],"col-md-6");
				echo $app::form_element($form['archy_art_contratto_valutazione.sede_adr'],"col-md-6");
               ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
            <?php echo $app::form_element($form['archy_art_contratto_valutazione.model_template'],"col-md-12"); ?>
        </div>
    </div>
</div>