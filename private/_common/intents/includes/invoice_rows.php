<?php
if(isset($_GET['id'])):
	$rows=$data['hidden_columns']['archy_invoices.invoice']['value'];
	$taxable_amount=$data['hidden_columns']['archy_invoices.taxable_amount']['value'];
	$vat=$data['hidden_columns']['archy_invoices.vat']['value'];
	$total=$data['hidden_columns']['archy_invoices.total']['value'];
	
	$_POST['firm_id']=$data['columns']['archy_invoices.firm_id']['value'];
	$default_tax_rate=$app->get_firm_default_tax_rate($echo=false);
else:
	$rows=[];
	$taxable_amount="0";
	$vat="0";
	$total="0";
	
	$_POST['firm_id']=$app->default_firm_id;
	$default_tax_rate=$app->get_firm_default_tax_rate($echo=false);
endif;

$name='invoice';// used in included tpl

?>

	<hr>
   <h3>Corpo Fattura</h3>
   <div class="table-responsive">
      <table class="table" id="invoice_body">
         <thead>
            <tr>
               <th scope="col" colspan="2">Causale <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></th>
               <th scope="col">Quantità <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></th>
               <th scope="col">Prezzo unitario<span class="fas fa-asterisk required-label" title="Campo richiesto"></span></th>
               <th scope="col">Imponibile</th>
               <th scope="col" colspan="2">IVA <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></th>
               <th class="text-end" scope="col">Subtotale</th>
            </tr>
         </thead>
         <tbody>
         <?php if(!empty($rows)):?>
            <?php foreach($rows['description'] as $index=>$description):?>
               <?php include 'invoice_row_tpl.php'?>
            <?php endforeach?>
         <?php endif?>
         </tbody>
         <tfoot>
            <tr>
                <th colspan="4"><button class="btn btn-primary btn-xs tt" id="add_row" type="button" title="Aggiungi riga"><span class="fas fa-plus"></span></button></th>
            </tr>
         	<tr id="invoice_totals">
               <th colspan="4">Totali</th>
               <th id="amount_total"><?php echo App::currency($taxable_amount)?></th>
               <th id="tax_total" colspan="2"><?php echo App::currency($vat)?></th>
               <th id="invoice_total"><?php echo App::currency($total)?></th>
         	</tr>
         </tfoot>
      </table>
   </div>
   <hr>
   <h3>Note da riportare in fattura</h3>
   <textarea class="tinymce d-none" name="main[notes]"><?php echo $data['hidden_columns']['archy_invoices.notes']['value']?></textarea>


<input type="hidden" name="main[taxable_amount]" value="<?php echo $taxable_amount?>" id="field_taxable_amount">
<input type="hidden" name="main[vat]" value="<?php echo $vat?>" id="field_vat">
<input type="hidden" name="main[total]" value="<?php echo $total?>" id="field_total">

<script type="text/html" id="invoice_row">
<?php 
if(isset($_GET['id'])):
	$rows=[];
	@$rows['tax_percentage'][0]=$default_tax_rate['percentage'];
	@$rows['tax_rate_id'][0]=$default_tax_rate['id'];
endif;
include 'invoice_row_tpl.php';
?>
</script>

<script type="text/javascript">

window.onload=function(){

	var date_input=$('input[name="main[date]"]');
	date_input.on('keyup change',function(){
		if($(this).val().replace(/_/g,'').length==10){
			get_invoice_number();
		}
	});
	
	$('input[name="main[firm_id]"]').on('input',function(){
		get_invoice_number();
		var firm_id=$('[name="main[firm_id]"]').val();
		get_default_tax_rate(firm_id);
	});
		
}

function get_invoice_number(){
	var firm_id=$('[name="main[firm_id]"]').val();
	var date=$('input[name="main[date]"]').val();
    var number=$('input[name="main[number]"]').val();

    if(number=='') {
        if(date.replace(/_/g,'').length==10 && firm_id!=''){
            var year=date.substr(6,4);
            $.ajax({
                type: "POST",
                url: "/archy/app.php?do_action=get_next_number_by_year_and_firm",
                data: 'year='+year+'&firm_id='+firm_id+'&table=archy_invoices&first_number=first_invoice_number&ajax=true',
                cache: false,
                success: function(number){
                    $('input[name="main[number]"]').val(number==''? 1 : number);
                }
            });
        }
    }
}

var tax_percentage='<?php echo $default_tax_rate['percentage']?>';
var tax_rate_id='<?php echo $default_tax_rate['id']?>';
function get_default_tax_rate(firm_id){
	$.ajax({
		type: "POST",
		url: "/archy/app.php?do_action=get_firm_default_tax_rate",
		data: 'firm_id='+firm_id,
		dataType: 'json',
		cache: false,
		success: function(tax_rate){
			tax_percentage=tax_rate.percentage;
			tax_rate_id=tax_rate.id;
		}
	});
}
</script>