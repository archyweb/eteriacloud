<tr>
   <td class="act"><button class="btn btn-danger btn-xs delete_row" type="button" tabindex="-1"><span class="fas fa-minus"></span></button></td>
   <td><input type="text" class="form-control" required="required" placeholder="Causale" name="main[<?php echo $name?>][description][]" value="<?php echo form_value(@$rows['description'][$index])?>"></td>
   <td>
        <input type="text" class="form-control invoice_item numbers_only invoice_quantity" required="required" placeholder="Quantità" name="main[<?php echo $name?>][quantity][]" value="<?php echo @$rows['quantity'][$index]?>">
   </td>
   <td>
        <div class="form-group">
           <div class="input-group has-validation">
                <div class="input-group-text"><span class="fas fa-euro-sign"></span></div>
                <input type="text" class="form-control numbers_only invoice_item invoice_price" required="required" placeholder="Prezzo unitario" name="main[<?php echo $name?>][price][]" value="<?php echo @$rows['price'][$index]?>">
              
           </div>
        </div>
   </td>
   <td class="invoice_amount">
   	<span><?php echo App::currency(@$rows['amount'][$index])?></span>
        <input type="hidden" class="invoice_amount_holder" name="main[<?php echo $name?>][amount][]" value="<?php echo @$rows['amount'][$index]?>">
   </td>
   <td class="tax_cell">
        <div class="input-group">
           <input type="text" class="form-control search invoice_tax_rate" required="required" placeholder="%" value="<?php echo @$rows['tax_percentage'][$index]?>" readonly tabindex="-1" name="main[<?php echo $name?>][tax_percentage][]">
           <div class="input-group-text clear"><span class="far fa-times-circle" title="Cancella"></span></div>
            <div class="input-group-text lookup" data-bs-toggle="modal" data-bs-target="#search_modal" data-intent="archy_tax_rates" data-domain="" data-intent_name="Aliquota IVA" data-lookup="percentage">
              <span class="fas fa-search" title="Cerca"></span>
           </div>
           <input type="hidden" name="main[<?php echo $name?>][tax_rate_id][]" value="<?php echo @$rows['tax_rate_id'][$index]?>" class="invoice_item">
        </div>
      
      <input type="hidden" name="main[<?php echo $name?>][tax][]" value="<?php echo @$rows['tax'][$index]?>" class="field_tax">
      <input type="hidden" name="main[<?php echo $name?>][subtotal][]" value="<?php echo @$rows['subtotal'][$index]?>" class="field_subtotal">
   </td>
	<?php
   $tax=isset($rows['tax'][@$index])? App::currency($rows['tax'][$index]) : '€ 0,00';
   $subtotal=isset($rows['subtotal'][@$index])? App::currency($rows['subtotal'][$index]) : '€ 0,00';
   ?>
   <td class="invoice_tax"><?php echo $tax?></td>
   <td class="invoice_subtotal"><?php echo $subtotal?></td>
</tr>
