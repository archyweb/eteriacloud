<?php
$form=$data['hidden_columns'];
?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Contratto di Donazione</a></li>
    <li class="nav-item"><a class="nav-link" id="final-report-tab" data-bs-toggle="tab" href="#tab-final-report" role="tab" aria-controls="tab-final-report" aria-selected="false">Modello contratto</a></li>
</ul>
<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-meeting" role="tabpanel" aria-labelledby="meeting-tab">
        <div class="accordion mt-2" id="meeting_accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_1" aria-expanded="true" aria-controls="meeting_accordion_1">DATI DONANTE</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_1" aria-labelledby="heading_meeting_accordion_1" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                        <?php
			   echo $app::form_element($form['archy_art_donazione.nominativo_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.indirizzo_donante'],"col-md-6");
               echo $app::form_element($form['archy_art_donazione.cap_donante'],"col-md-6");
               echo $app::form_element($form['archy_art_donazione.comune_donante'],"col-md-6");
               echo $app::form_element($form['archy_art_donazione.provincia_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.stato_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.cod_fisc_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.p_iva_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.tipologia_documento_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.numero_documento_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.telefono_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.fax_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.email_donante'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.pec_donante'],"col-md-6");
			   
			   echo $app::form_element($form['archy_art_donazione.timbro_certficatore'],"col-md-6");
			   echo $app::form_element($form['archy_art_donazione.archivio_artista'],"col-md-6");
               ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_2" aria-expanded="true" aria-controls="meeting_accordion_2">DATI DONATARIO</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_2" aria-labelledby="heading_meeting_accordion_2" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                         <?php
			   echo $app::form_element($form['archy_art_donazione.nominativo_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.indirizzo_donatario'],"col-md-4");
               echo $app::form_element($form['archy_art_donazione.cap_donatario'],"col-md-4");
               echo $app::form_element($form['archy_art_donazione.comune_donatario'],"col-md-4");
               echo $app::form_element($form['archy_art_donazione.provincia_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.stato_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.cod_fisc_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.p_iva_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.tipologia_documento_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.numero_documento_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.telefono_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.fax_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.email_donatario'],"col-md-4");
			   echo $app::form_element($form['archy_art_donazione.pec_donatario'],"col-md-4");
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
					echo $app::form_element($form['archy_art_donazione.nominativo_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.indirizzo_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.cap_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.comune_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.provincia_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.stato_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.cod_fisc_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.p_iva_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.tipologia_documento_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.numero_documento_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.telefono_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.email_rappresentante'],"col-md-4");
					echo $app::form_element($form['archy_art_donazione.pec_rappresentante'],"col-md-4");
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
			<p>Oggetto del Contratto</p>
			</div>
               <?php
					echo $app::form_element($form['archy_art_donazione.titolo_disposizione'],"col-md-6");
					echo $app::form_element($form['archy_art_donazione.titolo_opera'],"col-md-6");
					echo $app::form_element($form['archy_art_donazione.privato_pubblico'],"col-md-6");
               ?>
			   
			   <div class="col-lg-12 testo">
			<p>Obblighi, dichiarazioni e responsabilità delle Parti</p>
			</div>
				<?php
				   echo $app::form_element($form['archy_art_donazione.vivente'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.nominativo_autore'],"col-md-6");
				   
				   echo $app::form_element($form['archy_art_donazione.titolarita'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.titolo_proprieta_donante'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.originario_derivato'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.valore_opera'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.referente_restauro'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.nome_archivio_artista'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.spese_imballaggio'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.nome_trasportatore'],"col-md-6");
				?>
 
 
			<div class="col-lg-12 testo">
			<p>Diritti di utilizzazione economica</p>
			</div>

				<?php
				   echo $app::form_element($form['archy_art_donazione.tipo_di_cessione'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.durata_cessione'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.diritti_opera'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.dettagli_riproduzione'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.dicitura_opera'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.numero_copie'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.facolta_di_prestito'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.valore_assicurativo'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.nome_assicurazione'],"col-md-6");
				?>
 
 
			<div class="col-lg-12 testo">
			<p>Clausole finali</p>
			</div>

				<?php
				   echo $app::form_element($form['archy_art_donazione.sede_adr'],"col-md-6");
				   echo $app::form_element($form['archy_art_donazione.sede_foro'],"col-md-6");
                                   echo $app::form_element($form['archy_art_donazione.luogo'],"col-md-6");
				 ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
             <?php echo $app::form_element($form['archy_art_donazione.model_template'],"col-md-12"); ?>
        </div>
    </div>
</div>