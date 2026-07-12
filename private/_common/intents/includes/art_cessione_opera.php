<?php $form=$data['hidden_columns']; ?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Contratto di Cessione</a></li>
    <li class="nav-item"><a class="nav-link" id="final-report-tab" data-bs-toggle="tab" href="#tab-final-report" role="tab" aria-controls="tab-final-report" aria-selected="false">Modello Contratto</a></li>
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
                                echo $app::form_element($form['archy_art_cessione_opera.nominativo_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.indirizzo_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.cap_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.comune_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.provincia_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.stato_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.cod_fisc_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.p_iva_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.tipologia_documento_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.numero_documento_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.telefono_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.fax_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.email_cedente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.pec_cedente'],"col-md-4");
                           ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_2" aria-expanded="true" aria-controls="meeting_accordion_2">DATI ACQUIRENTE</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_2" aria-labelledby="heading_meeting_accordion_2" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                             <?php
                                echo $app::form_element($form['archy_art_cessione_opera.nominativo_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.indirizzo_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.cap_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.comune_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.provincia_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.stato_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.cod_fisc_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.p_iva_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.tipologia_documento_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.numero_documento_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.telefono_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.fax_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.email_acquirente'],"col-md-4");
                                echo $app::form_element($form['archy_art_cessione_opera.pec_acquirente'],"col-md-4");
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
                                    echo $app::form_element($form['archy_art_cessione_opera.nominativo_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.indirizzo_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.cap_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.comune_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.provincia_rappresentante'],"col-md-4");
									echo $app::form_element($form['archy_art_cessione_opera.stato_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.cod_fisc_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.p_iva_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.tipologia_documento_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.numero_documento_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.telefono_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.email_rappresentante'],"col-md-4");
                                    echo $app::form_element($form['archy_art_cessione_opera.pec_rappresentante'],"col-md-4");
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
                            <div class="col-sm-12"><h5 class="text-body-emphasis mb-1">Oggetto del Contratto</h5></div>
                                <?php
                                    echo $app::form_element($form['archy_art_cessione_opera.qualifica_cedente'],"col-md-12");
                                    echo $app::form_element($form['archy_art_cessione_opera.titolo_opera'],"col-md-12");
                                ?>
                            <div class="col-sm-12"><h5 class="text-body-emphasis mb-1 mt-2">Obblighi, dichiarazioni e responsabilità</h5></div>
                                <?php
                                    echo $app::form_element($form['archy_art_cessione_opera.vivente'],"col-md-6");
                                    echo $app::form_element($form['archy_art_cessione_opera.tipo_di_cessione'],"col-md-6");
                                    echo $app::form_element($form['archy_art_cessione_opera.titolo_originarieta'],"col-md-6");
                                    echo $app::form_element($form['archy_art_cessione_opera.spese_a_carico'],"col-md-6");
                                    echo $app::form_element($form['archy_art_cessione_opera.nome_trasportatore'],"col-md-6");
                                    echo $app::form_element($form['archy_art_cessione_opera.referente_restauro'],"col-md-6");
                                    echo $app::form_element($form['archy_art_cessione_opera.nome_archivio_artista'],"col-md-6");
                                ?>
                            <div class="col-sm-12"><h5 class="text-body-emphasis mb-1 mt-2">Corrispettivo</h5></div>
                                <?php
                                    echo $app::form_element($form['archy_art_cessione_opera.corrispettivo'],"col-md-12");
                                    echo $app::form_element($form['archy_art_cessione_opera.importo_corrispettivo'],"col-md-12");
                                    echo $app::form_element($form['archy_art_cessione_opera.indirizzo_fattura'],"col-md-12");
                                    echo $app::form_element($form['archy_art_cessione_opera.modalita_pagamento_corrispettivo'],"col-md-12");
                                ?>
                             <div class="col-sm-12"><h5 class="text-body-emphasis mb-1 mt-2">Diritti di utilizzazione economica</h5></div>
                                <?php
                                   echo $app::form_element($form['archy_art_cessione_opera.durata_cessione'],"col-md-6");
                                   echo $app::form_element($form['archy_art_cessione_opera.diritti_di_utilizzazione'],"col-md-6");
                                   echo $app::form_element($form['archy_art_cessione_opera.dettagli_riproduzione'],"col-md-6");
                                   echo $app::form_element($form['archy_art_cessione_opera.dicitura_opera'],"col-md-6");
                                   echo $app::form_element($form['archy_art_cessione_opera.numero_copie_fornite'],"col-md-6");
                                   echo $app::form_element($form['archy_art_cessione_opera.facolta_di_prestito'],"col-md-6");
                                   echo $app::form_element($form['archy_art_cessione_opera.valore_assicurativo'],"col-md-6");
                                   echo $app::form_element($form['archy_art_cessione_opera.nome_assicurazione'],"col-md-6");
                                ?>
                            <div class="col-sm-12"><h5 class="text-body-emphasis mb-1 mt-2">Legge applicabile e Foro competente</h5></div>
                                <?php
                                    echo $app::form_element($form['archy_art_cessione_opera.sede_adr'],"col-md-6");
                                    echo $app::form_element($form['archy_art_cessione_opera.sede_foro'],"col-md-6");

                                ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">      
        
        <div class="row g-3">
            <?php echo $app::form_element($form['archy_art_cessione_opera.model_template'],"col-md-12")?>
        </div>
                    
    </div>
</div>

<script type="text/javascript">

	window.onload=function(){

		// *** IMPORTO DIRITTI  *** //
		$('#archy_art_cessione_opera_corrispettivo').change(function(){

			var importo_corrispettivo =$('#archy_art_cessione_opera_importo_corrispettivo').parents('div[class^="col-"]');
			
			var indirizzo_fattura =$('#archy_art_cessione_opera_indirizzo_fattura').parents('div[class^="col-"]');
			
			var modalita_pagamento_corrispettivo =$('#archy_art_cessione_opera_modalita_pagamento_corrispettivo').parents('div[class^="col-"]');

			///$('.form-control',importo_corrispettivo).prop('required',true)
			///$('.glyphicon-asterisk',importo_corrispettivo).css('visibility','');

			switch($(this).val()){

				case '1':

					importo_corrispettivo.css('display','none');
					$('.form-control',importo_corrispettivo).prop('required',false)
					
					indirizzo_fattura.css('display','none');
					$('.form-control',indirizzo_fattura).prop('required',false)
					
					modalita_pagamento_corrispettivo.css('display','none');
					$('.form-control',modalita_pagamento_corrispettivo).prop('required',false)
					
					///$('.glyphicon-asterisk',importo_corrispettivo).css('visibility','hidden');

				break;

				case '0':

					importo_corrispettivo.css('display','');
					$('.form-control',importo_corrispettivo).prop('required',true)
					
					indirizzo_fattura.css('display','');
					$('.form-control',indirizzo_fattura).prop('required',true)
					
					modalita_pagamento_corrispettivo.css('display','');
					$('.form-control',modalita_pagamento_corrispettivo).prop('required',true)
					///$('.glyphicon-asterisk',importo_corrispettivo).css('visibility','visible');

				break;
	
				default :

					importo_corrispettivo.css('display','none');
					$('.form-control',importo_corrispettivo).prop('required',false)
					
					indirizzo_fattura.css('display','none');
					$('.form-control',indirizzo_fattura).prop('required',false)
					
					modalita_pagamento_corrispettivo.css('display','none');
					$('.form-control',modalita_pagamento_corrispettivo).prop('required',false)

				break;

			}

			}).trigger('change');
			
			// *** FINE IMPORTO DIRITTI  *** //
		
	}
</script>