<?php
$db=$app->get_db();
$db->query("
	create table if not exists archy_notice_broadcasts (
		id int unsigned not null auto_increment,
		title varchar(190) not null default '',
		notice mediumtext null,
		category varchar(32) not null default 'general',
		priority varchar(16) not null default 'normal',
		recipients_mode varchar(32) not null default 'all',
		target_count int unsigned not null default 0,
		success_count int unsigned not null default 0,
		failed_count int unsigned not null default 0,
		email_target int unsigned not null default 0,
		email_success int unsigned not null default 0,
		email_failed int unsigned not null default 0,
		customers_json mediumtext null,
		failed_customers_json mediumtext null,
		created_by varchar(190) not null default '',
		created_at timestamp not null default current_timestamp,
		updated_at timestamp null default null on update current_timestamp,
		primary key (id),
		key created_at (created_at),
		key category (category),
		key priority (priority)
	) engine=InnoDB default charset=utf8mb4
");

$header='
	<tr>
		<th>ID</th>
		<th>Data</th>
		<th>Titolo</th>
		<th>Categoria</th>
		<th>Priorita</th>
		<th>Clienti raggiunti</th>
		<th>E-mail</th>
		<th>Utente</th>
	</tr>
';

function notice_broadcast_escape($value){
	return htmlspecialchars((string)$value,ENT_QUOTES,'UTF-8');
}

function notice_broadcast_badge($label,$class){
	return '<span class="badge bg-'.$class.'">'.notice_broadcast_escape($label).'</span>';
}

function notice_broadcast_customer_list($json){
	$customers=json_decode((string)$json,true);
	if(!is_array($customers) || count($customers)==0):
		return '<span class="text-600">-</span>';
	endif;
	$html=[];
	foreach($customers as $customer):
		$name=isset($customer['customer'])? $customer['customer'] : '';
		$subdomain=isset($customer['subdomain']) && $customer['subdomain']!=''? ' <span class="text-600">('.notice_broadcast_escape($customer['subdomain']).')</span>' : '';
		$html[]='<div class="text-nowrap">'.notice_broadcast_escape($name).$subdomain.'</div>';
	endforeach;
	return join('',$html);
}

$rows='';
$broadcasts=$db->query("select * from archy_notice_broadcasts order by id desc");
if($broadcasts):
	while($broadcast=mysqli_fetch_object($broadcasts)):
		$category=$broadcast->category!=''? $broadcast->category : 'general';
		$priority=$broadcast->priority!=''? $broadcast->priority : 'normal';
		$priority_class=$priority=='urgent'? 'danger' : ($priority=='low'? 'secondary' : 'info');
		$status_class=((int)$broadcast->failed_count>0 || (int)$broadcast->email_failed>0)? 'warning':'success';
		$customers_count=(int)$broadcast->success_count.'/'.(int)$broadcast->target_count;
		$email_count=(int)$broadcast->email_success.'/'.(int)$broadcast->email_target;
		$rows.='
			<tr>
				<td>'.(int)$broadcast->id.'</td>
				<td data-order="'.notice_broadcast_escape($broadcast->created_at).'">'.App::latin_date_time($broadcast->created_at).'</td>
				<td>
					<strong>'.notice_broadcast_escape($broadcast->title).'</strong>
					<div class="fs-11 text-600">'.notice_broadcast_badge($customers_count.' clienti',$status_class).' '.notice_broadcast_escape($broadcast->recipients_mode).'</div>
				</td>
				<td>'.notice_broadcast_escape($category).'</td>
				<td>'.notice_broadcast_badge($priority,$priority_class).'</td>
				<td>'.notice_broadcast_customer_list($broadcast->customers_json).'</td>
				<td data-order="'.(int)$broadcast->email_success.'">'.notice_broadcast_badge($email_count,'primary').'</td>
				<td>'.notice_broadcast_escape($broadcast->created_by).'</td>
			</tr>
		';
	endwhile;
endif;
?>

<div class="row g-3">
	<div class="col-12 mb-3">
		<div class="row">
			<div class="d-flex justify-content-end mt-1 mt-sm-0">
				<div class="btn-toolbar" role="toolbar" aria-label="Eteria toolbars">
					<div class="btn-group me-2 mb-2" role="group">
						<a class="btn btn-secondary" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Aggiorna scheda"><span class="fas fa-sync"></span> Ricarica</a>
						<a class="btn btn-primary" href="/archy/app.php?view=notice_sender"><span class="fas fa-paper-plane me-1"></span> Invia notifica</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 pl-4 w-100">
		<h4 class="d-inline-block">Notifiche inviate ai clienti</h4>
	</div>
</div>

<table class="table mb-0 data-table fs-10 table-striped overflow-hidden" id="notice_broadcasts_table">
	<thead><?php echo $header; ?></thead>
	<tbody><?php echo $rows; ?></tbody>
	<tfoot><?php echo $header; ?></tfoot>
</table>

<script type="text/javascript">
window.onload=function(){
	$('#notice_broadcasts_table').DataTable({
		pageLength:25,
		scrollX:true,
		scrollCollapse:true,
		order: [[0,'desc']],
		language: data_table_it_IT
	});
}
</script>
