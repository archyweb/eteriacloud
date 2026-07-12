<?php if (!isset($_GET['id'])) : ?>
    <hr>
    <div class="container">
        <div class="row">
            <div class="col-md-12" style="text-align:center" id="member_status"></div>
        </div>
        <div class="row" id="new_dates" style="display:none;">
            <div class="col-md-3 col-md-offset-3">
                <div class="form-grup">
                    <label for="new_begin">Nuovo inizio validità</label>
                    <div class="input-group">
                        <input type="text" class="form-control date required" placeholder="__/__/____" name="begin" id="new_begin">
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-grup">
                    <label for="new_end">Nuova fine validità</label>
                    <div class="input-group">
                        <input type="text" class="form-control date required" placeholder="__/__/____" name="end" id="new_end">
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="status" id="status" value="">
    <script type="text/javascript">
        window.onload = function() {
            $('input[name="main[services_memberships_id]"]').on('change input', function() {
                $.ajax({
                    type: "POST",
                    url: "/archy/app.php?do_action=get_service_subscription_info&id="+$(this).val()+'&data='+$('input[name="main[date]"]').val(),
                    dataType: 'json',
                    cache: false,
                    success: function(row) {
                        var validita='<h4>Validità abbonamento al servizio:<br>'+
                                    'dal <b>' + row.membership_begin + '</b> al <b>' + row.membership_end + '</b></h4>';
                        if (row.status == '4') { // cessato
                            $('input[name="main[member_id]"], #archy_subscriptions_member_id').val('');
                            alert('L\'abbonato \'' + row.registry.label + '\' ha cessato in servizio, l\'abbonamento non può essere rinnovato.');
                        } else if (row.status == '6') { // in aggiornamento
                            $('input[name="main[member_id]"], #archy_subscriptions_member_id').val('');
                            alert('L\'abbonamento di \'' + row.registry.label + '\' è in aggiornamento. Non è possibile registrare un nuovo versamento.');
                        } else {
                            if (row.status == '5') { // in attesa primo versamento
                                validita='';
                            }
                            row.status = '6'; // in aggiornamento
                            $('#member_status').html(
                                '<h3>Cliente: <b>' + row.registry.label + '</b></h3>'+
                                validita+
                                '<h3>Status: <b>' + row.status_label + '</b></h3>'+
                                '<hr>');
                            $('#new_begin').val(row.new_begin);
                            $('#new_end').val(row.new_end);
                            $('#status').val(row.status);
                            $('#new_end').prop("readonly", row.new_end_readonly);
                            $('#archy_services_subscriptions_amount').val(row.service_amount);
                            $('#new_dates').css('display', row.dates_display);
                        }
                    }
                });

            });

        }
    </script>
<?php endif ?>