<?php
$month_names=$app->months; // ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];
$month_amount=unserialize($data['hidden_columns']['archy_services.splitting_months']['value']);
?>
<div class="container" id="service_wrapper" style="display:none">
    <fieldset>
        <legend>Frazionamento imponibile del servizio</legend>
        <div class="row">
            <?php foreach($month_names as $m): ?>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label" for="archy_services_splitting_months"><?php echo $m; ?>
                        <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="" data-original-title="Campo richiesto"></span>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control numbers_only required" id="splitting_months[<?php echo $m; ?>]"
                            name="splitting_months[<?php echo $m; ?>]" value="<?php echo $month_amount[$m]; ?>" placeholder="Importo">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-euro"></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>           
        </div>
    </fieldset>
</div>

<?php
$sms_enabled=$app->config['sms']['enabled']? '' : 'disabled';
?>

<span class="text-bg-primary px-3 py-2 text-uppercase">Template per generazione documenti</span>

<div class="accordion mb-5" id="servicesAccordion">
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading_certificate_of_attendance_specs">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#certificate_of_attendance_specs" aria-expanded="true" aria-controls="certificate_of_attendance_specs">Template di frequenza/attestato/certificazione</button>
        </h2>
        <div class="accordion-collapse collapse" id="certificate_of_attendance_specs" aria-labelledby="heading_certificate_of_attendance_specs" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
                <div class="row g-3">
                    
                    <?php echo $app::form_element($data['hidden_columns']['archy_services.certificates_width'],"col-md-6"); ?>
                    
                    <?php echo $app::form_element($data['hidden_columns']['archy_services.certificates_height'],"col-md-6"); ?>
                    
                    <?php echo $app::form_element($data['hidden_columns']['archy_services.certificates_model'],"col-md-12"); ?>

                    <?php echo $app::form_element($data['hidden_columns']['archy_services.certificates_background_image'],"col-md-12"); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading_module_model">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#module_model" aria-expanded="true" aria-controls="module_model">Template generico</button>
        </h2>
        <div class="accordion-collapse collapse" id="module_model" aria-labelledby="heading_module_model" data-bs-parent="#servicesAccordion">
            <div class="accordion-body">
                <div class="row g-3">
                    <?php echo $app::form_element($data['hidden_columns']['archy_services.module_model'],"col-md-12"); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<span class="text-bg-primary px-3 py-2 text-uppercase">Comunicazioni</span>
<div class="accordion mb-5" id="email_specs">
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading_email_specs">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#email_specs" aria-expanded="true" aria-controls="email_specs">Template di invio</button>
        </h2>
        <div class="accordion-collapse collapse" id="email_specs" aria-labelledby="heading_email_specs" data-bs-parent="#email_specs">
            <div class="accordion-body">
                <div class="row g-3">
                    <?php echo $app::form_element($data['hidden_columns']['archy_services.service_mail_subject'],'col-md-6'); ?>   
                    <?php echo $app::form_element($data['hidden_columns']['archy_services.service_mail_body'],'col-md-12'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($_GET['id']) && $_GET['id']!=''){ ?>
<span class="text-bg-primary px-3 py-2 text-uppercase">Promemoria automatici</span>
<div class="accordion mb-5 reminders_accordion" id="eteria_reminder">
        <div class="panel-placeholder"></div>
        <?php 
            $reminder = new Reminder();                 
            echo $reminder->get('archy_services',$_GET['id']); 
        ?>        
</div>
<?php } ?>


<script type="text/javascript">
window.onload=function(){
    <?php // Frazionamento ?>
	$('#archy_services_splitting').click(function(){
		if($(this).is(':checked')){
			$('#service_wrapper').slideDown();
			$('#service_wrapper .form-control').prop('required',true);;
		}else{
			$('#service_wrapper').slideUp();
			$('#service_wrapper .form-control').prop('required',false);;
		}
	});
	if($('#archy_services_splitting').is(':checked')){
		$('#service_wrapper').slideDown();
	}else{
		$('#service_wrapper .form-control').prop('required',false);;
    }

    <?php // Toglie IVA se il servizio non è di tipo commerciale ?>
    $("#archy_services_accounting_type").change(function(){
        if(this.value=='0') { 
            $("#archy_services_tax_rate_id").parent().parent().css("color","#fff");
            $("#archy_services_tax_rate_id").parent().hide();
        } else {
            $("#archy_services_tax_rate_id").parent().parent().css("color","#333");
            $("#archy_services_tax_rate_id").parent().show();
        };
    });
    if($('#archy_services_accounting_type').val()=='0'){
        $("#archy_services_tax_rate_id").parent().parent().css("color","#fff");
        $("#archy_services_tax_rate_id").parent().hide();
    } else {
        $("#archy_services_tax_rate_id").parent().parent().css("color","#333");
        $("#archy_services_tax_rate_id").parent().show();
    }
}
</script>

<style type="text/css">
	fieldset[disabled]{
		display: none;
	}
</style>