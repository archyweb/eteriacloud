<tr>
    <td>
        <?php echo @$payments['payment_unique_id'][$index]; ?>
        <input type="hidden" name="main[payments][payment_unique_id][]" value="<?php echo @$payments['payment_unique_id'][$index]; ?>"> 
    </td>
    <td>
        <?php echo @$date ?>
        <input type="hidden" name="main[payments][payment_date][]" value="<?php echo @$date; ?>">    
    </td>
    <td>
        <?php echo @$payments['paid'][$index]; ?>
        <input type="hidden" name="main[payments][paid][]" value="<?php echo App::currency(@$payments['paid'][$index]); ?>">
    </td>
    <td>
        <?php if(@$payments['fattura'][$index]==1){echo 'Fattura'; } ?>
        <?php if(@$payments['fattura'][$index]==0){echo 'Ricevuta'; } ?>
        <input type="hidden" name="main[payments][fattura][]" value="<?php echo @$payments['fattura'][$index]; ?>">
    </td>
    <td>
        <?php echo @$payments['petty_cash_type'][$index]; ?>
        <input type="hidden" name="main[payments][petty_cash_type][]" value="<?php echo @$payments['petty_cash_type'][$index]; ?>">
        <input type="hidden" name="main[payments][petty_cash_type_id][]" value="<?php echo @$payments['petty_cash_type_id'][$index]; ?>">
    </td>
    <td>
        <?php echo @$payments['payment_method'][$index]; ?>
        <input type="hidden" name="main[payments][payment_method][]" value="<?php echo @$payments['payment_method'][$index]; ?>">
        <input type="hidden" name="main[payments][payment_method_id][]" value="<?php echo @$payments['payment_method_id'][$index]; ?>">
    </td>
    <td></td>
</tr>