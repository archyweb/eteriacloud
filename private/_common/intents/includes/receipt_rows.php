<?php
if(isset($_GET['id'])):
	$rows=$data['hidden_columns'][$app->intent->table.'.receipt']['value'];
	$total=$data['hidden_columns'][$app->intent->table.'.total']['value'];
else:
	$rows=[
		'description'=>[''],
		'amount'=>['']
	];
	$total="0";
endif;
?>
<div class="container">

	<hr>
   <h3>Corpo Ricevuta</h3>
   <div class="table-responsive">
      <table class="table" id="receipt_body">
         <thead>
            <tr>
               <th scope="col" colspan="2">Causale <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></th>
               <th scope="col">Quantità <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></th>
               <th scope="col">Prezzo unitario <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></th>
               <th class="text-end" scope="col">Subtotale</th>
            </tr>
         </thead>
         <tbody>
         <?php foreach($rows['description'] as $index=>$description):?>
         	<?php include 'receipt_row_tpl.php'?>
         <?php endforeach?>
         </tbody>
         <tfoot>
             <tr>
                <th colspan="4"><button class="btn btn-primary btn-xs tt" id="add_row" type="button" title="Aggiungi riga"><span class="fas fa-plus"></span></button></th>
            </tr>
         	<tr id="invoice_totals">
               <th colspan="4">Totale Ricevuta</th>
               <th id="receipt_total"><?php echo App::currency($total)?></th>
         	</tr>
         </tfoot>
      </table>
   </div>
   
   <hr>
   <h3>Note da riportare nella ricevuta</h3>
   <textarea class="tinymce d-none" name="main[notes]"><?php echo $data['hidden_columns'][$app->intent->table.'.notes']['value']?></textarea>
   
</div>

<input type="hidden" name="main[total]" value="<?php echo $total?>" id="field_total">

<script type="text/html" id="receipt_row">
<?php 
$rows=[]; include 'receipt_row_tpl.php'?>
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
                data: 'year='+year+'&firm_id='+firm_id+'&table=<?php echo $app->intent->table?>&first_number=first_receipt_number&ajax=true',
                cache: false,
                success: function(number){
                    $('input[name="main[number]"]').val(number==''? 1 : number);
                }
            });
        }
	}
}

</script>