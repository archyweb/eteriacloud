<tr>
    <td></td>
    <td>
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control date input-sm" name="main[payments][payment_date][]" value="<?php if($data_payments!=''){ echo @$date; } ?>" placeholder="__/__/____" maxlength="10" required>
                <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
            </div>
        </div>
    </td>
    <td>
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control numbers_only input-sm" name="main[payments][paid][]" value="<?php if($data_payments!=''){ echo App::currency(@$payments['paid'][$index]); } ?>" placeholder="Importo pagato" required>
                <div class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></div>
            </div>
        </div>
    </td>
    <td>
        <div class="form-group">
			
            <select class="form-control required select2" id="archy_events_subscriptions_payments_fattura_<?php if($data_payments!=''){ echo $index; } ?>" name="main[payments][fattura][]" required>
                <option value="">Seleziona</option>
                <option value="1"<?php if($data_payments!=''){ if(@$payments['fattura'][$index]==1){echo ' selected'; } } ?>>Fattura</option>
                <option value="0"<?php if($data_payments!=''){ if(@$payments['fattura'][$index]==0){echo ' selected'; } } ?>>Ricevuta</option>
            </select>
			
            <?php if(@$payments['payment_unique_id'][$index]!=''){ ?>
            <input type="hidden" name="main[payments][payment_unique_id][]" value="<?php if($data_payments!=''){ echo @$payments['payment_unique_id'][$index]; } ?>">
            <?php } ?>
        </div>
    </td>
    <td>
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control search input-sm" name="main[payments][petty_cash_type][]" value="<?php if($data_payments!=''){ echo @$payments['petty_cash_type'][$index]; } ?>" readonly placeholder="Libro prima nota" required>
                <div class="input-group-addon clear"><span class="glyphicon glyphicon-remove"></span></div>
                <div class="input-group-addon lookup" data-toggle="modal" data-target="#search_modal" data-intent="archy_petty_cash_types" data-domain="" data-intent_name="Libro prima nota" data-lookup="type">
                    <span class="glyphicon glyphicon-search"></span>
                </div>
                <input type="hidden" name="main[payments][petty_cash_type_id][]" value="<?php if($data_payments!=''){ echo @$payments['petty_cash_type_id'][$index]; } ?>">
            </div>
        </div>
    </td>
    <td>
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control search input-sm" name="main[payments][payment_method][]" value="<?php if($data_payments!=''){ echo @$payments['payment_method'][$index]; } ?>" readonly placeholder="Modalità di pagamento" required>
                <div class="input-group-addon clear"><span class="glyphicon glyphicon-remove"></span></div>
                <div class="input-group-addon lookup" data-toggle="modal" data-target="#search_modal" data-intent="archy_payment_methods" data-domain="" data-intent_name="Modalità di pagamento" data-lookup="method">
                    <span class="glyphicon glyphicon-search"></span>
                </div>
                <input type="hidden" name="main[payments][payment_method_id][]" value="<?php if($data_payments!=''){ echo @$payments['payment_method_id'][$index]; } ?>">
            </div>
        </div>
    </td>
    <td><i class="glyphicon glyphicon-trash delete_payment"></i></td>
</tr>