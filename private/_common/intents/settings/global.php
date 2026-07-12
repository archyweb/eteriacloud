<?php
$settings=$app->get_settings($domain='notifications');
?>
<form role="form" class="row g-3 needs-validation" novalidate="" action="/archy/app.php?do_action=store_settings" method="post">
   
    <div class="col-12 mb-3">
        <div class="row justify-content-center justify-content-sm-between">
            <div class="col-sm-auto text-center">
                <h5 class="d-inline-block p-2">Impostazioni Generali</h5>
            </div>
            <div class="col-sm-auto d-flex justify-content-center mt-1 mt-sm-0">
                <div class="btn-group" role="group">
                    <a class="btn btn-secondary me-1 mb-1" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Reimposta modulo"><span class="fas fa-sync"></span></a>
                    <button type="submit" class="btn btn-success me-1 mb-1" data-toggle="tooltip" data-placement="left" title="Salva modifiche">Salva</button>
                </div>
            </div>	
        </div>
    </div>
      
    <span class="text-bg-primary px-3 py-2 text-uppercase">Mittente E-mail</span>
    <div class="col-sm-6">
       <label class="form-label" for="mail_sender_name">Nome Mittente</label>
       <input type="text" name="settings[notifications][sender_name]" value="<?php echo form_value(@$settings->sender_name)?>" class="form-control" id="mail_sender_name">
    </div>
    <div class="col-sm-6">
       <label class="form-label" for="mail_sender_email">E-mail Mittente</label>
       <input type="text" name="settings[notifications][sender_email]" value="<?php echo form_value(@$settings->sender_email)?>" class="form-control" id="mail_sender_email">
    </div>
            
    <span class="text-bg-primary px-3 py-2 text-uppercase">SMTP</span>
    <div class="col-sm-4">
       <label class="form-label" for="host">Host</label>
       <input type="text" name="settings[notifications][smtp_host]" value="<?php echo form_value(@$settings->smtp_host)?>" class="form-control" id="host">
    </div>
    <div class="col-sm-4">
       <label class="form-label" for="host_user">Username</label>
       <input type="text" name="settings[notifications][smtp_username]" value="<?php echo form_value(@$settings->smtp_username)?>" class="form-control" id="host_user">
    </div>
    <div class="col-sm-4">
       <label class="form-label" for="host_pswd">Password</label>
       <input type="password" name="settings[notifications][smtp_password]" value="<?php echo form_value(@$settings->smtp_password)?>" class="form-control" id="host_pswd">
    </div>
    <div class="col-sm-6">
       <label class="form-label" for="secure">Criptazione</label>
       <select name="settings[notifications][smtp_secure]" value="<?php echo form_value(@$settings->smtp_secure)?>" class="form-select" id="secure">
          <option value="ssl">SSL</option>
          <option value="tls" <?php echo @$settings->smtp_secure=='tls'? 'selected':''?>>TLS</option>
       </select>
    </div>
    <div class="col-sm-6">
       <label class="form-label" for="port">Porta</label>
       <input type="text" name="settings[notifications][smtp_port]" value="<?php echo form_value(@$settings->smtp_port)?>" class="form-control" id="port">
    </div>
    <div class="col-sm-6">
       <label class="form-label" for="port">Numero massimo di email inviate per sessione</label>
       <input type="text" name="settings[notifications][smtp_totalmail]" value="<?php echo form_value(@$settings->smtp_totalmail)?>" class="form-control" id="port">
       <div class="form-text">(da 0 a 10000. 10 se non specificato)</div>
    </div>
    <div class="col-sm-6">
       <label class="form-label" for="port">Numero di secondi di ritardo, durante l'invio, tra una mail e l'altra</label>
       <input type="text" name="settings[notifications][smtp_delay]" value="<?php echo form_value(@$settings->smtp_delay)?>" class="form-control" id="port">
       <div class="form-text">(da 0 a 30. 1 se non specificato)</div>
    </div>

    <span class="text-bg-primary px-3 py-2 text-uppercase">Invia e-mail di prova</span>
    <div class="col-sm-6">
        <div class="input-group">
            <input type="text" class="form-control" id="test_recipient" placeholder="Indirizzo e-mail">
            <button type="button" class="btn btn-outline-secondary" id="send_test_mail">Invia</button>
        </div>
    </div>


         <!--fieldset <?php //echo $sms_disabled?>>
            <legend>Mittente SMS</legend>
            <div class="form-group">
               <label for="sms_sender">Numero Mittente</label>
               <input type="text" name="settings[notifications][sms_sender]" value="<?php echo form_value(@$settings->sms_sender)?>" class="form-control" placeholder="+39 333 1234567" id="sms_sender">
            </div>
         </fieldset-->


</form>

<script type="text/javascript">
window.onload=function(){
	$('#send_test_mail').click(function(){
		if($('#test_recipient').val()!=''){
			$.ajax({
				type: "POST",
				url: "/archy/app.php?do_action=test_email_credentials",
				data: 'recipient='+$('#test_recipient').val(),
				cache: false,
				success: function(response){
					//console.log(response);
					$('#send_test_mail').prop('disabled',false).removeClass('disabled');
					if(response=='1'){
						alert('Tentativo di invio mail eseguito');
					}else{
						alert('Tentativo di invio mail fallito');
					}
				},
				beforeSend: function(){
					$('#send_test_mail').prop('disabled',true).addClass('disabled');
				}
			});
		}else{
			alert('Indica un indirizzo cui inviare la mail di prova');
			$('#test_recipient').focus();
		}
	});
}
</script>