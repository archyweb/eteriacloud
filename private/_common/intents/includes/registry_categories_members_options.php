<?php

$month_names = $app->months; // ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];
$month_amount = unserialize($data['hidden_columns']['archy_registry_categories.splitting_months']['value']);

?>


<?php
if (isset($_GET['id'])) :
    $subscription_fee = mysqli_fetch_object($app->db->query("select subscription_fee from archy_registry_categories where id=" . $_GET['id']))->subscription_fee;
else :
    $subscription_fee = '';
endif;
?>


<div id="members_options" style="display:none">
    <div class="container">
        <hr>
        <h3>Opzioni categoria associato</h3>

        <div class="form-group">
            <label for="subscription_fee">Importo quota associativa <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="" data-original-title="Campo richiesto"></span></label>
            <div class="input-group">
                <input type="text" class="form-control numbers_only" id="subscription_fee" name="subscription_fee" autocomplete="off" value="<?php echo $subscription_fee ?>">
                <div class="input-group-addon"><i class="glyphicon glyphicon-euro"></i></div>
            </div>
        </div>
        <?php if($app->config['association_splitting']): ?>
            <?php $is_splitted=(bool)$data['hidden_columns']['archy_registry_categories.splitting']['value']; ?>
            <div class="form-group ">
                <label class="control-label" for="archy_registry_categories_splitting">Frazionamento prima quota </label>
                <div><label class="checkbox-inline"><input type="checkbox" id="archy_registry_categories_splitting" class=" cbox" <?php echo $is_splitted?' checked':''?>> Frazionamento </label></div>
                <input type="hidden" name="main[splitting]" value="<?php echo $is_splitted; ?>">
            </div>

            <div class="container" id="splitting_wrapper" style="display:none">
                <fieldset>
                    <legend>Frazionamento prima quota associativa</legend>
                    <div class="row">
                        <?php foreach ($month_names as $m) : ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label" for="archy_registry_categories_splitting_months"><?php echo $m; ?>
                                        <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="" data-original-title="Campo richiesto"></span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control numbers_only required" id="splitting_months[<?php echo $m; ?>]" name="splitting_months[<?php echo $m; ?>]" value="<?php echo $month_amount[$m]; ?>" placeholder="Importo">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-euro"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            </div>
        <?php endif; ?>

    </div>
</div>


<script type="text/javascript">
    window.onload = function() {
        $('#archy_registry_categories_domain').change(function() {
            if ($(this).val() == 'memberships') {
                $('#members_options').slideDown();
                $('#subscription_fee').addClass('required');
            } else {
                $('#members_options').slideUp();
                $('#subscription_fee').removeClass('required');
            }
        }).trigger('change');

        $('#archy_registry_categories_splitting').click(function() {
            if ($(this).is(':checked')) {
                $('#splitting_wrapper').slideDown();
                $('#splitting_wrapper .form-control').addClass('required');
            } else {
                $('#splitting_wrapper').slideUp();
                $('#splitting_wrapper .form-control').removeClass('required');
            }
        });
        if ($('#archy_registry_categories_splitting').is(':checked')) {
            $('#splitting_wrapper').slideDown();
        } else {
            $('#splitting_wrapper .form-control').removeClass('required');
        }
    }
</script>