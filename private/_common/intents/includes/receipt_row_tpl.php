<tr>
   <td class="act"><button class="btn btn-danger btn-xs delete_row" type="button" tabindex="-1"><span class="fas fa-minus"></span></button></td>
   <td><div class="form-group"><input type="text" class="form-control" required="required" placeholder="Causale" name="main[receipt][description][]" value="<?php echo form_value(@$rows['description'][$index])?>"></div></td>
   <td >
      <div class="form-group"><input type="text" class="form-control receipt_item numbers_only receipt_quantity" required="required" placeholder="Quantità" name="main[receipt][quantity][]" value="<?php echo @$rows['quantity'][$index]?>"></div>
   </td>
   <td>
      <div class="form-group">
         <div class="input-group has-validation">
             <div class="input-group-text"><span class="fas fa-euro-sign"></span></div>
            <input type="text" class="form-control numbers_only receipt_item receipt_price" required="required" placeholder="Prezzo unitario" name="main[receipt][price][]" value="<?php echo @$rows['price'][$index]?>">
 
         </div>
      </div>
   </td>
   <td class="receipt_amount" align="right">
      <span><?php echo App::currency(@$rows['amount'][$index])?></span>
      <input type="hidden" name="main[receipt][amount][]" value="<?php echo @$rows['amount'][$index]?>" class="receipt_amount_holder">
   </td>
</tr>
