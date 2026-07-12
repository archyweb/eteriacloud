<div class="row">

    <div class="col-md-7">

        <div class="col-md-4">
            <div class="form-group ">
                <label for="archy_events_events_speaker_id">Iscritto <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="Campo richiesto"></span></label>
                <div class="input-group">
                    <input type="text" class="form-control search required" readonly name="main[subscriber_full_name]" value="<?php echo @$data['hidden_columns']['archy_events_subscriptions.subscriber_full_name']['value'] ?>">
                    <div class="input-group-addon clear" id="clear_subscriber"><span class="glyphicon glyphicon-remove"></span></div>
                    <div class="input-group-addon lookup" id="subscriber_type_target" data-toggle="modal" data-target="#search_modal" data-intent="archy_registry" data-domain="" data-intent_name="Anagrafiche" data-lookup="archy_registry.label"> <span class="glyphicon glyphicon-search"></span> </div>
                    <input type="hidden" name="main[subscriber_id]" value="<?php echo @$data['hidden_columns']['archy_events_subscriptions.subscriber_id']['value'] ?>">
                </div>
            </div>
        </div>
		
		<?php
        echo $app::form_element($data['hidden_columns']['archy_events_subscriptions.event_id'], "col-md-4");
        ?>
		
        <?php
        echo $app::form_element($data['hidden_columns']['archy_events_subscriptions.discount_id'], "col-md-4");
        ?> 
        
        <div class="col-md-4"></div>
        <div class="col-md-4"><label style="color:#00f;">Ultima qualifica</label> <input type="text" class="form-control" name="rating_title" id="rating_title"> <input type="hidden" name="rating_id" id="rating_id"><input type="hidden" name="ratings_id" id="ratings_id"></div>
        <div class="col-md-4"><label style="color:#00f;">Status associato</label> <input type="text" class="form-control" name="subscriber_status_desc" id="subscriber_status_desc"> <input type="hidden" name="subscriber_status" id="subscriber_status"></div> 
		
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Totale Eventi</th>
                            <th>Totale Extra</th>
                            <th>Sconto</th>
                            <th>Totale Iscrizione</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="events_total"><?php echo App::currency(@$data['hidden_columns']['archy_events_subscriptions.events_price']['value']) ?></td>
                            <td id="extras_total"><?php echo App::currency(@$data['hidden_columns']['archy_events_subscriptions.extras_price']['value']) ?></td>
                            <td id="discount_total"><?php echo App::currency(@$data['hidden_columns']['archy_events_subscriptions.discount_amount']['value']) ?></td>
                            <td id="subscription_total"><?php echo App::currency(@$data['hidden_columns']['archy_events_subscriptions.subscription_total']['value']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-12">
            <div class="table-responsive">
                <h4>Pagamenti</h4>
                <table class="table table-striped" id="payments_table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Importo</th>
                            <th>Tipologia</th>
                            <th>Libro prima nota</th>
                            <th>MOdalità pagamento</th>
                        </tr>
                    </thead>
                    <tbody id="payment_tpl">
                        <?php 
                        $date = $payments = '';
                        $id=null;
                        $data_payments = $data['hidden_columns']['archy_events_subscriptions.payments']['value'];
                        //echo "@@@@@".$data_payments;  
                        if (isset($_GET['id'])){ 
                            $id = $_GET['id'];                            
                        }                        
                        $app->load_payments_tpl($id,$data_payments); 
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" style="text-align:center">
                                <button type="button" class="btn btn-default btn-sm add_payment"><i class="glyphicon glyphicon-plus"></i> Aggiungi pagamento</button>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    <input type="hidden" name="sconti_type" value="" id="sconti_type">
    <input type="hidden" name="sconti_amount" value="" id="sconti_amount">
    <input type="hidden" name="main[events_price]" value="<?php echo @$data['hidden_columns']['archy_events_subscriptions.events_price']['value'] ?>" id="events_price">
    <input type="hidden" name="main[extras_price]" value="<?php echo @$data['hidden_columns']['archy_events_subscriptions.extras_price']['value'] ?>" id="extras_price">
    <input type="hidden" name="main[discount_amount]" value="<?php echo @$data['hidden_columns']['archy_events_subscriptions.discount_amount']['value'] ?>" id="discount_amount">
    <input type="hidden" name="main[subscription_total]" value="<?php echo @$data['hidden_columns']['archy_events_subscriptions.subscription_total']['value'] ?>" id="total">

    <div class="col-md-5" id="events_wrapper">
        <?php
        if (isset($_GET['id'])) :
            $app->load_events();
        endif;
        ?>
    </div>

</div>
<script>
    var activity_changed;
    var subscriber_changed;
    var discount_changed;
    var loaded_activity = '<?php echo isset($_GET['id']) ? $data['hidden_columns']['archy_events_subscriptions.event_id']['value'] : '' ?>';
    window.onload = function() {
        
        $('[name="main[subscriber_full_name]"]').change(function() {
            console.log('aaa'+$(this).val());
        }).trigger('change');

        $('body').on('click', '.event_cbox,.extra_cbox', function() {
            update_subscription_price();
        });

        $('body').on('click', '#all_events', function() {
            $('.event_cbox').not(':disabled').prop('checked', true);
            update_subscription_price();
        });
        $('body').on('click', '#all_extras', function() {
            $('.extra_cbox').prop('checked', true);
            update_subscription_price();
        });

        $('#search_modal').on('show.bs.modal', function(event) {
            var modal_intent = $(event.relatedTarget).data('intent');
            activity_changed = modal_intent == 'archy_events';
            subscriber_changed = modal_intent == 'archy_registry';
            discount_changed = modal_intent == 'archy_discounts';
            console.log("show modal, intent="+modal_intent);
        });

        $('#search_modal').on('hidden.bs.modal', function(e) {
            //console.log("hidden modal");
            if (activity_changed) {
				var subscriber_id = $('[name="main[subscriber_id]"]').val();
				//alert(subscriber_id);
                var event_id = $('[name="main[event_id]"]').val();
                if (event_id != loaded_activity) {
                    loaded_activity = event_id;
                    load_events(event_id,subscriber_id);
                }
            }
            if (subscriber_changed) {
                var subscriber_id = $('[name="main[subscriber_id]"]').val();
                if (subscriber_id != loaded_activity) {
                    loaded_activity = subscriber_id;
                    load_sconto_by_subscriber(subscriber_id);
                    //load_subscriber_ratings(subscriber_id); // carico i ratings
                    load_subscriber_status(subscriber_id);
                    update_subscription_price();

                    // VFVFVFVF load_ratings(subscriber_id);
                }
            }
            if (discount_changed) {
                var discount_id = $('[name="main[discount_id]"]').val();
                if (discount_id != loaded_activity) {
                    loaded_activity = discount_id;
                    load_sconto(discount_id);
                    update_subscription_price();
                }
            }
        });

        $('body').on('click', '.add_payment', function() {
            
            $.ajax({
                type: "POST",
                url: root + "archy/app.php?do_action=load_payments_tpl",
                data: 'add_ajax=1',
                cache: false,
                success: function(html) {
                    $('#payment_tpl').append(html);
                    dates();
                    //$('tbody', $(this).parents('table')).append($('#payment_tpl').html());
                }
            });
            
        });
        $('body').on('click', '.delete_payment', function() {
            if (confirm("Eliminare questo pagamento?")) {
                $(this).parents('tr').fadeOut(function() {
                    $(this).remove()
                });
            }
        });

    }

    function load_events(event_id,subscriber_id) {
        $.ajax({
            type: "POST",
            url: root + "archy/app.php?do_action=load_events",
            data: 'event_id=' + event_id + '&subscriber_id=' +subscriber_id,
            cache: false,
            success: function(html) {
                $('#events_wrapper').html(html);
            }
        });
    }

    function load_sconto_by_subscriber(subscriber_id) {
        $.ajax({
            type: "POST",
            url: root + "archy/app.php?do_action=load_sconto_by_subscriber",
            data: 'subscriber_id=' + subscriber_id,
            cache: false,
            success: function(discount) {
                console.log('discount='+discount);
                if(discount!='null'){
                    //console.log('ret='+discount);
                    const ret=JSON.parse(discount);
                    console.log('ret='+ret);
                    $('#archy_events_subscriptions_discount_id_value_holder').val(ret.id);
                    $('#archy_events_subscriptions_discount_id').val(ret.title);
                    $('#sconti_type').val(ret.type);
                    $('#sconti_amount').val(ret.amount);
                    $('#rating_id').val(ret.rating_id);
                    //$('#rating_title').val(ret.rating_title);
                } else {
                    $('#archy_events_subscriptions_discount_id_value_holder').val('');
                    $('#archy_events_subscriptions_discount_id').val('');
                    $('#sconti_type').val('');
                    $('#sconti_amount').val('');
                    $('#rating_id').val('');
                    //$('#rating_title').val('');
                }

            }
        });
    }

    function load_sconto(discount_id) {
        $.ajax({
            type: "POST",
            url: root + "archy/app.php?do_action=load_sconto",
            data: 'discount_id=' + discount_id,
            cache: false,
            success: function(discount) {
                if(discount!='null'){
                    //console.log(discount);
                    const ret=JSON.parse(discount);
                    $('#archy_events_subscriptions_discount_id_value_holder').val(ret.id);
                    $('#archy_events_subscriptions_discount_id').val(ret.title);
                    $('#sconti_type').val(ret.type);
                    $('#sconti_amount').val(ret.amount);
                    //$('#rating_id').val();
                    //$('#rating_title').val();
                }
            }
        });
    }

    function load_subscriber_status(subscriber_id) {
		
        $.ajax({
            type: "POST",
            url: root + "archy/app.php?do_action=load_subscriber_status",
            data: 'subscriber_id=' + subscriber_id,
            cache: false,
            success: function(info) {
                if(info!=null){
                    const ret=JSON.parse(info);
                    console.log(info);
                    $('#subscriber_status').val(ret.status);
                    $('#subscriber_status_desc').val(ret.status_desc);
                }
            }
        });
    }
	
	function load_subscriber_ratings(subscriber_id) {		
		$.ajax({
            type: "POST",
            url: root + "archy/app.php?do_action=load_subscriber_ratings",
            data: 'subscriber_id=' + subscriber_id,
            cache: false,
            success: function(info) {
                if(info!=null){
                    const ret=JSON.parse(info);
                    //console.log('ratings='+info);
                    //$('#ratings_id').val(ret);
                    //$('#subscriber_status_desc').val(ret.status_desc);
                }
            }
        });
	}
    
    function update_subscription_price() {
		
        var events_total = extras_total = discount = subscription_total = 0;
        var subscription_subtotal = 0;
        var subscriber_id = $('[name="main[subscriber_id]"]').val();
		
        // sconto associati
        var subscriber_status = $('#subscriber_status').val();
        //var subscriber_rating_id = $('#rating_id').val();
        //var subscriber_ratings = $('#ratings_id').val();
        //var subscriber_ratings_array = subscriber_ratings.split(",");
		//console.log(subscriber_ratings_array);
        $('.event_cbox').each(function() {
			
			
            //var required_rating = $(this).data('required_rating');
            if ($(this).is(':checked')) {
				
               // console.log('ratings'+ratings);
                //console.log('required_rating:'+required_rating);
                
				//if ($.inArray( required_rating, subscriber_ratings_array) >= 0 || required_rating=='') {
                    events_total += subscriber_status == '1' ? Number($(this).data('members_price')) : Number($(this).data('price'));
                //} else {
                    //alert('Rating non sufficiente per questa scelta');
                    //$(this).attr('checked',false);
                //}
            }
        });

        $('.extra_cbox').each(function() {
            if ($(this).is(':checked')) {
                extras_total += Number($(this).data('amount'));
            }
        });
            
        subscription_subtotal = events_total;    
        var discount_id = $('[name="main[discount_id]"]').val();
        if (discount_id != '') {
            //console.log('discount id:'+discount_id);
            discountType=$('#sconti_type').val();
            discountAmount=$('#sconti_amount').val();
           // console.log('discount type:'+discountType);
            ///console.log('discount amount:'+discountAmount);
            if(discountType=="fixed"){
                discount=-discountAmount; // Sconto = quota
            } else {
                discount=-subscription_subtotal*(discountAmount/100); // Sconto = percentuale
            }
            //console.log('discount:'+discount);
        }
        
        // CALCOLO TOTALI
        //subscription_subtotal = events_total + extras_total;
        
        subscription_total = (events_total + discount) + extras_total;

        $('#events_total').text(format_amount(events_total));
        $('#extras_total').text(format_amount(extras_total));
        $('#discount_total').text(format_amount(discount));
        $('#subscription_total').text(format_amount(subscription_total));
        //hidden fields
        $('#events_price').val(events_total);
        $('#extras_price').val(extras_total);
        $('#discount_amount').val(discount);
        $('#total').val(subscription_total);
    }
</script>
<script type="text/html" id="payment_tpl">
    <?php //$date = $payments = '';
    //include 'events_subscriptions_form_payments_tpl.php' ?>
</script>
<style type="text/css">
    .price {
        display: none;
    }
    .delete_payment {
        font-size: 14px;
        cursor: pointer;
        border-radius: 25px;
        padding: 8px;
        background: #999;
        color: #fff;
    }
    #payments_table .form-group {
        margin-bottom: 0;
        min-height: 0;
    }
</style>