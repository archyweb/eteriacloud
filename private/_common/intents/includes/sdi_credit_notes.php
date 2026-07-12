<?php
if(isset($_GET['id'])):
	$attached_invoice=mysqli_fetch_object($app->db->query("select invoice_id,invoice_number from archy_credit_notes_from_sdi where id=".$_GET['id']));
endif;
?>
<!--div class="container">
	<hr>
	<div class="row">
		<div class="col-md-4 col-sm-12">
			<div class="form-group ">
				<label class="control-label" for="archy_credit_notes_invoice_id">Associa Fattura <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="Campo richiesto"></span></label>
				<div class="input-group">
					<input type="text" class="form-control search required" id="archy_credit_notes_invoice_id" value="<?php echo @$attached_invoice->invoice_number?>" name="main[invoice_number]" readonly>
					<div class="input-group-addon clear"><span class="glyphicon glyphicon-remove"></span></div>
					<div class="input-group-addon lookup" data-toggle="modal" data-target="#search_modal" 
						data-intent="archy_payables" data-domain="" data-intent_name="Documenti di acquisto" data-lookup="archy_payables.number">
					<span class="glyphicon glyphicon-search"></span>
				</div>
				<input type="hidden" name="main[invoice_id]" value="<?php echo @$attached_invoice->invoice_id?>" id="archy_credit_notes_invoice_id_value_holder">
				<div class="input-group-addon inline_view" data-toggle="modal" data-target="#view_modal"
					data-intent="archy_payables" data-domain="" data-intent_name="Documenti di acquisto" data-lookup="archy_payables.number">
					<span class="glyphicon glyphicon-eye-open"></span>
				</div>
			</div>
		</div>
	</div>
</div-->
<hr>

<?php
if($data['hidden_columns']['archy_credit_notes_from_sdi.sdi_payload']['value']!=''):
	$sdi_credit_note=json_decode($data['hidden_columns']['archy_credit_notes_from_sdi.sdi_payload']['value']);
	$body=$sdi_credit_note->fattura_elettronica_body[0];
	?>
	<legend style="margin-top:25px">Corpo Nota di Credito SDI</legend>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Descrizione</th>
					<th>Quantità</th>
					<th>Prezzo unitario</th>
					<th>Imponibile</th>
					<th>IVA</th>
					<th>Subtotale</th>
				</tr>
			</thead>
			<tbody>
	<?php
	$total=$taxable_amount=$vat_total=0;
	foreach($body->dati_beni_servizi->dettaglio_linee as $data):
		$vat=$data->prezzo_totale*$data->aliquota_iva/100;
		$subtotal=$data->prezzo_totale+$vat;
		$total+=$subtotal;
		$taxable_amount+=$data->prezzo_totale;
		$vat_total+=$vat;
		?>
		<tr>
			<td><?php echo $data->descrizione?></td>
			<td><?php echo $data->prezzo_totale/$data->prezzo_unitario?></td>
			<td><?php echo App::currency($data->prezzo_unitario)?></td>
			<td><?php echo App::currency($data->prezzo_totale)?></td>
			<td>(<?php echo $data->aliquota_iva?>%) <?php echo App::currency($vat)?></td>
			<td><?php echo App::currency($subtotal)?></td>
		</tr>
		<?php
	endforeach;
	?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3">Totali</th>
					<th><?php echo App::currency($taxable_amount)?></th>
					<th><?php echo App::currency($vat_total)?></th>
					<th><?php echo App::currency($total)?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?php
endif;
?>
