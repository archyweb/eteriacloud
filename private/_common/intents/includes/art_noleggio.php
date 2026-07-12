<?php
$form=$data['hidden_columns'];
?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Contratto di Noleggio</a></li>
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
			   echo $app::form_element($form['archy_art_noleggio.nominativo_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.indirizzo_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_noleggio.cap_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_noleggio.comune_archivio'],"col-md-4");
               echo $app::form_element($form['archy_art_noleggio.provincia_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.stato_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.cod_fisc_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.p_iva_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.tipologia_documento_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.numero_documento_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.telefono_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.fax_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.email_archivio'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.pec_archivio'],"col-md-4");
               ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_2" aria-expanded="true" aria-controls="meeting_accordion_2">DATI UTILIZZATORE</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_2" aria-labelledby="heading_meeting_accordion_2" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                           <?php
			   echo $app::form_element($form['archy_art_noleggio.nominativo_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.indirizzo_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_noleggio.cap_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_noleggio.comune_controparte'],"col-md-4");
               echo $app::form_element($form['archy_art_noleggio.provincia_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.stato_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.cod_fisc_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.p_iva_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.tipologia_documento_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.numero_documento_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.telefono_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.fax_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.email_controparte'],"col-md-4");
			   echo $app::form_element($form['archy_art_noleggio.pec_controparte'],"col-md-4");
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
			echo $app::form_element($form['archy_art_noleggio.nominativo_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.indirizzo_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.cap_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.comune_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.provincia_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.stato_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.cod_fisc_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.p_iva_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.tipologia_documento_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.numero_documento_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.telefono_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.email_rappresentante'],"col-md-4");
			echo $app::form_element($form['archy_art_noleggio.pec_rappresentante'],"col-md-4");
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
echo $app::form_element($form['archy_art_noleggio.titolo_disposizione'],"col-md-6");
echo $app::form_element($form['archy_art_noleggio.scopo_utilizzo'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.evento_noleggio'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.titolo_opera'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.date_evento'],"col-md-6");
               echo $app::form_element($form['archy_art_noleggio.sede_evento'],"col-md-6");
               echo $app::form_element($form['archy_art_noleggio.progetto_evento'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.valore_opera'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.data_inizio'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.data_fine'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.tacito_rinnovo'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.periodo_disdetta'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.giorni_preavviso'],"col-md-6");

?>

			<div class="col-lg-12 testo">
<p>Obblighi, dichiarazioni e responsabilità delle Parti</p>
</div>

<?php
				echo $app::form_element($form['archy_art_noleggio.archivio_autore'],"col-md-6");
				echo $app::form_element($form['archy_art_noleggio.esclusiva_non_esclusiva'],"col-md-6");
				echo $app::form_element($form['archy_art_noleggio.originario_derivato'],"col-md-6");
				//echo $app::form_element($form['archy_art_noleggio.titolo_originarieta'],"col-md-6");
				echo $app::form_element($form['archy_art_noleggio.valore_assicurativo'],"col-md-6");
			    echo $app::form_element($form['archy_art_noleggio.nome_assicurazione'],"col-md-6");
			    echo $app::form_element($form['archy_art_noleggio.sede_riconsegna'],"col-md-6");
			    echo $app::form_element($form['archy_art_noleggio.tempo_concessione'],"col-md-6");
				echo $app::form_element($form['archy_art_noleggio.dettagli_materiali'],"col-md-6");
				echo $app::form_element($form['archy_art_noleggio.utilizzatore_archivio'],"col-md-6");
			    echo $app::form_element($form['archy_art_noleggio.nome_trasportatore'],"col-md-6");




?>
			<div class="col-lg-12 testo">
<p>Tutela dell’Opera</p>
</div>

<?php
			   echo $app::form_element($form['archy_art_noleggio.tipo_sorveglianza'],"col-md-12");
			   echo $app::form_element($form['archy_art_noleggio.descrizione_sorveglianza'],"col-md-12");
			   echo '<div class="col-md-12"><hr></div>';
			   echo $app::form_element($form['archy_art_noleggio.esposizione_esterna'],"col-md-6");
?>


			<div class="col-lg-12 testo">
<p>Oneri di trasporto dell’Opera e responsabilità dell’Utilizzatore</p>
</div>

				<?php

				echo $app::form_element($form['archy_art_noleggio.opzione_registrar'],"col-md-6");
			    echo $app::form_element($form['archy_art_noleggio.individuazione_registrar'],"col-md-12");
				echo $app::form_element($form['archy_art_noleggio.nominativo_registrar'],"col-md-6");
			    echo $app::form_element($form['archy_art_noleggio.tel_registrar'],"col-md-12");
				echo $app::form_element($form['archy_art_noleggio.email_registrar'],"col-md-6");
			    echo $app::form_element($form['archy_art_noleggio.pec_registrar'],"col-md-12");
			    echo $app::form_element($form['archy_art_noleggio.spese_registrar'],"col-md-12");

				?>

			<div class="col-lg-12 testo">
<p>Corrispettivo</p>
</div>
				<?php
				
			   echo $app::form_element($form['archy_art_noleggio.periodicita_canone'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.corrispettivo_noleggio'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.esposizione_iva'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.valore_iva'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.giorno_pagamento'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.periodicita_pagamento'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.modalita_pagamento'],"col-md-6");
			   
			echo '<div class="col-md-6"><hr></div>';
			   echo $app::form_element($form['archy_art_noleggio.provvigione_vendita'],"col-md-12");
			   echo $app::form_element($form['archy_art_noleggio.provvigione_percentuale'],"col-md-12");
			echo '<div class="col-md-12"><hr></div>';
			   
			   echo $app::form_element($form['archy_art_noleggio.sconto_canone_per_acquisto'],"col-md-6");
			  
				

				?>
			<div class="col-lg-12 testo">
<p>Diritti di utilizzazione economica dell’Opera</p>
</div>
				<?php
			   echo $app::form_element($form['archy_art_noleggio.concessione_diritti'],"col-md-12");
			   echo $app::form_element($form['archy_art_noleggio.diritti_immagine_corrispettivo'],"col-md-12");
			   echo $app::form_element($form['archy_art_noleggio.diritto_immagine'],"col-md-12");   
			echo '<div class="col-md-12"><hr></div>';
				
				echo $app::form_element($form['archy_art_noleggio.dettagli_riproduzione'],"col-md-6");
				echo $app::form_element($form['archy_art_noleggio.dicitura_opera'],"col-md-12");
				echo $app::form_element($form['archy_art_noleggio.numero_copie'],"col-md-6");
				

				?>



				
			<div class="col-lg-12 testo">
<p>Legge applicabile e Foro competente</p>
</div>
			
			
			
			
               <?php
			   echo $app::form_element($form['archy_art_noleggio.sede_adr'],"col-md-6");
			   echo $app::form_element($form['archy_art_noleggio.sede_foro'],"col-md-6");
                           echo $app::form_element($form['archy_art_noleggio.data'],"col-md-12");
			   echo $app::form_element($form['archy_art_noleggio.luogo_contratto'],"col-md-12");
			   ?>

			
            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
             <?php echo $app::form_element($form['archy_art_noleggio.model_template'],"col-md-12"); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
	window.onload=function(){
		

		// *** TIPOLOGIA DI SORVEGLIANZA *** //
		$('#archy_art_noleggio_tipo_sorveglianza').change(function(){

			var sorveglianza =$('#archy_art_noleggio_descrizione_sorveglianza').parents('div[class^="col-"]');

			switch($(this).val()){

				case '0':

					sorveglianza.css('display','none');
					$('.form-control',sorveglianza).prop('required',false)

				break;

				case '1':

					sorveglianza.css('display','');
					$('.form-control',sorveglianza).prop('required',true)

				break;

				default :

					sorveglianza.css('display','none');
					$('.form-control',sorveglianza).prop('required',false)

				break;

			}

		}).trigger('change');

		// *** FINE TIPOLOGIA DI SORVEGLIANZA ***

		// *** CONCESSIONE DIRITTI D'IMMAGINE  *** //
		$('#archy_art_noleggio_concessione_diritti').change(function(){

			var diritti =$('#archy_art_noleggio_diritti_immagine_corrispettivo').parents('div[class^="col-"]');

			switch($(this).val()){

				case '0':

					diritti.css('display','');
					$('.form-control',diritti).prop('required',false)

				break;

				case '1':

					diritti.css('display','none');
					$('.form-control',diritti).prop('required',true)

				break;
				
				case '2':

					diritti.css('display','none');
					$('.form-control',diritti).prop('required',true)

				break;

				default :

					diritti.css('display','none');
					$('.form-control',diritti).prop('required',false)

				break;

			}

		}).trigger('change');

		// *** FINE CONCESSIONE DIRITTI D'IMMAGINE  ***

		// *** PROVVIGIONE VENDITA  *** //
		$('#archy_art_noleggio_provvigione_vendita').change(function(){

			var provv_percentuale =$('#archy_art_noleggio_provvigione_percentuale').parents('div[class^="col-"]');

			///$('.form-control',provv_percentuale).prop('required',true)
			///$('.glyphicon-asterisk',provv_percentuale).css('visibility','');

			switch($(this).val()){

				case '0':

					provv_percentuale.css('display','none');
					$('.form-control',provv_percentuale).prop('required',false)
					///$('.glyphicon-asterisk',provv_percentuale).css('visibility','hidden');

				break;

				case '1':

					provv_percentuale.css('display','');
					$('.form-control',provv_percentuale).prop('required',true)
					///$('.glyphicon-asterisk',provv_percentuale).css('visibility','visible');

				break;
	
				default :

					provv_percentuale.css('display','none');
					$('.form-control',provv_percentuale).prop('required',false)

				break;

			}

			}).trigger('change');

		// *** FINE PROVVIGIONE VENDITA ***
		
		// *** Individuazione registrar  *** //
		$('#archy_art_noleggio_individuazione_registrar').change(function(){

			var nominativo_registrar =$('#archy_art_noleggio_nominativo_registrar').parents('div[class^="col-"]');
			
			var tel_registrar =$('#archy_art_noleggio_tel_registrar').parents('div[class^="col-"]');
			
			var email_registrar =$('#archy_art_noleggio_email_registrar').parents('div[class^="col-"]');
			
			var pec_registrar =$('#archy_art_noleggio_pec_registrar').parents('div[class^="col-"]');

			///$('.form-control',nominativo_registrar).prop('required',true)
			///$('.glyphicon-asterisk',nominativo_registrar).css('visibility','');

			switch($(this).val()){

				case '0':

					nominativo_registrar.css('display','none');
					$('.form-control',nominativo_registrar).prop('required',false)
					
					tel_registrar.css('display','none');
					$('.form-control',tel_registrar).prop('required',false)
					
					email_registrar.css('display','none');
					$('.form-control',email_registrar).prop('required',false)
					
					pec_registrar.css('display','none');
					$('.form-control',pec_registrar).prop('required',false)
					
					///$('.glyphicon-asterisk',nominativo_registrar).css('visibility','hidden');

				break;

				case '1':

					nominativo_registrar.css('display','');
					$('.form-control',nominativo_registrar).prop('required',true)
					
					tel_registrar.css('display','');
					$('.form-control',tel_registrar).prop('required',false)
					
					email_registrar.css('display','');
					$('.form-control',email_registrar).prop('required',false)
					
					pec_registrar.css('display','');
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
