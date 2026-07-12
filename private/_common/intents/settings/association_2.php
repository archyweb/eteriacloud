<?php
$Placeholder = new Placeholder();
$settings=$app->get_settings($domain='association_2');
$sms_enabled=$app->config['sms']['enabled']? '' : 'disabled';
$first=(object)@$settings->first;
$second=(object)@$settings->second;
$third=(object)@$settings->third;
$first_recession=(object)@$settings->first_recession;
$second_recession=(object)@$settings->second_recession;
$third_recession=(object)@$settings->third_recession;
$first_service=(object)@$settings->first_service;
$second_service=(object)@$settings->second_service;
$third_service=(object)@$settings->third_service;
$membership=new Membership();
$fee_list=$membership->get_subscription_fee_list();
?>
<form role="form" class="row g-3 needs-validation" novalidate="" action="/archy/app.php?do_action=store_settings" method="post" enctype="multipart/form-data">
    <div class="col-12 mb-3">
        <div class="row justify-content-center justify-content-sm-between">
            <div class="col-sm-auto text-center">
                <h5 class="d-inline-block p-2">Impostazioni Associati</h5>
            </div>
            <div class="col-sm-auto d-flex justify-content-center mt-1 mt-sm-0">
                <div class="btn-group" role="group">
                    <a class="btn btn-secondary me-1 mb-1" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Reimposta modulo"><span class="fas fa-sync"></span></a>
                    <button type="submit" class="btn btn-success me-1 mb-1" data-toggle="tooltip" data-placement="left" title="Salva modifiche">Salva</button>
                </div>
            </div>	
        </div>
    </div>
  
    <div class="accordion mt-2" id="members_accordion">
        
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_1" aria-expanded="true" aria-controls="association_accordion_1">Impostazioni associato</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_1" aria-labelledby="heading_association_accordion_1" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col">
                                <div class="form-group">
                                    <label class="form-label" for="first_membership_length">Durata prima quota associativa</label>
                                    <select class="form-select" id="first_membership_length" name="settings[association_2][first_membership_length]">
                                        <!--option value="solar_year">Sino al termine dell'esercizio</option-->
                                        <option value="one_year">365 giorni</option>
                                        <option value="custom_date" <?php echo @$settings->first_membership_length=='custom_date'? 'selected':''?>>Indica una data</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="f17">Numero giorni per rinnovo posticipato</label>
                                    <input type="text" class="form-control" id="f17" name="settings[association_2][validity_after_expiration]" value="<?php echo form_value(@$settings->validity_after_expiration)?>">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="first_membership_custom" style="display:<?php echo @$settings->first_membership_length=='custom_date'? 'block':'none'?>">
                            <h5 class="d-inline-block p-3">Data scadenza quota associativa</h5>
                            <div class="col">
                                <div class="form-group">
                                    <label class="form-label" for="first_membership_custom_day">Giorno</label>
                                    <select class="form-select" name="settings[association_2][first_membership_custom_day]" id="first_membership_custom_day">
                                        <?php for($i=1; $i<=31; $i++):$selected=$i==@$settings->first_membership_custom_day? 'selected':''?>
                                        <option value="<?php echo $i?>"<?php echo $selected?>><?php echo $i?></option>
                                        <?php endfor?>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label class="form-label" for="first_membership_custom_month">Mese</label>
                                    <select class="form-select" name="settings[association_2][first_membership_custom_month]" id="first_membership_custom_month">
                                        <?php for($i=1; $i<=12; $i++):$selected=$i==@$settings->first_membership_custom_month? 'selected':''?>
                                        <option value="<?php echo $i?>"<?php echo $selected?>><?php echo $app->months[$i-1]?></option>
                                        <?php endfor?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">    
                            <h5 class="d-inline-block p-3">Importo quota associativa</h5>
                            <div class="col">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th>Categoria</th>
                                                <th>Importo annuale</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($fee_list->categories as $category_id=>$data):?>
                                            <tr>
                                                <td><?php echo $data->category?></td>
                                                <td><?php echo App::currency($data->subscription_fee)?></td>
                                            </tr>
                                            <?php endforeach?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-falcon-primary me-1 mb-1" onClick="parent.$('#menu_archy_registry_categories a').trigger('click')">Modifica categorie</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_2">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_2" aria-expanded="true" aria-controls="association_accordion_2">Modulistica</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_2" aria-labelledby="heading_association_accordion_2" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label class="form-label" for="membership_application">Domanda di adesione</label>
                                    <textarea id="membership_application" name="settings[association_2][membership_application]" data-purpose="membership" data-tinymce="data-tinymce" data-shortcodes_label="Dati Socio" class="tinymce d-none"><?php echo @$settings->membership_application?></textarea>
                                </div>								
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="membership_application">
									<p><strong>Placeholder</strong></p>
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
						</div>
						<div class="row g-3">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label class="form-label" for="privacy_policy">Informativa privacy</label>
                                    <textarea id="privacy_policy" name="settings[association_2][privacy_policy]" data-purpose="membership" data-tinymce="data-tinymce" data-shortcodes_label="Dati Socio" class="tinymce d-none"><?php echo @$settings->privacy_policy?></textarea>
                                </div>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="privacy_policy">
									<p><strong>Placeholder</strong></p>
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="d-inline-block p-2 mt-4">Comunicazioni</h5>

            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_3">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_3" aria-expanded="true" aria-controls="association_accordion_3">Comunicazione ammissione candidato</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_3" aria-labelledby="heading_association_accordion_3" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="candidate_accepted_subject">Oggetto</label>
                                    <input type="text" id="candidate_accepted_subject" name="settings[association_2][candidate_accepted_subject]" class="form-control" value="<?php echo form_value(@$settings->candidate_accepted_subject)?>">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="candidate_accepted_description">Comunicazione ammissione candidato</label>
                                    <textarea id="candidate_accepted_description" name="settings[association_2][candidate_accepted]" data-purpose="membership" data-tinymce="data-tinymce" data-shortcodes_label="Dati Socio" class="tinymce d-none"><?php echo @$settings->candidate_accepted?></textarea>
                                </div>
                            </div>
							
							<div class="col-4">
								<div class="placeholder_container" data-rel="candidate_accepted">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_candidate_accepted" class="form-select placeholder_target_candidate_accepted">
											<option value="1" data-rel="candidate_accepted">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_4">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_4" aria-expanded="true" aria-controls="association_accordion_4">Comunicazione mancata ammissione candidato</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_4" aria-labelledby="heading_association_accordion_4" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="candidate_refused_subject">Oggetto</label>
                                    <input type="text" id="candidate_refused_subject" name="settings[association_2][candidate_refused_subject]" class="form-control" value="<?php echo form_value(@$settings->candidate_refused_subject)?>">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="candidate_refused_description">Comunicazione mancata ammissione candidato</label>
                                    <textarea id="candidate_refused_description" name="settings[association_2][candidate_refused]" data-purpose="membership" data-tinymce="data-tinymce" data-shortcodes_label="Dati Socio" class="tinymce d-none"><?php echo @$settings->candidate_refused?></textarea>
                                </div>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="candidate_refused">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_candidate_refused" class="form-select placeholder_target_candidate_refused">
											<option value="1" data-rel="candidate_refused">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_5">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_5" aria-expanded="true" aria-controls="association_accordion_5">Comunicazione avvenuta cessazione associato</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_5" aria-labelledby="heading_association_accordion_5" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="candidate_refused_subject">Oggetto</label>
                                    <input type="text" id="cessation_occurred_subject" name="settings[association_2][cessation_occurred_subject]" class="form-control" value="<?php echo form_value(@$settings->cessation_occurred_subject)?>">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="cessation_occurred_description">Comunicazione avvenuta cessazione associato</label>
                                    <textarea id="cessation_occurred_description" name="settings[association_2][cessation_occurred]" data-purpose="membership" data-tinymce="data-tinymce" data-shortcodes_label="Dati Socio" class="tinymce d-none"><?php echo @$settings->cessation_occurred?></textarea>
                                </div>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="cessation_occurred">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_cessation_occurred" class="form-select placeholder_target_cessation_occurred">
											<option value="1" data-rel="cessation_occurred">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_6">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_6" aria-expanded="true" aria-controls="association_accordion_6">Comunicazione invio tessera</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_6" aria-labelledby="heading_association_accordion_6" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="membership_card_subject">Oggetto</label>
                                    <input type="text" id="membership_card_subject" name="settings[association_2][membership_card_subject]" class="form-control" value="<?php echo form_value(@$settings->membership_card_subject)?>">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="membership_card_description">Corpo email</label>
                                    <textarea id="membership_card_description" name="settings[association_2][membership_card_body]" data-purpose="membership" data-tinymce="data-tinymce" data-shortcodes_label="Dati Socio" class="tinymce d-none"><?php echo @$settings->membership_card_body?></textarea>
                                </div>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="membership_card">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_membership_card" class="form-select placeholder_target_membership_card">
											<option value="1" data-rel="membership_card">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="d-inline-block p-2 mt-4">Notifiche automatiche scadenza tessera</h5>

            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_7">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_7" aria-expanded="true" aria-controls="association_accordion_7">E-mail scadenza della tessera (1° preavviso)</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_7" aria-labelledby="heading_association_accordion_7" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="form-label" for="expiring_card_1_subject">Oggetto</label>
                                    <input type="text" id="expiring_card_1_subject" name="settings[association_2][first][expiring_card_subject]" value="<?php echo form_value(@$first->expiring_card_subject)?>" class="form-control" placeholder="Oggetto">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <textarea id="expiring_card_1_description" class="tinymce d-none" name="settings[association_2][first][expiring_card]" data-purpose="membership" data-tinymce="data-tinymce"><?php echo @$first->expiring_card?></textarea>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="expiring_card_1">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_expiring_card_1" class="form-select placeholder_target_expiring_card_1">
											<option value="1" data-rel="expiring_card_1">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                        <div class="row g-3" <?php echo $sms_enabled?>>
                            <h5 class="mt-4">SMS</h5>
                            <div class="col">
                                <textarea class="form-control sms" name="settings[association_2][first][expiring_card_SMS]" data-chars="#count" data-tinymce="data-tinymce" rows="4" maxlength="140"><?php echo @$first->expiring_card_SMS?></textarea>
                                <div class="form-text">Caratteri: <span id="count"></span> di 140</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <h5 class="mt-4">Tempistica invio notifica</h5>                            
                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="settings[association_2][first][expiring_card_days]" value="<?php echo form_value(@$first->expiring_card_days)?>" placeholder="N° di giorni" aria-describedby="expiring_card_days">
                                        <span class="input-group-text" id="expiring_card_days">giorni</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group">
                                        <select id="f3" name="settings[association_2][first][expiring_card_days_delay]" class="form-select" aria-describedby="expiring_card_days_delay">
                                            <option value="before">Prima della</option>
                                            <option value="after" <?php echo @$first->expiring_card_days_delay=='after'? 'selected':''?>>Dopo la</option>
                                        </select>
                                        <span class="input-group-text" id="expiring_card_days_delay">data di scadenza</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_8">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_8" aria-expanded="true" aria-controls="association_accordion_8">E-mail scadenza della tessera (2° preavviso)</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_8" aria-labelledby="heading_association_accordion_8" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12">                                
                                <div class="form-group">
                                    <label class="form-label" for="expiring_card_2_subject">Oggetto</label>
                                    <input type="text" id="expiring_card_2_subject" name="settings[association_2][second][expiring_card_subject]" value="<?php echo form_value(@$second->expiring_card_subject)?>" class="form-control" placeholder="Oggetto">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <textarea id="expiring_card_2_description" class="tinymce d-none" name="settings[association_2][second][expiring_card]" data-purpose="membership" data-tinymce="data-tinymce"><?php echo @$second->expiring_card?></textarea>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="expiring_card_2">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_expiring_card_2" class="form-select placeholder_target_expiring_card_2">
											<option value="1" data-rel="expiring_card_2">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                        <div class="row g-3" <?php echo $sms_enabled?>>
                            <h5 class="mt-4">SMS</h5>
                            <div class="col">
                                <textarea class="form-control sms" name="settings[association_2][second][expiring_card_SMS]" data-chars="#count" rows="4" maxlength="140"><?php echo @$second->expiring_card_SMS?></textarea>
                                <div class="form-text">Caratteri: <span id="count"></span> di 140</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <h5 class="mt-4">Tempistica invio notifica</h5>

                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="settings[association_2][second][expiring_card_days]" value="<?php echo form_value(@$second->expiring_card_days)?>" placeholder="N° di giorni" aria-describedby="expiring_card_days_2">
                                        <span class="input-group-text" id="expiring_card_days_2">giorni</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group">
                                        <select id="f3" name="settings[association_2][second][expiring_card_days_delay]" class="form-select" aria-describedby="expiring_card_days_delay_2">
                                            <option value="before">Prima della</option>
                                            <option value="after" <?php echo @$second->expiring_card_days_delay=='after'? 'selected':''?>>Dopo la</option>
                                        </select>
                                        <span class="input-group-text" id="expiring_card_days_delay_2">data di scadenza</span>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_9">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_9" aria-expanded="true" aria-controls="association_accordion_9">E-mail scadenza della tessera (3° preavviso)</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_9" aria-labelledby="heading_association_accordion_9" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12">                                
                                <div class="form-group">
                                    <label class="form-label" for="expiring_card_3_subject">Oggetto</label>
                                    <input id="expiring_card_3_subject" type="text" name="settings[association_2][third][expiring_card_subject]" value="<?php echo form_value(@$third->expiring_card_subject)?>" class="form-control" placeholder="Oggetto">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <textarea id="expiring_card_3_description" class="tinymce d-none" name="settings[association_2][third][expiring_card]" data-purpose="membership" data-tinymce="data-tinymce"><?php echo @$third->expiring_card?></textarea>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="expiring_card_3">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_expiring_card_3" class="form-select placeholder_target_expiring_card_3">
											<option value="1" data-rel="expiring_card_3">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                        <div class="row g-3" <?php echo $sms_enabled?>>
                            <h5 class="mt-4">SMS</h5>
                            <div class="col">
                                <textarea class="form-control sms" name="settings[association_2][third][expiring_card_SMS]" data-chars="#count" rows="4" maxlength="140"><?php echo @$third->expiring_card_SMS?></textarea>
                                <div class="form-text">Caratteri: <span id="count"></span> di 140</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <h5 class="mt-4">Tempistica invio notifica</h5>
                            
                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="settings[association_2][third][expiring_card_days]" value="<?php echo form_value(@$third->expiring_card_days)?>" placeholder="N° di giorni" aria-describedby="expiring_card_days_3">
                                        <span class="input-group-text" id="expiring_card_days_delay_3">giorni</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group">
                                        <select id="f3" name="settings[association_2][third][expiring_card_days_delay]" class="form-select" aria-describedby="expiring_card_days_delay_3">
                                            <option value="before">Prima della</option>
                                            <option value="after" <?php echo @$third->expiring_card_days_delay=='after'? 'selected':''?>>Dopo la</option>
                                        </select>
                                        <span class="input-group-text" id="expiring_card_days_delay_3">data di scadenza</span>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_10">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_10" aria-expanded="true" aria-controls="association_accordion_10">E-mail notifica tessera scaduta</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_10" aria-labelledby="heading_association_accordion_10" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12"> 
                                <label class="form-label" for="card_expired_subject">Oggetto</label>                            
                                <input type="text" id="card_expired_subject" name="settings[association_2][card_expired_subject]" value="<?php echo form_value(@$settings->card_expired_subject)?>" class="form-control" placeholder="Oggetto">
                            </div>
                            <div class="col-sm-8">    
                                <textarea id="card_expired_description" class="tinymce d-none" name="settings[association_2][card_expired]" data-purpose="membership" data-tinymce="data-tinymce"><?php echo @$settings->card_expired?></textarea>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="card_expired">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_card_expired" class="form-select placeholder_target_card_expired">
											<option value="1" data-rel="card_expired">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                        <div class="row g-3" <?php echo $sms_enabled?>>
                            <h5 class="mt-4">SMS</h5>
                            <div class="col">
                                <textarea class="form-control sms" name="settings[association_2][card_expired_SMS]" data-chars="#count" rows="4" maxlength="140"><?php echo @$settings->card_expired_SMS?></textarea>
                                <div class="form-text">Caratteri: <span id="count"></span> di 140</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <h5 class="mt-4">Tempistica invio notifica</h5>

                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="settings[association_2][card_expired_days]" value="<?php echo form_value(@$settings->card_expired_days)?>" placeholder="N° di giorni" aria-describedby="card_expired_days">
                                        <span class="input-group-text" id="card_expired_days">giorni</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group">
                                        <select id="f3" name="settings[association_2][card_expired_days_delay]" class="form-select" aria-describedby="card_expired_days_delay">
                                            <option value="before">Prima della</option>
                                            <option value="after" <?php echo @$settings->card_expired_days_delay=='after'? 'selected':''?>>Dopo la</option>
                                        </select>
                                        <span class="input-group-text" id="card_expired_days_delay">data di scadenza</span>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="d-inline-block p-2 mt-4">Notifiche automatiche scadenza tessera periodo posticipato</h5>

            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_11">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_11" aria-expanded="true" aria-controls="association_accordion_11">E-mail scadenza tessera periodo posticipato (1° preavviso)</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_11" aria-labelledby="heading_association_accordion_11" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12"> 
                                <label class="form-label" for="recession_1_subject">Oggetto</label> 
                                <input type="text" id="recession_1_subject" name="settings[association_2][first_recession][recession_subject]" value="<?php echo form_value(@$first_recession->recession_subject)?>" class="form-control" placeholder="Oggetto">
                            </div>
                            <div class="col-sm-8">
                                <textarea id="recession_1_description" class="tinymce d-none" name="settings[association_2][first_recession][recession]" data-purpose="membership" data-tinymce="data-tinymce"><?php echo @$first_recession->recession?></textarea>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="recession_1">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_recession_1" class="form-select placeholder_target_recession_1">
											<option value="1" data-rel="recession_1">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                        <div class="row g-3" <?php echo $sms_enabled?>>
                            <h5 class="mt-4">SMS</h5>
                            <div class="col">
                                <textarea class="form-control sms" name="settings[association_2][first_recession][recession_SMS]" data-chars="#count" rows="4" maxlength="140"><?php echo @$first_recession->recession_SMS?></textarea>
                                <div class="form-text">Caratteri: <span id="count"></span> di 140</div>
                            </div>
                        </div>                            
                        <div class="row g-3">
                            <h5 class="mt-4">Tempistica invio notifica</h5>
                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="settings[association_2][first_recession][recession_days]" value="<?php echo form_value(@$first_recession->recession_days)?>" placeholder="N° di giorni" aria-describedby="recession_days_1">
                                    <span class="input-group-text" id="recession_days_1">giorni</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <select id="f3" name="settings[association_2][first_recession][recession_days_delay]" class="form-select" aria-describedby="recession_days_delay_1">
                                        <option value="before">Prima del</option>
                                        <option value="after" <?php echo @$first_recession->recession_days_delay=='after'? 'selected':''?>>Oltre il</option>
                                    </select>
                                    <span class="input-group-text" id="recession_days_delay_1">termine dei giorni di validità oltre la scadenza (<?php echo $settings->validity_after_expiration?> giorni)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_12">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_12" aria-expanded="true" aria-controls="association_accordion_12">E-mail scadenza tessera periodo posticipato (2° preavviso)</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_12" aria-labelledby="heading_association_accordion_12" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12"> 
                                <label class="form-label" for="recession_2_subject">Oggetto</label>
                                <input type="text" id="recession_2_subject" name="settings[association_2][second_recession][recession_subject]" value="<?php echo form_value(@$second_recession->recession_subject)?>" class="form-control" placeholder="Oggetto">
                            </div>
                            <div class="col-sm-8">
                                <textarea id="recession_2_description" class="tinymce d-none" name="settings[association_2][second_recession][recession]" data-purpose="membership" data-tinymce="data-tinymce"><?php echo @$second_recession->recession?></textarea>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="recession_2">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_recession_2" class="form-select placeholder_target_recession_2">
											<option value="1" data-rel="recession_2">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                        <div class="row g-3" <?php echo $sms_enabled?>>
                            <h5 class="mt-4">SMS scadenza tessera periodo posticipato (2° preavviso)</h5>
                            <div class="col">
                                <textarea class="form-control sms" name="settings[association_2][second_recession][recession_SMS]" data-chars="#count" rows="4"  maxlength="140"><?php echo @$second_recession->recession_SMS?></textarea>
                                <div class="form-text">Caratteri: <span id="count"></span> di 140</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <h5 class="mt-4">Tempistica invio notifica</h5>                           
                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="settings[association_2][second_recession][recession_days]" value="<?php echo form_value(@$second_recession->recession_days)?>" placeholder="N° di giorni" aria-describedby="recession_days_2">
                                    <span class="input-group-text" id="recession_days_delay">giorni</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <select id="f3" name="settings[association_2][second_recession][recession_days_delay]" class="form-select" aria-describedby="recession_days_delay_2">
                                        <option value="before">Prima del</option>
                                        <option value="after" <?php echo @$second_recession->recession_days_delay=='after'? 'selected':''?>>Oltre il</option>
                                    </select>
                                    <span class="input-group-text" id="recession_days_delay_2">termine dei giorni di validità oltre la scadenza (<?php echo $settings->validity_after_expiration?> giorni)</span>
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_13">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_13" aria-expanded="true" aria-controls="association_accordion_13">E-mail scadenza tessera periodo posticipato (3° preavviso)</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_13" aria-labelledby="heading_association_accordion_13" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12"> 
                                <label class="form-label" for="recession_3_subject">Oggetto</label>
                                <input type="text" id="recession_3_subject" name="settings[association_2][third_recession][recession_subject]" value="<?php echo form_value(@$third_recession->recession_subject)?>" class="form-control" placeholder="Oggetto">
                            </div>
                            <div class="col-sm-8">
                                <textarea id="recession_3_description" class="tinymce d-none" name="settings[association_2][third_recession][recession]" data-purpose="membership" data-tinymce="data-tinymce"><?php echo @$third_recession->recession?></textarea>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="recession_3">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_recession_3" class="form-select placeholder_target_recession_3">
											<option value="1" data-rel="recession_3">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                        <div class="row g-3" <?php echo $sms_enabled?>>
                            <h5 class="mt-4">SMS</h5>
                            <div class="col">
                                <textarea class="form-control sms" name="settings[association_2][third_recession][recession_SMS]" data-chars="#count" rows="4"  maxlength="140"><?php echo @$third_recession->recession_SMS?></textarea>
                                <div class="form-text">Caratteri: <span id="count"></span> di 140</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <h5 class="mt-4">Tempistica invio notifica</h5>                           
                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="settings[association_2][third_recession][recession_days]" value="<?php echo form_value(@$third_recession->recession_days)?>" placeholder="N° di giorni" aria-describedby="recession_days_3">
                                    <span class="input-group-text" id="recession_days_3">giorni</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <select id="f3" name="settings[association_2][third_recession][recession_days_delay]" class="form-select" aria-describedby="recession_days_delay_3">
                                        <option value="before">Prima del</option>
                                        <option value="after" <?php echo @$third_recession->recession_days_delay=='after'? 'selected':''?>>Oltre il</option>
                                    </select>
                                    <span class="input-group-text" id="recession_days_delay_3">termine dei giorni di validità oltre la scadenza (<?php echo $settings->validity_after_expiration?> giorni)</span>
                                </div>
                            </div>                           
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_14">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_14" aria-expanded="true" aria-controls="association_accordion_14">E-mail notifica tessera scaduta periodo posticipato</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_14" aria-labelledby="heading_association_accordion_14" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-sm-12"> 
                                <label class="form-label" for="recession_expired_subject">Oggetto</label>
                                <input type="text" id="recession_expired_subject" name="settings[association_2][recession_subject]" value="<?php echo form_value(@$settings->recession_subject)?>" class="form-control" placeholder="Oggetto">
                            </div>
                            <div class="col-sm-8">
                                <textarea id="recession_expired_description" class="tinymce d-none" name="settings[association_2][recession]" data-purpose="membership" data-tinymce="data-tinymce"><?php echo @$settings->recession?></textarea>
                            </div>
							<div class="col-4">
								<div class="placeholder_container" data-rel="recession_expired">
									<p><strong>Placeholder</strong></p>
									<p>
										<label>Seleziona dove inserire il placeholder</label> 
										<select name="placeholder_target_recession_expired" class="form-select placeholder_target_recession_expired">
											<option value="1" data-rel="recession_expired">Oggetto</option>
											<option value="2" selected>Editor</option>
										</select>
									</p>
        
									<?php echo $Placeholder->getAvailablePlaceholders('settings'); ?>
								</div>
							</div>
                        </div>
                        <div class="row g-3" <?php echo $sms_enabled?>>
                            <h5 class="mt-4">SMS</h5>
                            <div class="col">
                                <textarea class="form-control sms" name="settings[association_2][recession_SMS]" data-chars="#count" rows="4"  maxlength="140"><?php echo @$settings->recession_SMS?></textarea>
                                <div class="form-text">Caratteri: <span id="count"></span> di 140</div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <h5 class="mt-4">Tempistica invio notifica</h5>                            
                            <div class="col">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="settings[association_2][recession_days]" value="<?php echo form_value(@$settings->recession_days)?>" placeholder="N° di giorni" aria-describedby="recession_days">
                                    <span class="input-group-text" id="recession_days">giorni</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <select id="f3" name="settings[association_2][recession_days_delay]" class="form-select" aria-describedby="recession_days_delay">
                                        <option value="before">Prima del</option>
                                        <option value="after" <?php echo @$settings->recession_days_delay=='after'? 'selected':''?>>Oltre il</option>
                                    </select>
                                    <span class="input-group-text" id="recession_days_delay">termine dei giorni di validità oltre la scadenza (<?php echo $settings->validity_after_expiration?> giorni)</span>
                                </div>
                            </div>                           
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="d-inline-block p-2 mt-4">Opzioni tessera</h5>
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_15">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_15" aria-expanded="true" aria-controls="association_accordion_15">Opzioni</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_15" aria-labelledby="heading_association_accordion_15" data-bs-parent="#members_accordion">
                    <div class="accordion-body">                      
                        <div class="row g-3">
                            <div class="col">
                                    <label class="form-label" for="card_1">Prefisso numerazione</label>
                                    <input type="text" name="settings[association_2][card_prefix]" 
                                        value="<?php echo form_value(@$settings->card_prefix)?>" 
                                        class="form-control" id="card_1" <?php echo @$settings->card_prefix!=''? '':''?>>
                            </div>
                            <div class="col">
                                    <label class="form-label" for="card_2">Lunghezza numero</label>
                                    <input type="text" name="settings[association_2][card_number_length]" 
                                        value="<?php echo form_value(@$settings->card_number_length)?>" 
                                        class="form-control integers_only" id="card_2">
                            </div>
                            <div class="col">
                                    <label class="form-label" for="card_3">Numero prima tessera</label>
                                    <input type="text" name="settings[association_2][card_first_number]" 
                                        value="<?php echo isset($settings->card_first_number)? form_value($settings->card_first_number) : '1'?>" 
                                        class="form-control integers_only" id="card_3">
                            </div>
                        </div>                       
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_association_accordion_16">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_16" aria-expanded="true" aria-controls="association_accordion_16">Specifiche tessere</button>
                </h2>
                <div class="accordion-collapse collapse" id="association_accordion_16" aria-labelledby="heading_association_accordion_16" data-bs-parent="#members_accordion">
                    <div class="accordion-body">
                       
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="card_width">Larghezza (mm)</label>
                                <input type="text" name="settings[association_2][card_width]" 
                                        value="<?php echo form_value(@$settings->card_width)?>" 
                                        class="form-control" id="card_width" <?php echo @$settings->card_width!=''? '':''?>>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="card_height">Altezza (mm)</label>
                                <input type="text" name="settings[association_2][card_height]" 
                                        value="<?php echo form_value(@$settings->card_height); ?>" 
                                        class="form-control" id="card_height">
                            </div>                            

                            <div class="col-sm-12">
                                <label class="form-label" for="card_template">Template</label>
                                <textarea id="card_template" name="settings[association_2][card_template]" 
                                    data-purpose="membership" data-tinymce="data-tinymce" 
                                    data-shortcodes_label="Composizione tessera" 
                                    class="tinymce d-none"><?php echo @$settings->card_template; ?>
                                </textarea>
                            </div>

                            <div class="col-sm-12">
                                <label class="form-label" for="card_css">CSS personalizzato</label>
                                <textarea id="card_css" name="settings[association_2][card_css]" 
                                    data-purpose="css" data-tinymce="data-tinymce"
                                    data-shortcodes_label="Esempi classe/stile" 
                                    class="tinymce d-none"><?php echo @$settings->card_css; ?>
                                </textarea>
                            </div>

                            <div class="col-sm-12">
                                <label class="form-label" for="card_background_image">Layout grafico</label>
                                <input type="file" name="settings[association_2][card_background_image]" 
                                    class="form-control" id="card_background_image">
                                <?php if(isset($settings->card_background_image)): ?>   
                                    <img id="card_background_image_src" src="<?php echo form_value(@$settings->card_background_image)?>" style="max-width:350px; max-height: 250px;">
                                    <button id="card_background_image_remove" type="button" class="btn btn-light" data-toggle="tooltip" data-placement="right" title="Elimina layout grafico">
                                        <span class="far fa-trash-alt"></span> Elimina
                                    </button>
                                    <div id="card_background_image_msg_wrapper"></div>
                                <?php endif; ?>   
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>           
        </div>

</form>

<script type="text/javascript">
window.onload=function(){
	$('#first_membership_length').change(function(){
		if($(this).val()=='custom_date'){
			$('#first_membership_custom').slideDown();
		}else{
			$('#first_membership_custom').slideUp();
		}
	});

    $('#card_background_image_remove').click(function(){
        if (confirm('Cancellare definitivamente?')) {
            var domain='association_2';
            var name='card_background_image';
            var filename=$('#card_background_image_src').attr('src');
            $.ajax({
                type: "POST",
                url: root + "archy/app.php?do_action=delete_attachment_settings",
                data: 'domain=' + domain + '&name=' + name + '&filename=' + filename,
                cache: false,
                success: function(html) {
                    $('#card_background_image_msg_wrapper').append('Cancellato!');
                }
            });
            $('#card_background_image_src').attr('src','');
            $(this).hide();
        }
    });
}
</script>
