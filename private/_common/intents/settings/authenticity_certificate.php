<?php
$settings=$app->get_settings($domain='authenticity_certificate');
?>
<form role="form" class="row g-3 needs-validation" novalidate="" action="<?php echo $app->abs_root?>archy/app.php?do_action=store_settings" method="post">
    <div class="col-12 mb-3">
        <div class="row justify-content-center justify-content-sm-between">
            <div class="col-sm-auto text-center">
                <h5 class="d-inline-block p-2">Modello certificato di autenticità</h5>
            </div>
            <div class="col-sm-auto d-flex justify-content-center mt-1 mt-sm-0">
                <div class="btn-group" role="group">
                    <a class="btn btn-secondary me-1 mb-1" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Reimposta modulo"><span class="fas fa-sync"></span></a>
                    <button type="submit" class="btn btn-success me-1 mb-1" data-toggle="tooltip" data-placement="left" title="Salva modifiche">Salva</button>
                </div>
            </div>	
        </div>
    </div>
    <div class="col">
        <textarea name="settings[authenticity_certificate][certificate]" data-tinymce="data-tinymce" class="tinymce d-none"><?php echo @$settings->certificate?></textarea>
    </div>
</form>
<script type="text/javascript">
window.onload=function(){
	$('.mce-edit-area iframe').css('height','400px');
}
</script>