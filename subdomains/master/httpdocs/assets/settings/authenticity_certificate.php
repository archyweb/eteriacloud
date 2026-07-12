<?php
$settings=$app->get_settings($domain='authenticity_certificate');
?>
<form role="form" action="<?php echo $app->abs_root?>archy/app.php?do_action=store_settings" method="post">
   <div class="container">
   
      <div style="float:right;padding-top:15px;">
         <button type="submit" class="btn btn-success" data-toggle="tooltip" data-placement="left" title="Salva modifiche">
            <span class="glyphicon glyphicon-floppy-disk"></span> Salva
         </button>
      </div>
      <h3 style="margin:0;padding:20px 0;">Modello certificato di autenticità</h3>
      
               
         <div class="form-group">
            <textarea name="settings[authenticity_certificate][certificate]" class="editor"><?php echo @$settings->certificate?></textarea>
         </div>
               
      
   </div>

</form>
<script type="text/javascript">
window.onload=function(){
	$('.mce-edit-area iframe').css('height','400px');
}
</script>