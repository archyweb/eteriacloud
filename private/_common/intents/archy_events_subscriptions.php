<?php

$intent=new stdClass();
$intent->table='archy_events_subscriptions';
$intent->title='Iscrizioni';
$intent->singular='Iscrizione';
$intent->add='Nuova Iscrizione';
$intent->view_title='archy_events_subscriptions.subscriber_full_name @ archy_events.title';
$intent->joins=[
	'archy_events'=>'archy_events_subscriptions.event_id=archy_events.id',
        'archy_discounts'=>'archy_events_subscriptions.discount_id=archy_discounts.id'
];
$intent->columns=array(
	'archy_events_subscriptions.id'=>array(
		'label'=>'ID',
		'type'=>'text',
		'hidden'=>''
	),
    // rivedere ---------------
    // Non andrebbe linkato ad archy_registry ???
	'archy_events_subscriptions.subscriber_id'=>array(
		'label'=>'ID Iscritto',
		'type'=>'text',
		'required'=>true,
		'hidden'=>''
	),
	'archy_events_subscriptions.subscriber_full_name'=>array(
		'label'=>'Nome completo iscritto',
		'type'=>'text',
		'hidden'=>''
	),
    // ------------------------
	'archy_events_subscriptions.event_id'=>array(
		'label'=>'Attività',
		'type'=>'search',
		'intent'=>'archy_events',
		'lookup_text'=>'archy_events.title',
		'required'=>true,
		'hidden'=>''
	),
    'archy_events_subscriptions.discount_id'=>array(
		'label'=>'Sconto',
		'type'=>'search',
		'intent'=>'archy_discounts',
		'lookup_text'=>'archy_discounts.title',
        'hidden'=>''
	),
	'archy_events_subscriptions.events_price'=>array(
		'label'=>'Totale eventi',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_events_subscriptions.extras_price'=>array(
		'label'=>'Totale extra',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
    'archy_events_subscriptions.discount_amount'=>array(
		'label'=>'Totale sconto',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
	'archy_events_subscriptions.subscription_total'=>array(
		'label'=>'Totale iscrizione',
		'type'=>'amount',
		'formatter'=>function($d,$row){return App::currency($d);},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
        'archy_events_subscriptions.sessions'=>array(
            'label'=>'Sessioni',
            'type'=>'text',
            'formatter'=>function($d,$row){
			if($d!=''):
                            $response = '';
                            $sessions=unserialize($d);                            
                            foreach($sessions as $index=>$session):
                                $response .= $session['event'].'<br>';
                            endforeach;
                        endif;
                        return $response;
            },
            'formatter_hide_in_form'=>true,
            'hidden'=>''
        ),
	'archy_events_subscriptions.payments'=>array(
		'label'=>'Pagamenti',
		'type'=>'text',
		'formatter'=>function($d,$row){
			if($d!=''):
				$response='<table class="table-condensed table-bordered">';
				$payments=unserialize($d);
				$total_paid=0;
				if(is_array(@$payments['paid'])):
					foreach($payments['paid'] as $index=>$paid):
                                    
                                            $paid=$paid;
						$response.='
							<tr>
								<td><small>'.$payments['payment_date'][$index].'</small></td><td><small>'.str_replace(" ","&nbsp;",App::currency($paid)).'</small></td>
							</tr>
						';
						$total_paid+=$paid;
					endforeach;
				endif;
				$subscription_total=(float)@$row['subscription_total'];
				switch(true):
					case $total_paid==0:
                        $class='text-danger';
                        $status='Non pagato';
                        break;
					case $total_paid<$subscription_total:
                        $class='text-warning';
                        $status='Incompleto';
                        break;
					case $total_paid>=$subscription_total:
                        $class='text-success';
                        $status='Pagato';
                        break;
				endswitch;
				$response.='<tr><td><small><strong>Totale</strong></small></td><td><small><strong>'.App::currency($total_paid).'</strong></small></td></tr>';
				$response.='<tr><td colspan="2"><small>Status pagamento: <span class="'.$class.'">'.$status.'</span></small></td></tr>';
				$response.='</table>';
			endif;
			return $response;
		},
		'excel_formatter'=>function($d,$row){
			$response=[];
			if($d!=''):
				$payments=unserialize($d);
				if(is_array(@$payments['paid'])):
					foreach($payments['paid'] as $index=>$paid):
						$response[]=$payments['payment_date'][$index]." ".App::currency($paid);
					endforeach;
				endif;
			endif;
			return join("\n",$response);
		},
		'formatter_hide_in_form'=>true,
		'hidden'=>''
	),
    /*
    'archy_events_subscriptions.accounted'=>array(
		'label'=>'Presente',
		'type'=>'checkbox',
		'formatter'=>function($d,$row){return App::checkbox($d);},
		'formatter_hide_in_form'=>true,
        'hidden'=>''
	),
    */
	'archy_events_subscriptions.created_at'=>array(
		'label'=>'Data creazione',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	),
	'archy_events_subscriptions.updated_at'=>array(
		'label'=>'Ultima modifica',
		'type'=>'date',
		'formatter'=>function($d,$row){return App::latin_date_time($d);},
		'hidden'=>''
	)
);
$intent->default_columns=array(
	'archy_events_subscriptions.id',
	'archy_events_subscriptions.subscriber_full_name',
	'archy_events.title',
        'archy_events_subscriptions.sessions',
	'archy_events_subscriptions.events_price',
	'archy_events_subscriptions.extras_price',
    'archy_events_subscriptions.discount_amount',
	'archy_events_subscriptions.subscription_total',
	'archy_events_subscriptions.payments'
);
$intent->extra_action='store_events_subscriptions';
$intent->include_in_form='intents/includes/events_subscriptions_form.php';
$app->merge_intents($intent);
?>