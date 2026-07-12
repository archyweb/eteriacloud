<ul class="nav nav-tabs" id="firmTab" role="tablist">
  <li class="nav-item"><a class="nav-link active" id="firm-tab" data-bs-toggle="tab" href="#tab-firm" role="tab" aria-controls="tab-firm" aria-selected="true">Anagrafica registro</a></li>
  <li class="nav-item"><a class="nav-link" id="invoice-tab" data-bs-toggle="tab" href="#tab-invoice" role="tab" aria-controls="tab-invoice" aria-selected="false">Fatture</a></li>
  <li class="nav-item"><a class="nav-link" id="e-invoice-tab" data-bs-toggle="tab" href="#tab-e-invoice" role="tab" aria-controls="tab-e-invoice" aria-selected="false">Fatture Elettroniche</a></li>
  <li class="nav-item"><a class="nav-link" id="receipt-tab" data-bs-toggle="tab" href="#tab-receipt" role="tab" aria-controls="tab-receipt" aria-selected="false">Ricevute</a></li>
  <li class="nav-item"><a class="nav-link" id="credit_notes-tab" data-bs-toggle="tab" href="#tab-credit_notes" role="tab" aria-controls="tab-credit_notes" aria-selected="false">Note di credito</a></li>
  <li class="nav-item"><a class="nav-link" id="quote-tab" data-bs-toggle="tab" href="#tab-quote" role="tab" aria-controls="tab-quote" aria-selected="false">Preventivi</a></li>
</ul>
<div class="tab-content border border-top-0 p-3" id="firmTabContent">
    <div class="tab-pane fade show active" id="tab-firm" role="tabpanel" aria-labelledby="firm-tab">
        <div class="row">
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.firm_name'],"col-md-6");
            echo $app::form_element($data['hidden_columns']['archy_firms.vat_country_id'],"col-md-2");
            echo $app::form_element($data['hidden_columns']['archy_firms.vat_id'],"col-md-4");

            echo $app::form_element($data['hidden_columns']['archy_firms.tax_id'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.address'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.city_id'],"col-md-4");

            echo $app::form_element($data['hidden_columns']['archy_firms.zip'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.phone'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.fax'],"col-md-4");

            echo $app::form_element($data['hidden_columns']['archy_firms.mobile'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.email'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.certified_email'],"col-md-4");

            echo $app::form_element($data['hidden_columns']['archy_firms.website'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.logo'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.tax_rate_id'],"col-md-4");

            echo $app::form_element($data['hidden_columns']['archy_firms.bank_id'],"col-md-4");
            ?>
         </div>
    </div>
    <div class="tab-pane fade" id="tab-invoice" role="tabpanel" aria-labelledby="invoice-tab">
        <div class="mb-3"><span class="text-bg-primary px-3 py-2 w-100 text-uppercase">Impostazioni fattura</span></div>
        <div class="row g-3">            
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.invoice_prefix'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.invoice_number_length'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.first_invoice_number'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.invoice_template'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.invoice_footer'],"col-md-12");
            ?>
        </div>
        
        <hr>
        <div class="mb-3 mt-5"><span class="text-bg-primary px-3 py-2 text-uppercase">Email fattura</span></div>
        <div class="row g-3">
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.invoice_mail_subject'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.invoice_mail_body'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.invoice_mail_bcc'],"col-md-12");
            ?>
         </div>
    </div>
    <div class="tab-pane fade" id="tab-e-invoice" role="tabpanel" aria-labelledby="e-invoice-tab">
        <div class="mb-3"><span class="text-bg-primary px-3 py-2 text-uppercase">Fatturazione elettronica</span></div>
        <div class="row g-3">
        <?php 
          echo $app::form_element($data['hidden_columns']['archy_firms.tax_regime'],"col-md-4");
          echo $app::form_element($data['hidden_columns']['archy_firms.EORI'],"col-md-4");
          echo $app::form_element($data['hidden_columns']['archy_firms.VAT_collectability'],"col-md-4");
          ?>
       </div>
        
        <hr>
        <div class="mb-3 mt-5"><span class="text-bg-primary px-3 py-2 text-uppercase">Stabile Organizzazione</span></div>
        <p>I seguenti campi devono essere compilati solo se il cedente/prestatore è un soggetto non residente in Italia ma ha concesso beni o prestato i servizi indicati in fattura tramite una stabile organizzazione che ha sede in Italia.</p>
        <div class="row g-3">
           <?php 
           echo $app::form_element($data['hidden_columns']['archy_firms.stable_org_address'],"col-md-6");
           echo $app::form_element($data['hidden_columns']['archy_firms.stable_org_zip'],"col-md-2");
           echo $app::form_element($data['hidden_columns']['archy_firms.stable_org_city_id'],"col-md-4");
           ?>
        </div>
        
        <hr>
        <div class="mb-3 mt-5"><span class="text-bg-primary px-3 py-2 text-uppercase">Iscrizione REA</span></div>
        <p>I seguenti campi devono essere compilati nel caso in cui il cedente/prestatore sia una società soggetta al vincolo dell'iscrizione nel registro delle imprese ai sensi dell'art. 2250 del codice civile.</p>
        <div class="row g-3">
           <?php 
           echo $app::form_element($data['hidden_columns']['archy_firms.rea_office'],"col-md-4");
           echo $app::form_element($data['hidden_columns']['archy_firms.rea_number'],"col-md-4");
           echo $app::form_element($data['hidden_columns']['archy_firms.rea_share_capital'],"col-md-4");                              
           echo $app::form_element($data['hidden_columns']['archy_firms.rea_sole_shareholder'],"col-md-4");
           echo $app::form_element($data['hidden_columns']['archy_firms.rea_in_liquidation'],"col-md-4");
           ?>
        </div>
        
        <hr>
        <div class="mb-3 mt-5"><span class="text-bg-primary px-3 py-2 text-uppercase">Albo professionale</span></div>
        <p>I seguenti campi possono essere compilati se il cedente/prestatore è iscritto ad un albo professionale.</p>
        <div class="row g-3">
           <?php 
           echo $app::form_element($data['hidden_columns']['archy_firms.professional_register_name'],"col-md-3");
           echo $app::form_element($data['hidden_columns']['archy_firms.professional_register_province'],"col-md-3");
           echo $app::form_element($data['hidden_columns']['archy_firms.professional_register_subscription_number'],"col-md-3");                              
           echo $app::form_element($data['hidden_columns']['archy_firms.professional_register_subscription_date'],"col-md-3");
           ?>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-receipt" role="tabpanel" aria-labelledby="receipt-tab">
        <div class="mb-3"><span class="text-bg-primary px-3 py-2 text-uppercase">Impostazioni ricevuta</span></div>
        <div class="row g-3">
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.receipt_prefix'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.receipt_number_length'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.first_receipt_number'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.passive_receipt_prefix'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.passive_receipt_number_length'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.first_passive_receipt_number'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.receipt_template'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.receipt_footer'],"col-md-12");
            ?>
         </div>
        
        <hr>
        <div class="mb-3 mt-5"><span class="text-bg-primary px-3 py-2 text-uppercase">Email ricevuta</span></div>
        <div class="row g-3">
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.receipt_mail_subject'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.receipt_mail_body'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.receipt_mail_bcc'],"col-md-12");
            ?>
         </div>
    </div>
    <div class="tab-pane fade" id="tab-credit_notes" role="tabpanel" aria-labelledby="credit_notes-tab">
        <div class="mb-3"><span class="text-bg-primary px-3 py-2 text-uppercase">Impostazioni Nota di credito</span></div>
        <div class="row g-3">
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.credit_note_prefix'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.credit_note_number_length'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.first_credit_note_number'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.credit_note_template'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.credit_note_footer'],"col-md-12");
            ?>
         </div>
        
        <hr>
        <div class="mb-3 mt-5"><span class="text-bg-primary px-3 py-2 text-uppercase">Email Nota di credito</span></div>
        <div class="row g-3">
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.credit_note_mail_subject'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.credit_note_mail_body'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.credit_note_mail_bcc'],"col-md-12");
            ?>
         </div>
    </div>
    <div class="tab-pane fade" id="tab-quote" role="tabpanel" aria-labelledby="quote-tab">
        <div class="mb-3"><span class="text-bg-primary px-3 py-2 text-uppercase">Impostazioni preventivo</span></div>
        <div class="row g-3">
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.quote_prefix'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.quote_number_length'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.first_quote_number'],"col-md-4");
            echo $app::form_element($data['hidden_columns']['archy_firms.quote_template'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.quote_footer'],"col-md-12");
            ?>
         </div>
        
        <hr>
        <div class="mb-3 mt-5"><span class="text-bg-primary px-3 py-2 text-uppercase">Email preventivo</span></div>
        <div class="row g-3">
            <?php 
            echo $app::form_element($data['hidden_columns']['archy_firms.quote_mail_subject'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.quote_mail_body'],"col-md-12");
            echo $app::form_element($data['hidden_columns']['archy_firms.quote_mail_bcc'],"col-md-12");
            ?>
         </div>
    </div>
</div>

<script type="text/javascript">
window.onload=function(){
	var city_changed;
	var loaded_city='<?php echo isset($_GET['id'])? $data['hidden_columns']['archy_firms.city_id']['value'] : ''?>';
	$('#search_modal').on('show.bs.modal', function (event) {
		var modal_intent=$(event.relatedTarget).data('intent');
		city_changed=modal_intent=='archy_cities';
	});
	$('#search_modal').on('hidden.bs.modal', function (e) {
		if(city_changed){
			var city_id=$('[name="main[city_id]"]').val();
			if(city_id!=loaded_city){
				loaded_city=city_id;
				$.ajax({
					type: "POST",
					url: root+"archy/app.php?do_action=get_cap",
					data: 'city_id='+city_id,
					cache: false,
					success: function(cap){
						$('input[name="main[zip]"]').val(cap);
					}
				});
			}
		}
	});
}
</script>