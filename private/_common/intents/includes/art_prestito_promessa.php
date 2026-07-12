<?php
$form=$data['hidden_columns'];
?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Contratto di Promessa prestito</a></li>
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
			   echo $app::form_element($form['archy_art_prestito_promessa.nominativo_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.indirizzo_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_promessa.cap_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_promessa.comune_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_promessa.provincia_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.stato_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.cod_fisc_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.p_iva_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.tipologia_documento_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.numero_documento_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.telefono_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_promessa.fax_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.email_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.pec_archivio'],"col-md-4");
               ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_2" aria-expanded="true" aria-controls="meeting_accordion_2">DATI CONTROPARTE</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_2" aria-labelledby="heading_meeting_accordion_2" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                         <?php
			   echo $app::form_element($form['archy_art_prestito_promessa.nominativo_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.indirizzo_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_promessa.cap_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_promessa.comune_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_prestito_promessa.provincia_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.stato_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.cod_fisc_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.p_iva_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.tipologia_documento_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.numero_documento_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.telefono_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.email_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_prestito_promessa.pec_controparte'],"col-md-4");
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
echo $app::form_element($form['archy_art_prestito_promessa.nominativo_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.indirizzo_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.cap_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.comune_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.provincia_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.stato_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.cod_fisc_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.p_iva_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.tipologia_documento_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.numero_documento_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.telefono_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.email_rappresentante'],"col-md-4");
echo $app::form_element($form['archy_art_prestito_promessa.pec_rappresentante'],"col-md-4");
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
				<p>Oggetto della Promessa di prestito</p>
			</div>

			<?php
			echo $app::form_element($form['archy_art_prestito_promessa.titolo_disposizione'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.titolo_opera'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.termine_prestito'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.scopo_utilizzo'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.evento_prestito'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.date_evento'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.sede_evento'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.progetto_evento'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.valore_opera'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.termine_stipulazione'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.data_inizio'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.data_fine'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.tacito_rinnovo'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.periodo_disdetta'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.giorni_preavviso'],"col-md-12");
			?>
			
			
			
			<div class="col-lg-12 testo">
				<p>Obblighi, dichiarazioni e responsabilità</p>
			</div>

			<?php
			echo $app::form_element($form['archy_art_prestito_promessa.tipo_di_cessione'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.titolo_originarieta'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.valore_assicurativo'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.nome_assicurazione'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.sede_riconsegna'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.dettagli_materiali'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.nome_trasportatore'],"col-md-12");
			?>
			
			<div class="col-lg-12 testo">
				<p>Tutela dell’Opera</p>
			</div>

			<?php
			
			echo $app::form_element($form['archy_art_prestito_promessa.tipo_sorveglianza'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.esposizione_esterno'],"col-md-12");
			?>

			
			<div class="col-lg-12 testo">
				<p>Oneri di trasporto dell’Opera</p>
			</div>

			<?php
			echo $app::form_element($form['archy_art_prestito_promessa.opzione_registrar'],"col-md-12"); 
			echo $app::form_element($form['archy_art_prestito_promessa.individuazione_registrar'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.nominativo_registrar'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.tel_registrar'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.email_registrar'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.pec_registrar'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.spese_registrar'],"col-md-12");
			?>
			
			
			
			<div class="col-lg-12 testo">
				<p>Diritti di utilizzazione economica</p>
			</div>

			<?php
			
			echo $app::form_element($form['archy_art_prestito_promessa.concessione_diritti'],"col-md-12");
                        echo $app::form_element($form['archy_art_prestito_promessa.importo_corrispettivo'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.diritto_immagine'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.dettagli_riproduzione'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.copyright_fotgrafico'],"col-md-12");
			 echo $app::form_element($form['archy_art_prestito_promessa.numero_copie'],"col-md-12");
			?>
			
			
			<div class="col-lg-12 testo">
				<p>Legge applicabile e Foro competente</p>
			</div>

			<?php
			echo $app::form_element($form['archy_art_prestito_promessa.sede_adr'],"col-md-12");
			echo $app::form_element($form['archy_art_prestito_promessa.sede_foro'],"col-md-12");
			?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
             <?php echo $app::form_element($form['archy_art_prestito_promessa.model_template'],"col-md-12"); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
	window.onload=function(){
		
				// *** Individuazione registrar  *** //
		$('#archy_art_prestito_promessa_individuazione_registrar').change(function(){

			var nominativo_registrar =$('#archy_art_prestito_promessa_nominativo_registrar').parents('div[class^="col-"]');
			
			var tel_registrar =$('#archy_art_prestito_promessa_tel_registrar').parents('div[class^="col-"]');
			
			var email_registrar =$('#archy_art_prestito_promessa_email_registrar').parents('div[class^="col-"]');
			
			var pec_registrar =$('#archy_art_prestito_promessa_pec_registrar').parents('div[class^="col-"]');

			///$('.form-control',nominativo_registrar).prop('required',true)
			///$('.glyphicon-asterisk',nominativo_registrar).css('visibility','');

			switch($(this).val()){

				case '0':

					nominativo_registrar.css('display','');
					$('.form-control',nominativo_registrar).prop('required',false)
					
					tel_registrar.css('display','');
					$('.form-control',tel_registrar).prop('required',false)
					
					email_registrar.css('display','');
					$('.form-control',email_registrar).prop('required',false)
					
					pec_registrar.css('display','');
					$('.form-control',pec_registrar).prop('required',false)
					
					///$('.glyphicon-asterisk',nominativo_registrar).css('visibility','hidden');

				break;

				case '1':

					nominativo_registrar.css('display','none');
					$('.form-control',nominativo_registrar).prop('required',true)
					
					tel_registrar.css('display','none');
					$('.form-control',tel_registrar).prop('required',false)
					
					email_registrar.css('display','none');
					$('.form-control',email_registrar).prop('required',false)
					
					pec_registrar.css('display','none');
					$('.form-control',pec_registrar).prop('required',false)
					///$('.glyphicon-asterisk',nominativo_registrar).css('visibility','visible');

				break;
	
				default :

					nominativo_registrar.css('display','none');
					$('.form-control',nominativo_registrar).prop('required',false)
					
					tel_registrar.css('display','none');
					$('.form-control',tel_registrar).prop('required',false)
					
					email_registrar.css('display','none');
					$('.form-control',email_registrar).prop('required',false)
					
					pec_registrar.css('display','none');
					$('.form-control',pec_registrar).prop('required',false)

				break;

			}

			}).trigger('change');

		
	}
</script>