<?php

// $_GET['id'] => ID sessione 
if (isset($_GET['id'])) :
    $ev = new Event();
    $session=$ev->get_session($_GET['id']);
    $ev = new Event($session->event_id);
    $subscriptions=$ev->get_session_subscriptions($_GET['id']);    
endif;

// ----------------


if (isset($_GET['id'])) :
    $event_title = $ev->title;
    //$are_extras_editable = $event->are_extras_editable($_GET['id']);
else :
    $event_title = "";
    //$are_extras_editable = true;
endif;

?>

<div class="table-responsive scrollbar">
   
            <?php
            if (isset($_GET['id'])) :
                $header = '
                    <tr>
                            <th scope="col">Iscritto</th>
                            <th scope="col">Crediti maturati</th>
                            <th scope="col">Saldo pagamenti</th>
                            <th scope="col">Presenze</th>
                            <th scope="col">Att. sessione</th>
                            <th scope="col">Att. evento</th>
                    </tr>
            ';
            ?>
			<?php //print_r($subscriptions); ?>
                <table class="table table-condensed table-hover" id="subscribers">
                    <colgroup>
                    <col class="bg-primary-subtle" />
                    <col />
                    <col />
                    <col />
                    <col />
                    <col />
                    </colgroup>
                    <thead><?php echo $header ?></thead>
                    <tbody>
                        <?php foreach ($subscriptions as $subscription) :
                            // var_dump('subscription',$subscription);
                        ?>
                            <tr>
                                <td><?php echo $subscription->subscriber_full_name ?></td>
                                <td class="accrued_credits" id="accrued_credits_<?php echo $subscription->id; ?>"><?php echo $subscription->accrued_credits; ?></td>
                                <td>									
                                    <table class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Dovuto</th>
                                                <th scope="col">Pagato</th>
                                                <th scope="col">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo App::currency($subscription->subscription_total); ?></td>                                            
                                                <td><?php echo App::currency($subscription->total_paid); ?></td>
                                                <td <?php echo $subscription->balance < 0 ? 'style="color:red"' : '' ?>><?php echo App::currency($subscription->balance); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <div class="form-group ">
                                        <div>
                                            <?php
                                            $tmp=unserialize($subscription->accounted);
                                                $dis=false;
                                            foreach($tmp as $t) {
                                                if($t['session_id']==$_GET['id']) {
                                                    $dis=true;
                                                    break;
                                                }
                                            }
                                            if(!$dis) {
                                            ?>
                                            <label class="form-label" for="subscription">Presente:</label> <span id="flag_presenza_<?php echo $subscription->id; ?>">no</span><br>
                                            <button class="accounted btn btn-success" id="accounted_<?php echo $subscription->id; ?>" 
                                                data-credits="<?php echo $session->credits; ?>"
                                                data-event_id="<?php echo $subscription->event_id; ?>"
                                                data-session_title="<?php echo $session->title; ?>"
                                                data-session_id="<?php echo $_GET['id']; ?>"
                                                data-subscription_id="<?php echo $subscription->id; ?>"
                                                data-subscriber_id="<?php echo $subscription->subscriber_id; ?>"
                                            >Convalida presenza</button>
                                            <?php } else { ?>
                                                <label class="form-label">Presente:</label> sì
                                            <?php } ?>
                                        </div>

                                        <input type="hidden" name="session[<?php echo $subscription->id; ?>][accounted]" value="<?php echo $subscription->accounted; ?>">
                                        <input type="hidden" id="session_event_id" value="<?php echo $subscription->event_id; ?>">
                                        <input type="hidden" id="session_id" value="<?php echo $_GET['id']; ?>">
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $sent_on_arr=unserialize($subscription->sent_on);
                                        $btn_send_session_class=$btn_send_event_class="btn-warning";
                                        $title_send_session=$title_send_event="Invia";
                                        foreach($sent_on_arr as $sent) {
                                            if(isset($sent['session_id'])){ $btn_send_session_class="btn-success"; $title_send_session="Reinvia"; }
                                            if(isset($sent['event_id'])){ $btn_send_event_class="btn-success"; $title_send_event="Reinvia"; }
                                        }
                                        
                                        $file_path_event_session="/assets/documents/events/".$subscription->id."_event_".$subscription->event_id."_session_".$_GET['id']."_".$subscription->subscriber_id.".pdf";
                                        $physical_path_event_session=$app->subdomain_root.$file_path_event_session;
                                        if (file_exists($physical_path_event_session)) { ?>
                                                <a class="btn btn-sm btn-primary certificate" data-toggle="tooltip" 
                                                    title="Attestato Sessione <?php echo $session->title; ?>" 
                                                    data-session_id="<?php echo $_GET['id'] ?>" 
                                                    data-subscription_id="<?php echo $subscription->id ?>" 
                                                    href="<?php echo $file_path_event_session; ?>" target="_blank"><span class="far fa-file-archive"></span></a>
                                    
                                                <a class="btn btn-sm <?php echo $btn_send_session_class; ?>"
                                                    href="/archy/app.php?do_action=send_events_certificate&subscription_id=<?php echo $subscription->id; ?>&session_id=<?php echo $_GET['id']; ?>" 
                                                    data-toggle="tooltip" title="<?php echo $title_send_session; ?> attestato"
                                                    onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re l\'attestato?\')"
                                                    ><span class="fas fa-location-arrow"></span></a>    
                                                        
                                        <?php } ?>	                                    
                                </td>
                                <td> 
                                    <?php 
                                        
                                        $file_path_event="/assets/documents/events/".$subscription->id."_event_".$subscription->event_id."_".$subscription->subscriber_id.".pdf";
                                        $physical_path_event=$app->subdomain_root.$file_path_event;
                                        if (file_exists($physical_path_event)) { ?>
                                                <a class="btn btn-sm btn-success certificate" data-toggle="tooltip" 
                                                    title="Attestato Evento" 
                                                    data-session_id="<?php echo $_GET['id'] ?>" 
                                                    data-subscription_id="<?php echo $subscription->id ?>"
                                                    href="<?php echo $file_path_event; ?>" target="_blank"><span class="far fa-file-archive"></span></a>
                                                    
                                                <a class="btn btn-sm <?php echo $btn_send_event_class; ?>"
                                                    href="/archy/app.php?do_action=send_events_certificate&subscription_id=<?php echo $subscription->id; ?>" 
                                                    data-toggle="tooltip" title="<?php echo $title_send_event; ?> attestato"
                                                    onClick="return confirm(\'Si è certi di voler '.strtolower($title).'re l\'attestato?\')"
                                                    ><span class="fas fa-location-arrow"></span></a>    
                                                
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot><?php echo $header ?></tfoot>
                </table>
            <?php
            endif;
            ?>

</div>
<script type="text/javascript">
    
    
    
    window.addEventListener ? 
    window.addEventListener("load",myFunction,false) : 
    window.attachEvent && window.attachEvent("onload",myFunction);

    function myFunction () {

        <?php if (isset($_GET['id'])) : ?>
            $('.accounted').click(function($ev) {

                $ev.preventDefault();

                var credits = Number($(this).data('credits'));
                var event_id = Number($(this).data('event_id'));
                var session_id = Number($(this).data('session_id'));
                var session_title = $(this).data('session_title');
                var subscription_id = Number($(this).data('subscription_id'));
                var subscriber_id = Number($(this).data('subscriber_id'));
            
                if( confirm('Confermare presenza?' )) {
                    $.ajax({
                        type: "POST",
                        url: "/archy/app.php?do_action=account_for_event",
                        data: { 
                            'event_id': event_id,
                            'session_id': session_id,
                            'session_title': session_title,
                            'subscription_id': subscription_id,
                            'subscriber_id': subscriber_id,
                            'credits': credits
                        },
                        cache: false,
                        success: function(resp) {                           
                            if(resp=="OK") {
                                location.reload();
                            }
                        }
                    });
                } 
        
            });
            
        <?php endif; ?>

    }
</script>

<script type="text/html" id="extra_tpl">
    <?php
    /*
    $event_index = '';
    $extra['description'] = '';
    $extra['amount'] = '';
    $event['archy_activities_events.extras']['editable_extras'] = true;
    include 'event_extra_tpl.php'
    */
    ?>
</script>
<style type="text/css">
    .delete_extra {
        font-size: 14px;
        cursor: pointer;
        border-radius: 25px;
        padding: 8px;
        background: #999;
        color: #fff;
    }

    #extras_table .form-group {
        margin-bottom: 0;
        min-height: 0;
    }

    #evt_count {
        font-weight: bold
    }

    .msg {
        display: none;
    }

    #subscribers tfoot th .form-control {
        width: 100%;
    }
</style>

<!-- Modal -->
<div class="modal fade" id="pdfOptions" tabindex="-1" role="dialog" aria-labelledby="pdfOptionsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="pdfOptionsLabel">Opzioni PDF Attestati</h4>
            </div>
            <div class="modal-body" id="pdfOptionsWrapper">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-lg-4">
                        <div class="form-group">
                            <label for="orientation">Orientamento</label>
                            <select class="form-control" name="orientation" id="orientation">
                                <option value="P">Verticale</option>
                                <option value="L">Orizzontale</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-lg-4">
                        <div class="form-group">
                            <label for="sheet">Foglio</label>
                            <select class="form-control" name="sheet" id="sheet">
                                <option value="A6">A6</option>
                                <option value="A5">A5</option>
                                <option value="A4" selected>A4</option>
                                <option value="A3">A3</option>
                                <option value="A2">A2</option>
                                <option value="A1">A1</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-lg-4">
                        <div class="form-group">
                            <label for="size">Carattere</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="size" name="size" value="12">
                                <div class="input-group-addon">pt</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <legend style="margin:20px 15px; width:auto">Margini</legend>
                    <div class="col-xs-12 col-sm-6 col-lg-3">
                        <div class="form-group">
                            <label for="up">Superiore</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="up" name="up" value="50">
                                <div class="input-group-addon">pt</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-lg-3">
                        <div class="form-group">
                            <label for="down">Inferiore</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="down" name="down" value="70">
                                <div class="input-group-addon">pt</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-lg-3">
                        <div class="form-group">
                            <label for="left">Sinistro</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="left" name="left" value="50">
                                <div class="input-group-addon">pt</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-lg-3">
                        <div class="form-group">
                            <label for="right">Destro</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="right" name="right" value="50">
                                <div class="input-group-addon">pt</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <legend style="margin:20px 0; width:auto">Crea un unico pdf per la stampa</legend>
                        <button type="button" class="btn btn-primary" id="bulk_event" data-event_id="<?php echo @$_GET['id'] ?>">Attestati Evento</button>
                        <button type="button" class="btn btn-primary" id="bulk_activity" data-activity_id="<?php echo @$event_id ?>">Attestati Attività</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="applyPDFOptions">Finito</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->