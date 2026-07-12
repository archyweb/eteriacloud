<?php
$form=$data['hidden_columns'];
?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Contratto di Riproduzione</a></li>
    <li class="nav-item"><a class="nav-link" id="final-report-tab" data-bs-toggle="tab" href="#tab-final-report" role="tab" aria-controls="tab-final-report" aria-selected="false">Modello contratto</a></li>
</ul>
<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-meeting" role="tabpanel" aria-labelledby="meeting-tab">
        <div class="accordion mt-2" id="meeting_accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_1" aria-expanded="true" aria-controls="meeting_accordion_1">DATI CEDENTE</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_1" aria-labelledby="heading_meeting_accordion_1" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                         <?php
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.nominativo_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.indirizzo_cedente'],"col-md-6");
               echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.cap_cedente'],"col-md-6");
               echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.comune_cedente'],"col-md-6");
               echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.provincia_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.stato_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.cod_fisc_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.p_iva_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.tipologia_documento_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.numero_documento_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.telefono_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.fax_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.email_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.pec_cedente'],"col-md-6");
               ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_2" aria-expanded="true" aria-controls="meeting_accordion_2">DATI CESSIONARIO</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_2" aria-labelledby="heading_meeting_accordion_2" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                         <?php
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.nominativo_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.indirizzo_cessionario'],"col-md-6");
               echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.cap_cessionario'],"col-md-6");
               echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.comune_cessionario'],"col-md-6");
               echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.provincia_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.stato_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.cod_fisc_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.p_iva_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.tipologia_documento_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.numero_documento_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.telefono_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.fax_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.email_cessionario'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.pec_cessionario'],"col-md-6");
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
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.nominativo_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.indirizzo_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.cap_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.comune_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.provincia_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.stato_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.cod_fisc_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.p_iva_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.tipologia_documento_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.numero_documento_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.telefono_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.email_rappresentante'],"col-md-6");
echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.pec_rappresentante'],"col-md-6");
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
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.titolo_opera'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.tipologia_evento'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.tipo_di_cessione'],"col-md-6");
			   echo '<div class="col-md-12"><hr></div>';
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.durata_cessione'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.durata_tempo'],"col-md-12");
			   echo '<div class="col-md-12"><hr></div>';
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.diritti_patrimoniali'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.data_inizio'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.data_fine'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.tacito_rinnovo'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.periodo_disdetta'],"col-md-6");
               ?>
			   
			   
			   <div class="col-lg-12 testo">
					<p>Obblighi, dichiarazioni e responsabilità</p>
			   </div>
               <?php
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.titolo_proprieta_cedente'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.originario_derivato'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.dicitura_opera'],"col-md-6");
               ?>
			   
			   <div class="col-lg-12 testo">
					<p>Corrispettivo</p>
			   </div>
               <?php
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.tipologia_cessione'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.importo_corrispettivo'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.indirizzo_fattura'],"col-md-12");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.modalita_pagamento'],"col-md-12");
               ?>
			   
			   
			   <div class="col-lg-12 testo">
					<p>Legge applicabile e Foro competente</p>
			   </div>
               <?php
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.sede_adr'],"col-md-6");
			   echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.sede_foro'],"col-md-6");
               ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
            <?php echo $app::form_element($form['archy_art_diritto_riproduzione_immagine.model_template'],"col-md-12"); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
	window.onload=function(){

		// *** DURATA CESSIONE  *** //
		$('#archy_art_diritto_riproduzione_immagine_durata_cessione').change(function(){

                    var durata_tempo =$('#archy_art_diritto_riproduzione_immagine_durata_tempo').parents('div[class^="col-"]');
                    switch($(this).val()){
                        case '1':
                                durata_tempo.css('display','none');
                                $('.form-control',durata_tempo).prop('required',false);
                        break;

                        case '0':
                                durata_tempo.css('display','');
                                $('.form-control',durata_tempo).prop('required',true);
                        break;

                        default :
                                durata_tempo.css('display','none');
                                $('.form-control',durata_tempo).prop('required',false);
                        break;
                    }

		}).trigger('change');

		$('#archy_art_diritto_riproduzione_immagine_tipologia_cessione').change(function(){

			var importo_corrispettivo =$('#archy_art_diritto_riproduzione_immagine_importo_corrispettivo').parents('div[class^="col-"]');			
			var indirizzo_fattura =$('#archy_art_diritto_riproduzione_immagine_indirizzo_fattura').parents('div[class^="col-"]');			
			var modalita_pagamento =$('#archy_art_diritto_riproduzione_immagine_modalita_pagamento').parents('div[class^="col-"]');

			switch($(this).val()){

				case '1':

					importo_corrispettivo.css('display','none');
					$('.form-control',importo_corrispettivo).prop('required',false);
					
					indirizzo_fattura.css('display','none');
					$('.form-control',indirizzo_fattura).prop('required',false);
					
					modalita_pagamento.css('display','none');
					$('.form-control',modalita_pagamento).prop('required',false);
					

				break;

				case '0':

					importo_corrispettivo.css('display','');
					$('.form-control',importo_corrispettivo).prop('required',true);
					
					indirizzo_fattura.css('display','');
					$('.form-control',indirizzo_fattura).prop('required',true);
					
					modalita_pagamento.css('display','');
					$('.form-control',modalita_pagamento).prop('required',true);

				break;
	
				default :

					importo_corrispettivo.css('display','none');
					$('.form-control',importo_corrispettivo).prop('required',false);
					
					indirizzo_fattura.css('display','none');
					$('.form-control',indirizzo_fattura).prop('required',false);
					
					modalita_pagamento.css('display','none');
					$('.form-control',modalita_pagamento).prop('required',false);

				break;

			}

			}).trigger('change');
			
	}
</script>