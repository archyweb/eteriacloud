<?php
$settings=$app->get_settings($domain='notifications');
?>
<form role="form" action="<?php echo $app->abs_root?>archy/app.php?do_action=store_settings" method="post">
   
   <div class="container">
   
      <div style="float:right;padding-top:15px;">
         <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="left" title="Salva modifiche">
            <span class="glyphicon glyphicon-floppy-disk"></span> Salva
         </button>
      </div>
      
      <h3 style="margin:0;padding:20px 0;">Impostazioni Generali</h3>
      
      <ul class="nav nav-tabs">
         <li class="active"><a href="#sender" data-toggle="tab">Mittente E-mail</a></li>
      </ul>
      
      <div class="settings">
      	<div class="tab-content">
      		
            <div class="tab-pane active" id="sender">
               <fieldset>
                  <legend>Mittente E-mail</legend>
                  <div class="form-group">
                     <label for="mail_sender_name">Nome Mittente</label>
                     <input type="text" name="settings[notifications][sender_name]" value="<?php echo form_value(@$settings->sender_name)?>" class="form-control" id="mail_sender_name">
                  </div>
                  <div class="form-group">
                     <label for="mail_sender_email">E-mail Mittente</label>
                     <input type="text" name="settings[notifications][sender_email]" value="<?php echo form_value(@$settings->sender_email)?>" class="form-control" id="mail_sender_email">
                  </div>
                  <legend>SMTP</legend>
                  <div class="form-group">
                     <label for="host">Host</label>
                     <input type="text" name="settings[notifications][smtp_host]" value="<?php echo form_value(@$settings->smtp_host)?>" class="form-control" id="host">
                  </div>
                  <div class="form-group">
                     <label for="host_user">Username</label>
                     <input type="text" name="settings[notifications][smtp_username]" value="<?php echo form_value(@$settings->smtp_username)?>" class="form-control" id="host_user">
                  </div>
                  <div class="form-group">
                     <label for="host_pswd">Password</label>
                     <input type="password" name="settings[notifications][smtp_password]" value="<?php echo form_value(@$settings->smtp_password)?>" class="form-control" id="host_pswd">
                  </div>
                  <div class="form-group">
                     <label for="secure">Criptazione</label>
                     <select name="settings[notifications][smtp_secure]" value="<?php echo form_value(@$settings->smtp_secure)?>" class="form-control" id="secure">
                        <option value="ssl">SSL</option>
                        <option value="tls" <?php echo @$settings->smtp_secure=='tls'? 'selected':''?>>TLS</option>
                     </select>
                  </div>
                  <div class="form-group">
                     <label for="port">Porta</label>
                     <input type="text" name="settings[notifications][smtp_port]" value="<?php echo form_value(@$settings->smtp_port)?>" class="form-control" id="port">
                  </div>
               </fieldset>
               <!--fieldset <?php //echo $sms_disabled?>>
                  <legend>Mittente SMS</legend>
                  <div class="form-group">
                     <label for="sms_sender">Numero Mittente</label>
                     <input type="text" name="settings[notifications][sms_sender]" value="<?php echo form_value(@$settings->sms_sender)?>" class="form-control" placeholder="+39 333 1234567" id="sms_sender">
                  </div>
               </fieldset-->
            </div>


            
         </div>
         
      </div>
            
   </div>

</form>