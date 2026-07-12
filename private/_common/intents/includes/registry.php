<div class="row row-cols-lg-5 g-3 align-items-center">

   <div class="col-sm-6">
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" id="inlineFormCheckReg" type="checkbox" checked disabled/>
            <label class="mb-0 form-check-label" for="inlineFormCheckReg">Anagrafica generica</label>
        </div>
   
   
   <?php	
   $is_customer=(bool)$data['hidden_columns']['archy_registry.is_customer']['value']?>
   
        <div class="form-check form-switch mb-0">
            <input class="form-check-input form-check-input-add" id="archy_registry_is_customer" data-target="#tab-customer" type="checkbox"<?php echo $is_customer?' checked':''?>/>
            <input type="hidden" name="main[is_customer]" value="<?php echo $is_customer?'1':'0'?>">
            <label class="mb-0 form-check-label" for="archy_registry_is_customer">Cliente</label>
        </div>
  
   
   <?php	
   $is_supplier=(bool)$data['hidden_columns']['archy_registry.is_supplier']['value']?>
   
        <div class="form-check form-switch mb-0">
            <input class="form-check-input form-check-input-add" id="archy_registry_is_supplier" data-target="#tab-supplier" type="checkbox"<?php echo $is_supplier?' checked':''?>/>
            <input type="hidden" name="main[is_supplier]" value="<?php echo $is_supplier?'1':'0'?>">
            <label class="mb-0 form-check-label" for="archy_registry_is_supplier">Fornitore</label>
        </div>
  
   
   <?php if($app->config['association']===true):?>
   <?php	
    $is_member=(bool)$data['hidden_columns']['archy_registry.is_member']['value']?>
    
        <div class="form-check form-switch mb-0">
            <input class="form-check-input form-check-input-add" id="archy_registry_is_member" data-target="#tab-member" type="checkbox"<?php echo $is_member?' checked':''?>/>
            <input type="hidden" name="main[is_member]" value="<?php echo $is_member?'1':'0'?>">
            <label class="mb-0 form-check-label" for="archy_registry_is_member">Anagrafica pubblica</label>
        </div>
    
   <?php endif; ?>
    </div>
</div>

<ul class="nav nav-tabs" id="registryTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="registry-tab" data-bs-toggle="tab" href="#tab-registry" role="tab" aria-controls="tab-registry" aria-selected="true">Dati anagrafici</a></li>
    <li id="customer-wrap-tab" class="nav-item"><a class="nav-link disabled" id="customer-tab" data-bs-toggle="tab" href="#tab-customer" role="tab" aria-controls="tab-customer" aria-selected="false">Cliente</a></li>
    <li id="supplier-wrap-tab" class="nav-item"><a class="nav-link disabled" id="supplier-tab" data-bs-toggle="tab" href="#tab-supplier" role="tab" aria-controls="tab-supplier" aria-selected="false">Fornitore</a></li>
    <li id="member-wrap-tab" class="nav-item"><a class="nav-link disabled" id="member-tab" data-bs-toggle="tab" href="#tab-member" role="tab" aria-controls="tab-member" aria-selected="false">Anagrafica pubblica</a></li>
    <li style="display:none" class="nav-item" id="additional-field-tab"><a class="nav-link" id="additional-fields-tab" data-bs-toggle="tab" href="#tab-additional-fields" role="tab" aria-controls="tab-additional-fields" aria-selected="false">Campi aggiuntivi</a></li>
    <li style="display:none" class="nav-item" id="activities-tab"><a class="nav-link" id="activities-tab" data-bs-toggle="tab" href="#tab-activities" role="tab" aria-controls="tab-activities" aria-selected="false">Attività</a></li>
</ul>

<div class="tab-content border border-top-0 p-3" id="registryTabContent">
    
    <!-- ANAGRAFICA GENERICA -->
    <div class="tab-pane fade show active" id="tab-registry" role="tabpanel" aria-labelledby="registry-tab">
        <div class="mb-3"><span class="text-bg-primary px-3 py-2 w-100 text-uppercase">Dati Anagrafici</span></div>
        <div class="row g-3">
            <?php 
                echo $app::form_element($data['hidden_columns']['archy_registry.legal_personality'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.firm_name'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.name'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.surname'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.vat_country_id'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.vat_id'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.tax_code'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.city_id'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.zip'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.address'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.gender'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.birth_date'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.birth_place'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.main_picture'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.notes'],"col-md-4");
            ?>
        </div>

        <hr>
        <div class="mb-3 mt-3 pt-2"><span class="text-bg-primary px-3 py-2 w-100 text-uppercase">Contatti</span></div>
        <div class="row g-3">
            <?php 
                echo $app::form_element($data['hidden_columns']['archy_registry.email'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.certified_email'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.website'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.telephone'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.fax'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.mobile'],"col-md-4");
            ?>
        </div>

        <hr>
        <div class="mb-3 mt-3 pt-2"><span class="text-bg-primary px-3 py-2 w-100 text-uppercase">Dati aggiuntivi</span></div>
        <div class="row g-3">
            <?php 
                echo $app::form_element($data['hidden_columns']['archy_registry.category_id'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.contact_person'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.contact_person_is_legal_representative'],"col-md-4");
            ?>

        </div>
    </div>
    
    <!-- CLIENTE -->
    <div class="tab-pane fade" id="tab-customer" role="tabpanel" aria-labelledby="customer-tab">
        <div class="mb-3"><span class="text-bg-primary px-3 py-2 w-100 text-uppercase">Cliente</span></div>
        <div class="row g-3">
            <?php
                    echo $app::form_element($data['hidden_columns']['archy_registry.customer_category_id'],'col-md-4');
                    echo $app::form_element($data['hidden_columns']['archy_registry.customer_signatory'],'col-md-4');
                    echo $app::form_element($data['hidden_columns']['archy_registry.customer_contact_person'],'col-md-4');
                    echo $app::form_element($data['hidden_columns']['archy_registry.customer_contact_person_is_legal_representative'],'col-md-4');
                    echo $app::form_element($data['hidden_columns']['archy_registry.customer_notes'],'col-md-4');
            ?>
        </div>
        <hr>
        <div class="mb-3 mt-3 pt-2"><span class="text-bg-primary px-3 py-2 w-100 text-uppercase">Rappresentante Fiscale</span></div>
        <div class="row g-3">
                <?php
                echo $app::form_element($data['hidden_columns']['archy_registry.customer_tax_representative_firm_name'],'col-md-8');
                echo $app::form_element($data['hidden_columns']['archy_registry.customer_tax_representative_vat_country_id'],'col-md-2');
                echo $app::form_element($data['hidden_columns']['archy_registry.customer_tax_representative_vat_id'],'col-md-2');
                ?>
        </div>
        <hr>
        <div class="mb-3 mt-3 pt-2"><span class="text-bg-primary px-3 py-2 w-100 text-uppercase">Fatturazione elettronica</span></div>
        <div class="row g-3">
                <?php
                echo $app::form_element($data['hidden_columns']['archy_registry.customer_private'],'col-md-3');
                echo $app::form_element($data['hidden_columns']['archy_registry.customer_sending_method'],'col-md-3 sending_method');
                echo $app::form_element($data['hidden_columns']['archy_registry.customer_recipient_code'],'col-md-3 recipient_code');
                echo $app::form_element($data['hidden_columns']['archy_registry.customer_sdi_pec'],'col-md-3 sdi_pec');
                ?>
        </div>
        <div class="row g-3">
                <?php
                echo $app::form_element($data['hidden_columns']['archy_registry.customer_EORI'],'col-md-3');
                ?>
        </div> 
    </div>
    
    <!-- FORNITORE -->
    <div class="tab-pane fade" id="tab-supplier" role="tabpanel" aria-labelledby="supplier-tab">
        <div class="row g-3">
            <?php
                echo $app::form_element($data['hidden_columns']['archy_registry.supplier_category_id'],'col-md-3');
                echo $app::form_element($data['hidden_columns']['archy_registry.supplier_contact_person'],'col-md-3 sending_method');
                echo $app::form_element($data['hidden_columns']['archy_registry.supplier_contact_person_is_legal_representative'],'col-md-3 recipient_code');
                echo $app::form_element($data['hidden_columns']['archy_registry.supplier_notes'],'col-md-3 sdi_pec');
            ?>                
        </div>
    </div>
    <div class="tab-pane fade" id="tab-member" role="tabpanel" aria-labelledby="member-tab">
        <div class="row g-3">
            <div class="col-md-6">
                  <button type="button" class="btn btn-primary btn-sm" id="copy_address">Importa dati anagrafici dalla scheda principale</button>
            </div>
        </div>
        <div class="row g-3">
            <?php 
                echo $app::form_element($data['hidden_columns']['archy_registry.member_published'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.member_public_name'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.member_public_surname'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.member_public_address'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.member_public_zip'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.member_public_city_id'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.latitude'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.longitude'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.member_public_phone'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.member_public_mail'],"col-md-4");
                echo $app::form_element($data['hidden_columns']['archy_registry.member_public_website'],"col-md-4");
            ?>
        </div>
    </div>
    
    <!-- CAMPI AGGIUNTIVI -->
    <div class="tab-pane fade" id="tab-additional-fields" role="tabpanel" aria-labelledby="additional-fields-tab">
        <div class="row g-3">
            <?php $app->additional_fields('form'); ?>
        </div>
    </div>
    
    <!-- ATTIVITA (RECUPERARE TAB DA PRODUZIONE VECCHIA -->
    
</div>

<script type="text/javascript">
    
    document.addEventListener("DOMContentLoaded", function () {
        manageFields(
        "archy_registry_legal_personality", {
            0: [
                {id: "archy_registry_name", required:true},
                {id: "archy_registry_vat_country_id", required:false},
                {id: "archy_registry_vat_id", required:false},
                {id: "archy_registry_surname", required:true},
                {id: "archy_registry_firm_name", hide:true, required:false},
                {id: "archy_registry_tax_code", required:true},
                {id: "archy_registry_city_id", required:false},
                {id: "archy_registry_zip", required:false},
                {id: "archy_registry_address", required:false}
            ], // persona fisica
            1: [
                {id: "archy_registry_name", hide:true}, 
                {id: "archy_registry_surname", hide:true},
                {id: "archy_registry_tax_code", required:true},
                {id: "archy_registry_city_id", required:true},
                {id: "archy_registry_zip", required:true},
                {id: "archy_registry_address", required:true},
                {id: "archy_registry_vat_country_id", required:true},
                {id: "archy_registry_vat_id", required:true}
            ], // persona giuridica
            2: [
                {id: "archy_registry_name", hide:true}, 
                {id: "archy_registry_surname", hide:true},
                {id: "archy_registry_tax_code", required:true},
                {id: "archy_registry_city_id", required:true},
                {id: "archy_registry_zip", required:true},
                {id: "archy_registry_address", required:true}
            ], // Onlus
            3: [
                {id: "archy_registry_name"}, 
                {id: "archy_registry_surname"},
                {id: "archy_registry_firm_name", hide:true},
                {id: "archy_registry_vat_country_id", hide:true},
                {id: "archy_registry_vat_id", hide:true},
                {id: "archy_registry_tax_code", required:false},
                {id: "archy_registry_city_id", required:false},
                {id: "archy_registry_zip", required:false},
                {id: "archy_registry_address", required:false},
                {id: "archy_registry_email", required:false}
            ] // nominativo
        }, { mode: "hide" });
        
        (() => {
        'use strict'

        const forms = document.querySelectorAll('.needs-validation')
        
        const customValidators = [
            {
              selector: "#archy_registry_vat_id",
              validate: isValidVat,
              message: "Partita IVA non valida"
            },
            {
              selector: "#archy_registry_zip",
              validate: isValidZip,
              message: "CAP non valido"
            },
            {
              selector: "#archy_registry_email",
              validate: isValidEmail,
              message: "Email non valida"
            },
            {
              selector: "#archy_registry_tax_code",
              validate: isValidTaxCode,
              message: "Codice Fiscale non valido"
            }
        ];
        
        function customCheck(form) {
            let valid = true;

            customValidators.forEach(rule => {
              const el = form.querySelector(rule.selector);
              if (!el) return;

              // 👉 ignora campi nascosti
              if (el.offsetParent === null) return;

              const ok = rule.validate(el.value);
              const feedback = el.nextElementSibling;
              
              if (!ok) {
                el.setCustomValidity(rule.message);
                el.classList.add("is-invalid");
                el.classList.remove("is-valid");
                if (feedback && feedback.classList.contains("invalid-feedback")) {
                    feedback.textContent = rule.message;
                  }
                valid = false;
              } else {
                el.setCustomValidity("");
                el.classList.remove("is-invalid");
                if (feedback) {
                    feedback.textContent = "";
                  }
              }
            });

            return valid;
        }

        Array.from(forms).forEach(form => {
          form.addEventListener('submit', event => {

            const bootstrapValid = form.checkValidity();
            const customValid = customCheck(form);

            if (!bootstrapValid || !customValid) {
              event.preventDefault();
              event.stopPropagation();
            }

            form.classList.add('was-validated');
          }, false)
        })
      })();
        
        $('#archy_registry_customer_private').change(function(){
		switch( $(this).val() ){
			case '':
				$('#archy_registry_customer_recipient_code, #archy_registry_customer_sdi_pec').prop('disabled',true);
			break;
			case '1':
				$('#archy_registry_customer_recipient_code').attr('maxlength',7).prop('disabled',false);
			break;
			case '2':
				$('#archy_registry_customer_recipient_code').attr('maxlength',6).prop('disabled',false);
			break;
		}
	}).trigger('change');
        
        $('#archy_registry_customer_sending_method').change(function(){
		switch( $(this).val() ){
			case '':
				$('.recipient_code, .sdi_pec').css('display','none');
			break;
			case '1':
				$('.sdi_pec').css('display','block');
				$('.recipient_code').css('display','none');
				$('#archy_registry_customer_recipient_code').prop('required',false).prop('disabled',true);
				$('#archy_registry_customer_sdi_pec').prop('required',true).prop('disabled',false);
			break;
			case '2':
				$('.sdi_pec').css('display','none');
				$('.recipient_code').css('display','block');
				$('#archy_registry_customer_recipient_code').prop('required',true).prop('disabled',false);
				$('#archy_registry_customer_sdi_pec').prop('required',false).prop('disabled',true);
			break;
		}
	}).trigger('change');
        
        $('#copy_address').click(function(){
            if ($('#archy_registry_legal_personality').val() == '0'){ // persona fisica, copia anche nome+cognome
                $('[name="main[member_public_name]"]').val($('[name="main[name]"]').val());
                $('[name="main[member_public_surname]"]').val($('[name="main[surname]"]').val());
            } 

            $('[name="main[member_public_address]"]').val($('[name="main[address]"]').val());
            $('[name="main[member_public_zip]"]').val($('[name="main[zip]"]').val());
            $('#archy_registry_member_public_city_id').val($('#archy_registry_city_id').val());
            $('[name="main[member_public_city_id]"]').val($('[name="main[city_id]"]').val());
            $('[name="main[member_public_phone]"]').val($('[name="main[telephone]"]').val());
            $('[name="main[member_public_mail]"]').val($('[name="main[email]"]').val());
            $('[name="main[member_public_website]"]').val($('[name="main[website]"]').val());
	});
        
        $('input[name="main[tax_code]"],input[name="main[vat_id]"]').on('change',function(){
		var value=$(this).val().replace(/ /g,'').toUpperCase();
		$(this).val(value);
		var column=$(this).attr('name').replace('main[','').replace(']','');
		if(value.trim()!=''){
			var id='<?php echo @$_GET['id']?>';
                        var lp=$('#archy_registry_legal_personality').val();
			$.ajax({
				type: "POST",
				url: "app.php?do_action=check_unique_taxcode&value="+value+"&lp="+lp,
				data: '',
				cache: false,
				success: function(html){
					if(html=='ko'){
						prompt_duplicated_entry(value,column);
					}else{
						if(value.length==16 && column=='tax_code'){
							retrieve_tax_code_info(value);
						}
					}
				}
			});
		}
	});
	
	function prompt_duplicated_entry(value,column){
		$('[name="main['+column+']"]').val('');
		$.ajax({
			type: "POST",
			url: "app.php?do_action=registry_duplicated_entry&value="+value+"&column="+column,
			data: '',
			dataType: 'json',
			cache: false,
			success: function(data){
				$('#anchor').attr('data-url',root+'archy/app.php?intent=archy_registry&view=edit&id='+data.id);
				$('#duplicated_entry .modal-body').html(data.message);
				$('#duplicated_entry').modal('show');
			}
		});
	}

	$('#archy_registry_is_customer').click(function(){
		var display=$(this).is(':checked')? '' : 'none';
		$('#customer-wrap-tab').css('display',display);
	});
	$('#archy_registry_is_supplier').click(function(){
		var display=$(this).is(':checked')? '' : 'none';
		$('#supplier-wrap-tab').css('display',display);
	});
	$('#archy_registry_is_member').click(function(){
		var display=$(this).is(':checked')? '' : 'none';
		$('#member-wrap-tab').css('display',display);
	});
	var display=$('#archy_registry_is_customer').is(':checked')? '' : 'none';
	$('#customer-wrap-tab').css('display',display);
	display=$('#archy_registry_is_supplier').is(':checked')? '' : 'none';
	$('#supplier-wrap-tab').css('display',display);
	display=$('#archy_registry_is_member').is(':checked')? '' : 'none';
	$('#member-wrap-tab').css('display',display);
		
	$('input[name="main[city_id]"]').on('input',function(){
		$.ajax({
			type: "POST",
			url: "/archy/app.php?do_action=get_cap",
			data: 'city_id='+$(this).val(),
			cache: false,
			success: function(cap){
				$('input[name="main[zip]"]').val(cap);
			}
		});
	});

	
	<?php if(!isset($_GET['id'])):?>
	var url = new URL(parent.location.href);
	if(url.searchParams.get("intent")=='archy_memberships'){
		$('#archy_registry_is_member').trigger('click');
	}
	<?php endif?>
	
        
    });
    
</script>