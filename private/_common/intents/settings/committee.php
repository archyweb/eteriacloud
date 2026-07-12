<?php
$settings=$app->get_settings($domain='impostazioni_comitato');
$sms_enabled=$app->config['sms']['enabled']? '' : 'disabled';
$first=(object)@$settings->first;
$second=(object)@$settings->second;
$third=(object)@$settings->third;
$first_recession=(object)@$settings->first_recession;
$second_recession=(object)@$settings->second_recession;
$third_recession=(object)@$settings->third_recession;
$membership=new Membership();
$fee_list=$membership->get_subscription_fee_list();
?>
<form role="form" class="row g-3 needs-validation" novalidate="" action="/archy/app.php?do_action=store_settings" method="post" enctype="multipart/form-data">
    <div class="col-12 mb-3">
        <div class="row justify-content-center justify-content-sm-between">
            <div class="col-sm-auto text-center">
                <h5 class="d-inline-block p-2">Gestione Comitato</h5>
            </div>
            <div class="col-sm-auto d-flex justify-content-center mt-1 mt-sm-0">
                <div class="btn-group" role="group">
                    <a class="btn btn-secondary me-1 mb-1" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Reimposta modulo"><span class="fas fa-sync"></span></a>
                    <button type="submit" class="btn btn-success me-1 mb-1" data-toggle="tooltip" data-placement="left" title="Salva modifiche">Salva</button>
                </div>
            </div>	
        </div>
    </div>
    
    <div class="accordion mt-2" id="foundation_accordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading_foundation_accordion_1">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#foundation_accordion_1" aria-expanded="true" aria-controls="foundation_accordion_1">Inserimento dati predefiniti ai fini della generazione dei certificati e dei contratti</button>
            </h2>
            <div class="accordion-collapse collapse" id="foundation_accordion_1" aria-labelledby="heading_foundation_accordion_1" data-bs-parent="#foundation_accordion">
                <div class="accordion-body">
                   <div class="row g-3">
			<div class="col-sm-3">
                     <label class="form-label" for="nominativo_certificatore">Nominativo Ente</label>
                     <input type="text" name="settings[impostazioni_comitato][nominativo_certificatore]" value="<?php echo form_value(@$settings->nominativo_certificatore)?>" class="form-control" id="nominativo_certificatore">
                  </div>
                  <div class="col-sm-3">
                     <label class="form-label" for="indirizzo_certificatore">Indirizzo</label>
                     <input type="text" name="settings[impostazioni_comitato][indirizzo_certificatore]" value="<?php echo form_value(@$settings->indirizzo_certificatore)?>" class="form-control" id="indirizzo_certificatore">
                  </div>
				  
                    <div class="col-sm-3">
                     <label class="form-label" for="cap_certificatore">Cap</label>
                     <input type="text" name="settings[impostazioni_comitato][cap_certificatore]" value="<?php echo form_value(@$settings->cap_certificatore)?>" class="form-control" id="cap_certificatore">
                  </div>
				  
                    <div class="col-sm-3">
                     <label class="form-label" for="comune_certificatore">Comune</label>
                     <input type="text" name="settings[impostazioni_comitato][comune_certificatore]" value="<?php echo form_value(@$settings->comune_certificatore)?>" class="form-control" id="comune_certificatore">
                  </div>
		<div class="col-sm-3">
                     <label class="form-label" for="provincia_certificatore">Provincia</label>
                     <input type="text" name="settings[impostazioni_comitato][provincia_certificatore]" value="<?php echo form_value(@$settings->provincia_certificatore)?>" class="form-control" id="provincia_certificatore">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="stato_certificatore">Nazione</label>
                     <input type="text" name="settings[impostazioni_comitato][stato_certificatore]" value="<?php echo form_value(@$settings->stato_certificatore)?>" class="form-control" id="stato_certificatore">
                  </div>
                       
			<div class="col-sm-3">
                     <label class="form-label" for="cod_fisc_certificatore">Codice Fiscale</label>
                     <input type="text" name="settings[impostazioni_comitato][cod_fisc_certificatore]" value="<?php echo form_value(@$settings->cod_fisc_certificatore)?>" class="form-control" id="cod_fisc_certificatore">
                  </div>
			 <div class="col-sm-3">
                     <label class="form-label" for="p_iva_certificatore">Partita iva</label>
                     <input type="text" name="settings[impostazioni_comitato][p_iva_certificatore]" value="<?php echo form_value(@$settings->p_iva_certificatore)?>" class="form-control" id="p_iva_certificatore">
                  </div>
                   
			<div class="col-sm-3">
                     <label class="form-label" for="tipologia_documento_certificatore">Tipologia documento d'identità</label>
                     <input type="text" name="settings[impostazioni_comitato][tipologia_documento_certificatore]" value="<?php echo form_value(@$settings->tipologia_documento_certificatore)?>" class="form-control" id="tipologia_documento_certificatore">
                  </div>
				  
		 <div class="col-sm-3">
                     <label class="form-label" for="numero_documento_certificatore">Numero documento d'identità</label>
                     <input type="text" name="settings[impostazioni_comitato][numero_documento_certificatore]" value="<?php echo form_value(@$settings->numero_documento_certificatore)?>" class="form-control" id="numero_documento_certificatore">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="telefono_certificatore">Telefono</label>
                     <input type="text" name="settings[impostazioni_comitato][telefono_certificatore]" value="<?php echo form_value(@$settings->telefono_certificatore)?>" class="form-control" id="telefono_certificatore">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="fax_certificatore">Fax</label>
                     <input type="text" name="settings[impostazioni_comitato][fax_certificatore]" value="<?php echo form_value(@$settings->fax_certificatore)?>" class="form-control" id="fax_certificatore">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="email_certificatore">E-Mail</label>
                     <input type="text" name="settings[impostazioni_comitato][email_certificatore]" value="<?php echo form_value(@$settings->email_certificatore)?>" class="form-control" id="email_certificatore">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="pec_certificatore">Pec</label>
                     <input type="text" name="settings[impostazioni_comitato][pec_certificatore]" value="<?php echo form_value(@$settings->pec_certificatore)?>" class="form-control" id="pec_certificatore">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="web_site_certificatore">Web site</label>
                     <input type="text" name="settings[impostazioni_comitato][web_site_certificatore]" value="<?php echo form_value(@$settings->web_site_certificatore)?>" class="form-control" id="web_site_certificatore">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="luogo_certificatore">Luogo</label>
                     <input type="text" name="settings[impostazioni_comitato][luogo_certificatore]" value="<?php echo form_value(@$settings->luogo_certificatore)?>" class="form-control" id="luogo_certificatore">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="responsabile_dati">Responsabile dei dati personali</label>
                     <input type="text" name="settings[impostazioni_comitato][responsabile_dati]" value="<?php echo form_value(@$settings->responsabile_dati)?>" class="form-control" id="responsabile_dati">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="scientifico">Comitato scientifico o Comitato per le autentiche</label>
                     <input type="text" name="settings[impostazioni_comitato][scientifico]" value="<?php echo form_value(@$settings->scientifico)?>" class="form-control" id="scientifico">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="nominativo_presidente">Nominativo Presidente</label>
                     <input type="text" name="settings[impostazioni_comitato][nominativo_presidente]" value="<?php echo form_value(@$settings->nominativo_presidente)?>" class="form-control" id="nominativo_presidente">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="foro_competente">Foro Competente</label>
                     <input type="text" name="settings[impostazioni_comitato][foro_competente]" value="<?php echo form_value(@$settings->foro_competente)?>" class="form-control" id="foro_competente">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="archivio_artista">Tipo di archivio | indicare se Archivio o Artista</label>
                     <input type="text" name="settings[impostazioni_comitato][archivio_artista]" value="<?php echo form_value(@$settings->archivio_artista)?>" class="form-control" id="archivio_artista">
                  </div>
			<div class="col-sm-3">
                     <label class="form-label" for="scopo_archivio">Scopo dell'Archivio</label>
                     <input type="text" name="settings[impostazioni_comitato][scopo_archivio]" value="<?php echo form_value(@$settings->scopo_archivio)?>" class="form-control" id="scopo_archivio">
                  </div>
			 <div class="col-sm-12">
                     <label class="form-label" for="dichiarazione_autenticita">Dichiarazione di autenticità da stampare nei certificati<br>per l'Archivio: che l’Opera di seguito individuata e meglio descritta è autentica<br>Per Autore: di essere Autore dell’Opera di seguito individuata e meglio descritta, e ne attesta l’autenticità</label>
                     <input type="text" name="settings[impostazioni_comitato][dichiarazione_autenticita]" value="<?php echo form_value(@$settings->dichiarazione_autenticita)?>" class="form-control" id="dichiarazione_autenticita">
                  </div>
			 <div class="col-sm-4">
                     <label class="form-label" for="timbro">Carica il timbro</label>
                     <input type="file" name="settings[impostazioni_comitato][timbro]" value="<?php echo form_value(@$settings->timbro)?>" class="form-control" id="timbro">
                  </div>
			<div class="col-sm-4">
                     <label class="form-label" for="timbro">Link del timbro dopo il caricamento</label>
                     <input type="text" name="settings[impostazioni_comitato][timbro]" value="<?php echo form_value(@$settings->timbro)?>" class="form-control" id="timbro">
                  </div>
				  
                </div>
            </div>
        </div>
    </div>
	  <!--
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading_foundation_accordion_2">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#foundation_accordion_2" aria-expanded="true" aria-controls="foundation_accordion_2">Impostazioni predefinite per verbali comitato</button>
        </h2>
        <div class="accordion-collapse collapse" id="foundation_accordion_2" aria-labelledby="heading_foundation_accordion_2" data-bs-parent="#foundation_accordion">
            <div class="accordion-body">
               <div class="row g-3">
			   
					<div class="col">
                     <label class="form-label" for="luogo_riunione">Luogo</label>
                     <input type="text" name="settings[impostazioni_comitato][luogo_riunione]" value="<?php echo form_value(@$settings->luogo_riunione)?>" class="form-control" id="luogo_riunione">
                  </div>
				  
				  <div class="col">
                     <label class="form-label" for="presidente_riunione">Presidente</label>
                     <input type="text" name="settings[impostazioni_comitato][presidente_riunione]" value="<?php echo form_value(@$settings->presidente_riunione)?>" class="form-control" id="presidente_riunione">
                  </div>
				  
				  <div class="col">
                     <label class="form-label" for="segretario_riunione">Segretario</label>
                     <input type="text" name="settings[impostazioni_comitato][segretario_riunione]" value="<?php echo form_value(@$settings->segretario_riunione)?>" class="form-control" id="segretario_riunione">
                  </div>
				  
               </div>
            </div>
        </div>
    </div>-->
	  
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading_foundation_accordion_3">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#foundation_accordion_3" aria-expanded="true" aria-controls="foundation_accordion_3">Impostazioni predefinite per dati Opera</button>
        </h2>
        <div class="accordion-collapse collapse" id="foundation_accordion_3" aria-labelledby="heading_foundation_accordion_3" data-bs-parent="#foundation_accordion">
            <div class="accordion-body">
               <div class="row g-3">
			   
					<div class="col">
                     <label class="form-label" for="nome_autore">Nominativo Autore</label>
                     <input type="text" name="settings[impostazioni_comitato][nome_autore]" value="<?php echo form_value(@$settings->nome_autore)?>" class="form-control" id="nome_autore">
                  </div>
				  
				  <div class="col">
                     <label class="form-label" for="data_nascita_autore">Data di nascita dell'Autore</label>
                     <input type="text" name="settings[impostazioni_comitato][data_nascita_autore]" value="<?php echo form_value(@$settings->data_nascita_autore)?>" class="form-control" id="data_nascita_autore">
                  </div>
				  
				  <div class="col">
                     <label class="form-label" for="luogo_nascita_autore">Luogo di nascita dell'Autore</label>
                     <input type="text" name="settings[impostazioni_comitato][luogo_nascita_autore]" value="<?php echo form_value(@$settings->luogo_nascita_autore)?>" class="form-control" id="luogo_nascita_autore">
                  </div>
				  
				  <div class="col">
                     <label class="form-label" for="data_morte_autore">Data di morte dell'Autore</label>
                     <input type="text" name="settings[impostazioni_comitato][data_morte_autore]" value="<?php echo form_value(@$settings->data_morte_autore)?>" class="form-control" id="data_morte_autore">
                  </div>
				  
				  <div class="col">
                     <label class="form-label" for="luogo_morte_autore">Luogo di morte dell'Autore</label>
                     <input type="text" name="settings[impostazioni_comitato][luogo_morte_autore]" value="<?php echo form_value(@$settings->luogo_morte_autore)?>" class="form-control" id="luogo_morte_autore">
                  </div>
               </div>
            </div>
         </div>
    </div>
    
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading_foundation_accordion_4">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#foundation_accordion_4" aria-expanded="true" aria-controls="foundation_accordion_4">Circolazione Internazionale</button>
        </h2>
        <div class="accordion-collapse collapse" id="foundation_accordion_4" aria-labelledby="heading_foundation_accordion_4" data-bs-parent="#foundation_accordion">
            <div class="accordion-body">
               <div class="row g-3">
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Allegato%20A1_Richiesta%20per%20il%20rilascio%20del%20Certificato%20di%20Avvenuta%20Importazione%20da%20un%20Paese%20terzo%20di%20oggetti%20di%20antichità%20e%20arte.pdf" target="_blank">Allegato A1_Richiesta per il rilascio del Certificato di Avvenuta Importazione da un Paese terzo di oggetti di antichità e arte</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Allegato%20B1_Richiesta%20per%20il%20rilascio%20del%20Certificato%20di%20Avvenuta%20Spedizione%20da%20uno%20Stato%20membro%20dell'Unione%20Europea%20di%20oggetti%20di%20antichità%20e%20arte.pdf" target="_blank">Allegato B1_Richiesta per il rilascio del Certificato di Avvenuta Spedizione da uno Stato membro dell'Unione Europea di oggetti di antichità e arte</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Allegato%20C1_Richiesta%20per%20il%20rinnovo%20del%20Certificato%20di%20Avvenuta%20Importazione%20da%20un%20Paese%20terzo%20o%20del%20Certificato%20di%20Avvenuta%20Spedizione%20da%20uno%20Stato%20membro.pdf" target="_blank">Allegato C1_Richiesta per il rinnovo del Certificato di Avvenuta Importazione da un Paese terzo o del Certificato di Avvenuta Spedizione da uno Stato membro</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Allegato%20D1_Dichiarazione%20per%20l'uscita%20di%20oggetti%20d’arte%20eseguiti%20da%20meno%20di%20settant'anni%20e%20da%20più%20di%20cinquanta.pdf" target="_blank">Allegato D1_Dichiarazione per l'uscita di oggetti d’arte eseguiti da meno di settant'anni e da più di cinquanta</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Attestato%20Circolazione%20Temporanea%20Art.%2071.doc" target="_blank">Attestato Circolazione Temporanea Art. 71</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Attestato%20Libera%20Circolazione%20Art.%2068.doc" target="_blank">Attestato Libera Circolazione Art. 68</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Autocertificazione%20per%20l'esportazione%20di%20arte%20contemporanea.doc" target="_blank">Autocertificazione per l'esportazione di arte contemporanea</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Certificato%20Avvenuta%20Importazione%20Art.%2072.doc" target="_blank">Certificato Avvenuta Importazione Art. 72</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Certificato%20Avvenuta%20Spedizione%20Art.%2072.doc" target="_blank">Certificato Avvenuta Spedizione Art. 72</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Denuncia%20per%20Attestato%20di%20Libera%20Circolazione.doc" target="_blank">Denuncia per Attestato di Libera Circolazione</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Denuncia%20per%20il%20rilascio%20Attestato%20di%20Libera%20Circolazione.doc" target="_blank">Denuncia per il rilascio Attestato di Libera Circolazione</a></p>
                    <p><a href="https://fondazione.archy.it/assets/documents/documentazione/Richiesta%20di%20esame%20preliminare.docx" target="_blank">Richiesta di esame preliminare</a></p>
               </div>
            </div>
        </div>
    </div>
		 
		 
     
   </div>

</form>
<script type="text/javascript">
window.onload=function(){
	/*$('select[name="settings[foundation_1][first_membership_length]"]').change(function(){
		var display=$(this).val()!='one_year'? 'block':'none';
		$('#days_left').css('display',display);
		$('#social_year_block, #custom_date_block').css('display','none');
		if($(this).val()=='solar_year'){
			$('#social_year_block').css('display','');
		}
		if($(this).val()=='custom_date'){
			$('#custom_date_block').css('display','');
		}
		$('input[name="settings[foundation_1][subscription_fee]"]').trigger('input');
		
	}).trigger('change');
	
	$('input[name="settings[foundation_1][subscription_fee]"]').on('input',function(){
		var daily_fee=Number($(this).val())/365;
		$('#daily_fee').text(format_amount(daily_fee));
		var days_left=$('#remaining_days').is(':visible')? Number($('#remaining_days').text()) : Number($('#custom_date_remaining_days').text());
		var amount=daily_fee*days_left;
		$('#days_left_amount').text(format_amount(amount));
	});*/
	
	$('#first_membership_length').change(function(){
		if($(this).val()=='custom_date'){
			$('#first_membership_custom').slideDown();
		}else{
			$('#first_membership_custom').slideUp();
		}
	});
	
}
</script>
