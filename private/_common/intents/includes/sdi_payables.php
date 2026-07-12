<?php
if($data['hidden_columns']['archy_payables.sdi_invoice']['value']!=''):
	$sdi_invoice=json_decode($data['hidden_columns']['archy_payables.sdi_invoice']['value']);
	$body=$sdi_invoice->fattura_elettronica_body[0];
	?>
	<legend style="margin-top:25px">Corpo Fattura SDI</legend>
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
			<!--td><?php echo $data->prezzo_totale/$data->prezzo_unitario?></td-->
			<td><?php echo $data->quantita?></td>
			<td><?php echo App::currency($data->prezzo_unitario)?></td>
			<td><?php echo App::currency($data->prezzo_totale)?></td>
			<td>(<?php echo $data->aliquota_iva?>%) <?php echo App::currency($vat)?></td>
			<td><?php echo App::currency($subtotal)?></td>
		</tr>
		<?php
	endforeach;
	if(is_array($body->dati_generali->dati_generali_documento->dati_cassa_previdenziale)):
		foreach($body->dati_generali->dati_generali_documento->dati_cassa_previdenziale as $data):
			$vat=$data->importo_contributo_cassa*$data->aliquota_iva/100;
			$subtotal=$data->importo_contributo_cassa+$vat;
			$total+=$subtotal;
			$taxable_amount+=$data->importo_contributo_cassa;
			$vat_total+=$vat;
			?>
			<tr>
				<td>Cassa previdenziale <?php echo $data->tipo_cassa?></td>
				<td>1.00</td>
				<td><?php echo App::currency($data->importo_contributo_cassa)?></td>
				<td><?php echo App::currency($data->importo_contributo_cassa)?></td>
				<td>(<?php echo $data->aliquota_iva?>%) <?php echo App::currency($vat)?></td>
				<td><?php echo App::currency($subtotal)?></td>
			</tr>
			<?php
		endforeach;
	endif;
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