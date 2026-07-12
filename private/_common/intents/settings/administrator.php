<?php
$settings=$app->get_settings($domain='administrator');
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
         <li class="active"><a href="#members" data-toggle="tab">SMS</a></li>
      </ul>
      
      <div class="settings" id="form_message"><!-- #form_message loads editor shortcodes -->
         
         <div class="tab-content">
            
            <div class="tab-pane active" id="members">
               <fieldset>
               	<legend>Abilita SMS</legend>
                  <div class="form-group">
                     <select id="f1" name="settings[administrator][enable_sms]" class="form-control">
                        <option value="false">Disabilita</option>
                        <option value="true" <?php echo @$settings->enable_sms=='true'? 'selected':''?>>Abilita</option>
                     </select>
                  </div>                  
               </fieldset>
            </div>
            
         </div>
         
      </div><!-- /.settings -->
      
   </div>

</form>
<style type="text/css">
.mce-edit-area iframe{
	height:350px !important;
}
</style>