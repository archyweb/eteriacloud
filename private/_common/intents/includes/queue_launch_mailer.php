<?php
if(isset($_GET['id'])):
$message_detail=mysqli_fetch_object($app->db->query("select message from archy_messages WHERE id='".$_GET['id']."'"));


echo  $message_detail->message;

endif;
?>


<!--
<button type="button" class="btn btn-sm btn-default" onClick="$('#mailer').slideToggle();">Coda di invio SMTP</button>
<div style="display:none; padding-top:20px; position:relative;" id="mailer">
    <form method="post" role="form" action="/archy/app.php?do_action=launch_mailer" class="form-inline" onSubmit="return confirm('Si è certi di voler rischedulare l'invio?')">
        <button type="submit" class="btn btn-primary">Rielabora coda di invio</button>
    </form>

    <p style="margin-top:25px;">L'esecuzione verrà schedulata ed elaborata dopo qualche minuto.</p>
</div>
<hr>-->
