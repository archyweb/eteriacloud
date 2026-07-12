<?php
$form=$data['hidden_columns'];
?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Contratto di Prestito definitivo</a></li>
    <li class="nav-item"><a class="nav-link" id="final-report-tab" data-bs-toggle="tab" href="#tab-final-report" role="tab" aria-controls="tab-final-report" aria-selected="false">Modello contratto</a></li>
</ul>
<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-meeting" role="tabpanel" aria-labelledby="meeting-tab">
        <div class="accordion mt-2" id="meeting_accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_1" aria-expanded="true" aria-controls="meeting_accordion_1">DATI PRESTATORE</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_1" aria-labelledby="heading_meeting_accordion_1" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                         <?php
			   echo $app::form_element($form['archy_art_prestito_definitivo.nominativo_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.indirizzo_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_definitivo.cap_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_definitivo.comune_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_definitivo.provincia_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.stato_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.cod_fisc_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.p_iva_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.tipologia_documento_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.numero_documento_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.telefono_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_definitivo.fax_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.email_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.pec_archivio'],"col-md-4");
               ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_2" aria-expanded="true" aria-controls="meeting_accordion_2">DATI PRESTATARIO</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_2" aria-labelledby="heading_meeting_accordion_2" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                         <?php
			   echo $app::form_element($form['archy_art_prestito_definitivo.nominativo_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.indirizzo_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_definitivo.cap_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_definitivo.comune_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_definitivo.provincia_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.stato_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.cod_fisc_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.p_iva_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.tipologia_documento_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.numero_documento_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.telefono_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.email_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.pec_controparte'],"col-md-4");
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
                            echo $app::form_element($form['archy_art_prestito_definitivo.nominativo_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.indirizzo_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.cap_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.comune_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.provincia_rappresentante'],"col-md-4");
							echo $app::form_element($form['archy_art_prestito_definitivo.stato_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.cod_fisc_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.p_iva_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.tipologia_documento_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.numero_documento_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.telefono_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.email_rappresentante'],"col-md-4");
                            echo $app::form_element($form['archy_art_prestito_definitivo.pec_rappresentante'],"col-md-4");
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
                        <?php
			   echo $app::form_element($form['archy_art_prestito_definitivo.titolo_disposizione'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.titolo_opera'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.valore_opera'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.scopo_utilizzo'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.evento_prestito'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.date_evento'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.sede_evento'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.progetto_evento'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.data_promessa'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.data'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_definitivo.luogo'],"col-md-4");
			?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
            <?php echo $app::form_element($form['archy_art_prestito_definitivo.model_template'],"col-md-12"); ?>
        </div>
    </div>
</div>