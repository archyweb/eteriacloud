<?php
$settings=$app->get_settings($domain='association_1');
$sms_enabled=$app->config['sms']['enabled']? '' : 'disabled';
?>
<form role="form" class="row g-3 needs-validation" novalidate="" action="/archy/app.php?do_action=store_settings" method="post" enctype="multipart/form-data">
    <div class="col-12 mb-3">
        <div class="row justify-content-center justify-content-sm-between">
            <div class="col-sm-auto text-center">
                <h5 class="d-inline-block p-2">Impostazioni Associazione</h5>
            </div>
            <div class="col-sm-auto d-flex justify-content-center mt-1 mt-sm-0">
                <div class="btn-group" role="group">
                    <a class="btn btn-secondary me-1 mb-1" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Reimposta modulo"><span class="fas fa-sync"></span></a>
                    <button type="submit" class="btn btn-success me-1 mb-1" data-toggle="tooltip" data-placement="left" title="Salva modifiche">Salva</button>
                </div>
            </div>	
        </div>
    </div>
    
    <div class="accordion mt-2" id="association_accordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading_association_accordion_1">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_1" aria-expanded="true" aria-controls="association_accordion_1">Termine esercizio sociale</button>
            </h2>
            <div class="accordion-collapse collapse" id="association_accordion_1" aria-labelledby="heading_association_accordion_1" data-bs-parent="#association_accordion">
                <div class="accordion-body">
                    <div class="row g-3">
                        <div class="col">
                            <div class="form-group">
                               <label class="form-label" for="company_year_day">Giorno</label>
                               <select class="form-select" name="settings[association_1][company_year_day]" id="company_year_day">
                                  <?php for($i=1; $i<=31; $i++):$selected=$i==@$settings->company_year_day? 'selected':''?>
                                  <option value="<?php echo $i?>"<?php echo $selected?>><?php echo $i?></option>
                                  <?php endfor?>
                               </select>
                            </div>
                         </div>
                         <div class="col">
                            <div class="form-group">
                               <label class="form-label" for="company_year_month">Mese</label>
                               <select class="form-control" name="settings[association_1][company_year_month]" id="company_year_month">
                                  <?php for($i=1; $i<=12; $i++):$selected=$i==@$settings->company_year_month? 'selected':''?>
                                  <option value="<?php echo $i?>"<?php echo $selected?>><?php echo $app->months[$i-1]?></option>
                                  <?php endfor?>
                               </select>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="heading_association_accordion_2">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_2" aria-expanded="true" aria-controls="association_accordion_2">Notitiche automatiche</button>
            </h2>
            <div class="accordion-collapse collapse" id="association_accordion_2" aria-labelledby="heading_association_accordion_2" data-bs-parent="#association_accordion">
                <div class="accordion-body">
                    <div class="row g-3">
                        <h5>Mittente E-mail</h5>
                        <div class="col">                            
                            <div class="form-group">
                               <label class="form-label" for="mail_sender_name">Nome Mittente</label>
                               <input type="text" name="settings[association_1][sender_name]" value="<?php echo form_value(@$settings->sender_name)?>" class="form-control" id="mail_sender_name">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                               <label class="form-label" for="mail_sender_email">E-mail Mittente</label>
                               <input type="text" name="settings[association_1][sender_email]" value="<?php echo form_value(@$settings->sender_email)?>" class="form-control" id="mail_sender_email">
                            </div>
                        </div>
                        <h5 class="mt-4">Mittente SMS</h5>
                        <div class="col-6 <?php echo $sms_enabled; ?>">                            
                            <div class="form-group">
                               <label class="form-label" for="sms_sender">Numero Mittente</label>
                               <input type="text" name="settings[association_1][sms_sender]" value="<?php echo form_value(@$settings->sms_sender)?>" class="form-control" placeholder="+39 333 1234567" id="sms_sender">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading_association_accordion_3">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#association_accordion_3" aria-expanded="true" aria-controls="association_accordion_3">Impostazioni adunanze</button>
            </h2>
            <div class="accordion-collapse collapse" id="association_accordion_3" aria-labelledby="heading_association_accordion_3" data-bs-parent="#association_accordion">
                <div class="accordion-body">
                    <div class="row g-3">
                        <h5>Adunanza predefinita</h5>
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label" for="meeting_title">Titolo Adunanza</label>
                                <input type="text" name="settings[association_1][meeting_title]" value="<?php echo form_value(@$settings->meeting_title)?>" class="form-control" id="meeting_title">
                             </div>
                        </div>
                        <div class="col">
                             <div class="form-group">
                                <label class="form-label" for="meeting_president">Presidente</label>
                                <input type="text" name="settings[association_1][meeting_president]" value="<?php echo form_value(@$settings->meeting_president)?>" class="form-control" id="meeting_president">
                             </div>
                        </div>
                        <div class="col">
                             <div class="form-group">
                                <label class="form-label" for="meeting_secretary">Segretario</label>
                                <input type="text" name="settings[association_1][meeting_secretary]" value="<?php echo form_value(@$settings->meeting_secretary)?>" class="form-control" id="meeting_secretary">
                             </div>
                        </div>
                        <div class="col">
                             <div class="form-group">
                                <label class="form-label" for="meeting_place">Luogo</label>
                                <input type="text" name="settings[association_1][meeting_place]" value="<?php echo form_value(@$settings->meeting_place)?>" class="form-control" id="meeting_place">
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
    </div>
    
</form>

<script type="text/javascript">
window.onload=function(){
	/*$('select[name="settings[association_1][first_membership_length]"]').change(function(){
		var display=$(this).val()!='one_year'? 'block':'none';
		$('#days_left').css('display',display);
		$('#social_year_block, #custom_date_block').css('display','none');
		if($(this).val()=='solar_year'){
			$('#social_year_block').css('display','');
		}
		if($(this).val()=='custom_date'){
			$('#custom_date_block').css('display','');
		}
		$('input[name="settings[association_1][subscription_fee]"]').trigger('input');
		
	}).trigger('change');
	
	$('input[name="settings[association_1][subscription_fee]"]').on('input',function(){
		var daily_fee=Number($(this).val())/365;
		$('#daily_fee').text(format_amount(daily_fee));
		var days_left=$('#remaining_days').is(':visible')? Number($('#remaining_days').text()) : Number($('#custom_date_remaining_days').text());
		var amount=daily_fee*days_left;
		$('#days_left_amount').text(format_amount(amount));
	});
	
	$('#first_membership_length').change(function(){
		if($(this).val()=='custom_date'){
			$('#first_membership_custom').slideDown();
		}else{
			$('#first_membership_custom').slideUp();
		}
	});*/
}
</script>
