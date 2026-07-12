<?php
$form=$data['hidden_columns'];
?>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Condition Report</a></li>
    <li class="nav-item"><a class="nav-link" id="final-report-tab" data-bs-toggle="tab" href="#tab-final-report" role="tab" aria-controls="tab-final-report" aria-selected="false">Modello certificato</a></li>
</ul>
<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-meeting" role="tabpanel" aria-labelledby="meeting-tab">
	<div class="row g-3 mb-3">
<?php
if (isset($form['archy_art_condition_report.condition_report_title'])) {
    echo $app::form_element($form['archy_art_condition_report.condition_report_title'], "col-md-3");
}

if (isset($form['archy_art_condition_report.opera_id'])) {
    echo $app::form_element($form['archy_art_condition_report.opera_id'], "col-md-3");
}

if (isset($form['archy_art_condition_report.model_id'])) {
    echo $app::form_element($form['archy_art_condition_report.model_id'], "col-md-3");
}

if (isset($form['archy_art_condition_report.data'])) {
    echo $app::form_element($form['archy_art_condition_report.data'], "col-md-3");
}
?>
</div>
        <div class="accordion mt-2" id="meeting_accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_1" aria-expanded="true" aria-controls="meeting_accordion_1">STATO DI CONSERVAZIONE DELL'OPERA</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_1" aria-labelledby="heading_meeting_accordion_1" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                        <div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Alterazione cromatica</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.alterazione_cromatica_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.alterazione_cromatica_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Concrezione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.concrezione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.concrezione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Corrosione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.corrosione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.corrosione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Ossidazione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.ossidazione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.ossidazione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Croste</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.croste_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.croste_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Deformazioni o ondulazioni</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.deformazioni_ondulazioni_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.deformazioni_ondulazioni_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Degradazione differenziata</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.degradazione_differenziata_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.degradazione_differenziata_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Deposito superficiale</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.deposito_superficiale_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.deposito_superficiale_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Difetti di fusione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.difetti_fusione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.difetti_fusione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Disgregazione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.disgregazione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.disgregazione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Distacchi</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.distacchi_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.distacchi_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Efflorescenza</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.efflorescenza_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.efflorescenza_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Erosione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.erosione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.erosione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Esfoliazione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.esfoliazione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.esfoliazione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Fratturazione o fessurazione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.fratturazione_fessurazione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.fratturazione_fessurazione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Incrostazione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.incrostazione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.incrostazione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Lacune</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.lacune_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.lacune_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Macchie</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.macchie_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.macchie_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Tagli, fori o lacerazioni</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.tagli_fori_lacerazioni_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.tagli_fori_lacerazioni_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Sbiadimento o opacizzazione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.sbiadimento_opacizzazione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.sbiadimento_opacizzazione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Pellicola</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.pellicola_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.pellicola_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Pitting</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.pitting_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.pitting_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Polverizzazione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.polverizzazione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.polverizzazione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Restauri precedenti</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.restauri_precedenti_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.restauri_precedenti_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Rigonfiamenti o sollevamento</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.rigonfiamenti_sollevamento_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.rigonfiamenti_sollevamento_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Scagliatura</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.scagliatura_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.scagliatura_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Sconnessioni</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.sconnessioni_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.sconnessioni_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Vandalismi</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.vandalismi_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.vandalismi_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Integrazioni</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.integrazioni_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.integrazioni_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Patina</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.patina_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.patina_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Infestazione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.infestazione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.infestazione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Ingiallimento o imbrunimento</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.ingiallimento_imbrunimento_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.ingiallimento_imbrunimento_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Infezione</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.infezione_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.infezione_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Residui</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.residui_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.residui_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Distorsioni</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.distorsioni_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.distorsioni_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Abrasioni</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.abrasioni_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.abrasioni_montaggio'],"col-md-5");
               ?>
			   
<div class="col-xs-12"><hr></div>

<div class="col-lg-2 testo"><!--appertura  colonna con il nome e decrizzione -->
<p>Grinze e pieghe</p>
</div>		
               <?php
				echo $app::form_element($form['archy_art_condition_report.grinze_pieghe_sup_primario'],"col-md-5");
			   echo $app::form_element($form['archy_art_condition_report.grinze_pieghe_montaggio'],"col-md-5");
               ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_meeting_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#meeting_accordion_2" aria-expanded="true" aria-controls="meeting_accordion_2">MOVIMENTAZIONE DELL'OPERA</button>
                </h2>
                <div class="accordion-collapse collapse" id="meeting_accordion_2" aria-labelledby="heading_meeting_accordion_2" data-bs-parent="#meeting_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                        <div class="col-lg-12 testo">
<p>TRASPORTO INIZIO [MOSTRA O ALTRO EVENTO]</p>
</div>
			   		   	
               <?php
				echo $app::form_element($form['archy_art_condition_report.ti_imballato_il'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.ti_numero_contenitore'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.ti_contenitore_condizioni'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.ti_imballaggio_condizioni'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.ti_tipo_riempimento'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.ti_condizioni_partenza'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.ti_danni_riscontrati'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.ti_nome_accompagnatore'],"col-md-4");
			    echo $app::form_element($form['archy_art_condition_report.ti_nome_ricevente'],"col-md-4");
               ?>
			   
<div class="col-lg-12 testo">
<p>TRASPORTO FINE [MOSTRA O ALTRO EVENTO]</p>
</div>
			   		   	
               <?php
				echo $app::form_element($form['archy_art_condition_report.tf_data_partenza'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.tf_data_arrivo'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.tf_disimballato_il'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.tf_condizioni_arrivo'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.tf_danni_riscontrati'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.tf_nome_accompagnatore'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.tf_nome_ricevente'],"col-md-4");
               ?>

<div class="col-lg-12 testo">
<p>RICONSEGNA</p>
</div>
			   		   	
               <?php
				echo $app::form_element($form['archy_art_condition_report.r_data_partenza'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.r_data_arrivo'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.r_disimballato_il'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.r_condizioni_arrivo'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.r_danni_riscontrati'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.r_nome_accompagnatore'],"col-md-4");
			   echo $app::form_element($form['archy_art_condition_report.r_nome_ricevente'],"col-md-4");
               ?>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
            <?php echo $app::form_element($form['archy_art_condition_report.model_template'],"col-md-12"); ?>
        </div>
    </div>
</div>