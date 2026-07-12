<?php
//$petty_cash=new PettyCash();
$document_table=$data['hidden_columns']["archy_petty_cash_book.document_table"]["value"];
$document_id=$data['hidden_columns']["archy_petty_cash_book.document_id"]["value"];
?>
<hr>
<div class="container">
   <div class="row">
      <div class="col-md-3">
         
         <div class="form-group">
            <label class="form-label" for="document" style="display:block">Associa documento contabile</label>
            <select class="form-select select2" id="document" name="">
               <option value="">Seleziona</option>
               <option value="archy_invoices">Fattura</option>
               <option value="archy_credit_notes">Nota di credito</option>
               <option value="archy_tax_receipts">Ricevuta</option>
               <option value="archy_passive_receipts">Storno ricevuta</option>
               <option value="archy_payables">Documento di acquisto</option>
            </select>
         </div>
         
         <div class="form-group" style="display:none">
            <div class="input-group has-validation">
               <div class="input-group-text lookup" id="main_trigger" data-bs-toggle="modal" data-bs-target="#search_modal" 
               	data-intent="" data-domain="" data-intent_name=""> <span class="fas fa-search" title="Cerca"></span> </div>
               <input type="hidden" name="main[document_id]" id="id_holder" value="<?php echo $document_id?>">
            </div>
         </div>
         <input type="hidden" name="main[document_table]" id="table_holder" value="<?php echo $document_table?>">
         
      </div>
   </div>
   <div id="document_data" class="table-responsive"></div>
</div>

<script type="text/javascript">
var search_modal_enabled=true;
window.onload=function(){
	
	$('#document').change(function(){
		
		if(!search_modal_enabled){
			return false;
		}
		
		var intent_names={
			'archy_invoices' : 'Fatture',
			'archy_credit_notes' : 'Note di credito',
			'archy_tax_receipts' : 'Ricevute', 
			'archy_passive_receipts' : 'Storno Ricevute', 
			'archy_payables' : 'Documenti di acquisto'
		};
		
		if($(this).val()!=''){
			var intent=$(this).val();
			$('#main_trigger').data('intent',intent).data('intent_name',intent_names.intent);			
			$('#table_holder').val(intent);
			$('#main_trigger').trigger('click');
			
		}else{
			
			$('#id_holder,#table_holder').val('');
			$('#document_data').html('');
			
		}
	}).trigger('change');
	
	$('#id_holder').on('input',function(){
		var id=$(this).val();
		var table=$('#table_holder').val();
		
		var intent_names={
			'archy_invoices' : ['Fattura','invoice_number'],
			'archy_credit_notes' : ['Nota di credito','credit_note_number'],
			'archy_tax_receipts' : ['Ricevuta','receipt_number'], 
			'archy_passive_receipts' : ['Storno ricevuta','receipt_number'], 
			'archy_payables' : ['Documento di acquisto','number']
		};
		
		$.ajax({
			type: "POST",
			url: "/archy/app.php?do_action=document_info",
			data: 'table='+table+'&id='+id,
			dataType:"json",
			cache: false,
			success: function(json){
				
				var total=Number(json['total']);
				var paid=Number(json['paid']);
				var unpaid=Math.abs(total)-paid;
				var response='<table class="table table-condensed table-bordered mt-3">';
				response+='<thead><tr><th>Tipo</th><th>Numero</th><th>Data</th><th>Intestatario</th><th>Totale</th><th>Pagato</th><th>Residuo</th></tr></thead>';
				response+='<tbody><tr><td>'+intent_names[table][0]+'</td><td>'+json[intent_names[table][1]]+'</td><td>'+json['latin_date']+'</td><td>'+json['customer']['label']+'</td><td>'+format_amount(total)+'</td><td>'+format_amount(paid)+'</td><td>'+format_amount(unpaid)+'</td></tr></tbody>';
				response+='</table>';
				$('#document_data').html(response);
				
				var description=intent_names[table][0]+' '+json[intent_names[table][1]]+' del '+json['latin_date'];
				if(table=='archy_payables'){
					description+=' '+json['customer']['firm_name'];
				}
				
				if($('input[name="main[petty_cash]"]').val()==''){
					$('input[name="main[petty_cash]"]').val(description);
				}
				
			}
		});
		
	});
	
	<?php if(isset($_GET['id'])):?>
		$('#table_holder').val('<?php echo $document_table?>');
		$('#id_holder').val('<?php echo $document_id?>');
		$('#id_holder').trigger('input');
		
		search_modal_enabled=false;
		$('#document').val('<?php echo $document_table?>').trigger('change');
		search_modal_enabled=true;
	<?php endif?>
}
</script>