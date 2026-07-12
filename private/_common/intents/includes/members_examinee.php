<div class="container" id="fee_wrapper" style="display:none">
	<fieldset>
   	<legend>Quota associativa</legend>
      <div class="row">
		<?php
		echo $app::form_element($data['hidden_columns']['archy_memberships_examinee.fee_date'],"col-md-3");
		echo $app::form_element($data['hidden_columns']['archy_memberships_examinee.fee_category_amount'],"col-md-3");

        ?>

        <div class="col-md-3">
            <div class="form-group">
            <label class="control-label" for="archy_memberships_examinee_total_fee_amount">Tot. Imp. versato</label>
            <div class="input-group">
                <input type="text" class="form-control numbers_only required" id="fee_total_amount" name="fee_total_amount" value="0" placeholder="Tot. Imp. versato" readonly="">
                <div class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></div>
            </div>
            </div>
        </div>

    </div>
    <div class="row">
        <?php
		echo $app::form_element($data['hidden_columns']['archy_memberships_examinee.firm_id'],"col-md-4");
		echo $app::form_element($data['hidden_columns']['archy_memberships_examinee.payment_method_id'],"col-md-4");
		echo $app::form_element($data['hidden_columns']['archy_memberships_examinee.petty_cash_type_id'],"col-md-4");
		?>
      </div>
   </fieldset>
</div>

<script type="text/javascript">

function calc_tot() {
    fee = (parseFloat($('input[name="main[fee_category_amount]"]').val() || '0.00'));
    eps = (parseFloat($('input[name="main[fee_eps_amount]"]').val() || '0.00'));
    tot = parseFloat(fee)+(parseFloat(eps) || 0.0);
    return tot;    
}

window.onload=function(){
    if($('#archy_memberships_examinee_fee').is(':checked')){
        $('#fee_wrapper').slideDown();

        var tot=calc_tot();
        $('input[name="fee_total_amount"]').val(tot.toFixed(2));

        // Disabilito check deposito
        $('#archy_memberships_examinee_fee').attr('disabled', true);
        // Disabilito modifica candidato
        $('#archy_memberships_examinee_registry_id').attr('disabled', true);
        $('#archy_memberships_examinee_registry_id + div').hide();
    }else{
        $('#fee_wrapper .form-control').prop('required',false);
    }

    $('#archy_memberships_examinee_fee').click(function(){
        if($(this).is(':checked')){
            $('#fee_wrapper').slideDown();
            var tot=calc_tot();
            $('input[name="fee_total_amount"]').val(tot.toFixed(2));
            $('#fee_wrapper .form-control').prop('required',true);
        }else{
            $('#fee_wrapper').slideUp();
            $('#fee_wrapper .form-control').prop('required',false);
            //$('#fee_wrapper .form-control, #fee_wrapper input[type="hidden"]').val('');
        }
    });

    // Handle Fee
    $('input[name="main[category_id]"]').on('input',function() {
        handleFee();
    });
	$('input[name="main[fee_date]"]').on('change',function() {
        handleFee();
    });
    function handleFee() {
        $.ajax({
            type: "POST",
            url: root+"archy/app.php?do_action=get_subscription_fee",
            data: 'category_id='+$('input[name="main[category_id]"]').val()+'&data='+$('input[name="main[fee_date]"]').val(),
            cache: false,
            success: function(fee){
                $('input[name="main[fee_category_amount]"]').val(fee || '0.00');
                eps = (parseFloat($('input[name="main[fee_eps_amount]"]').val() || '0.00'));
                tot = eps+(parseFloat(fee) || 0.0);
                $('input[name="fee_total_amount"]').val(tot.toFixed(2));
            }
        });
    }

    $('#archy_memberships_examinee_eps').on('change',function(){
        $.ajax({
            type: "POST",
            url: root+"archy/app.php?do_action=get_eps_fee",
            data: 'eps='+$(this).val(),
            cache: false,
            success: function(eps){
                $('input[name="main[fee_eps_amount]"]').val(eps || '0.00');
                fee = (parseFloat($('input[name="main[fee_category_amount]"]').val() || '0.00'));
                tot = fee+(parseFloat(eps) || 0.0);
                $('input[name="fee_total_amount"]').val(tot.toFixed(2));
            }
        });
    });
    
    $('input[name="main[registry_id]"]').on('input',function(){
        $.ajax({
            type: "POST",
            url: "/archy/app.php?do_action=check_candidate",
            data: 'registry_id='+$(this).val(),
            dataType: 'json',
            cache: false,
            success: function(response){
                    if(response.is_already_candidate){
                            alert("La candidatura per l'anagrafica "+$('#archy_memberships_examinee_registry_id').val()+' è già in corso di valutazione.');
                            $('#archy_memberships_examinee_registry_id, input[name="main[registry_id]"]').val('');
                    }
                    if(response.is_already_member){
                            alert($('#archy_memberships_examinee_registry_id').val()+' è già iscritto come associato.');
                            $('#archy_memberships_examinee_registry_id, input[name="main[registry_id]"]').val('');
                    }
            }
        });
	});
}
</script>