<?php
$primaryKey = 'id';
switch($_GET['intent']){
	case 'orders':
		$table="lr_orders";
		$columns = array(
			 array('db'=>'`order`.`code`', 'dt'=>0, 'field'=>'code','formatter'=>function($d,$row){return '<a href="/account/seller_orders/'.$d.'" class="main_color" data-toggle="tooltip" title="Dettagli ordine">'.$d.'</a>';}),
			 array('db'=>'`order`.`creation_date`', 'dt'=>1, 'field'=>'creation_date','formatter'=>function($d,$row){return latin_date_time($d);}),
			 array('db'=>'`order`.`status`', 'dt'=>2, 'field'=>'status','formatter'=>function($d,$row){
			 	global $order_statuses; 
				return '<span class="status '.$order_statuses[$d]['css_class'].'"></span> '.$order_statuses[$d]['label'];}
			),
			 array('db'=>'`user`.`first_name`', 'dt'=>3, 'field'=>'first_name'),
			 array('db'=>'`user`.`last_name`', 'dt'=>4, 'field'=>'last_name'),
			 array('db'=>'`order`.`payment_method`', 'dt'=>5, 'field'=>'payment_method','formatter'=>function($d,$row){global $payment_names; return $payment_names[$d];})
		);
		$last_column=5;
		$id_prefix='order';
		$joinQuery = ", (select count(i.id) from lr_order_items as i where i.order_id=order.id and i.seller_id=".$_SESSION['seller_id'].") as found FROM `lr_orders` AS `order` JOIN `users` AS `user` ON (`order`.`buyer_id`= `user`.`id`)  ";
		$extraWhere = '';
		$edit_url='?intent=sales&id=';
		$delete_url='?action=save_form';
	break;
	case 'accomodations':
		$table = 'accomodations';
		$columns = array(
			 array('db'=>'`acc`.`id`', 'dt'=>0, 'field'=>'id'),
			 array('db'=>'`acc`.`name`', 'dt'=>1, 'field'=>'name'),
			 array('db'=>'`acc`.`region`', 'dt'=>2, 'field'=>'region'),
			 array('db'=>'`acc`.`province`', 'dt'=>3, 'field'=>'province'),
			 array('db'=>'`acc`.`city`', 'dt'=>4, 'field'=>'city'),
			 array('db'=>'`zone`.`code`', 'dt'=>5, 'field'=>'code'),
			 array('db'=>'`acc`.`type`', 'dt'=>6, 'field'=>'type'),
			 array('db'=>'`acc`.`status`', 'dt'=>7, 'field'=>'status'),
			 array('db'=>'`acc`.`class`', 'dt'=>8, 'field'=>'class'),
			 array('db'=>'`ag`.`company_name`', 'dt'=>9, 'field'=>'company_name')
		);
		$edit_url='?intent=accomodations&id=';
		$delete_url='?action=save_form';
		$last_column=10;
		$id_prefix='acc';
		
		$joinQuery = " FROM `accomodations` AS `acc` LEFT JOIN `zones` AS `zone` ON (`acc`.`zone_id` = `zone`.`id`) LEFT JOIN `agencies` as `ag` ON (`acc`.`agency_id`=`ag`.`id`) ";
		$extraWhere = "`acc`.`deleted`=0";//"`u`.`salary` >= 90000";        
	break;
	
	case 'quotations':
		$table = 'quotations';
		$columns = array(
			 array('db'=>'`quot`.`id`', 'dt'=>0, 'field'=>'id'),
			 array('db'=>'`cust`.`id`', 'dt'=>1, 'field'=>'id'),
			 array('db'=>'`cust`.`name`', 'dt'=>2, 'field'=>'name','formatter'=>create_function('$d,$row','return "<a href=\"index.php?intent=customers&id=$row[1]\">$d</a>";')),
			 array('db'=>'`cust`.`surname`', 'dt'=>3, 'field'=>'surname','formatter'=>create_function('$d,$row','return "<a href=\"index.php?intent=customers&id=$row[1]\">$d</a>";')),
			 array('db'=>'`quot`.`from_date`', 'dt'=>4, 'field'=>'from_date','formatter'=>create_function('$d,$row','return substr($d,8)."/".substr($d,5,2)."/".substr($d,0,4);')),
			 array('db'=>'`quot`.`to_date`', 'dt'=>5, 'field'=>'to_date','formatter'=>create_function('$d,$row','return substr($d,8)."/".substr($d,5,2)."/".substr($d,0,4);')),
			 array('db'=>'`quot`.`nights`', 'dt'=>6, 'field'=>'nights'),
			 array('db'=>'`quot`.`amount`', 'dt'=>7, 'field'=>'amount','formatter'=>create_function('$d,$row','return "€ ".number_format($d,2,",",".");')),
			 array('db'=>'`quot`.`sold`', 'dt'=>8, 'field'=>'sold','formatter'=>create_function(
			 	'$d,$row',
				'
				$id=$row[id];
				if($d==0){//not sold
					$str="<a href=\"index.php?action=set_as_sold&id=$id\" title=\"Imposta come venduto\" class=\"btn btn-warning btn-sm\" data-toggle=\"tooltip\"><i class=\"fa fa-check-square-o\"></i></a>";
				}else{
					$str="<button type=\"button\" title=\"Venduto\" class=\"btn btn-success btn-sm\" data-toggle=\"tooltip\"><i class=\"fa fa-check-square-o\"></i></button>";
				}
				return $str;'
				)
			)
		);
		$edit_url='?intent=quotation&id=';
		$delete_url='?action=save_form';
		$last_column=9;
		$id_prefix='quot';
		
		$joinQuery = " FROM `quotations` AS `quot` LEFT JOIN `customers` AS `cust` ON (`quot`.`customer_id` = `cust`.`id`)";
		$extraWhere = '';        
	break;
	
	case 'sales':
		$table = 'sales';
		$columns = array(
			 array('db'=>'`sale`.`id`', 'dt'=>0, 'field'=>'id'),
			 array('db'=>'`sale`.`quotation_id`', 'dt'=>1, 'field'=>'quotation_id','formatter'=>create_function('$d,$row','return $d." - <a href=\"index.php?intent=quotation&id=$d\">Vedi preventivo</a>";')),
			 array('db'=>'`cust`.`id`', 'dt'=>2, 'field'=>'id'),
			 array('db'=>'`cust`.`name`', 'dt'=>3, 'field'=>'name','formatter'=>create_function('$d,$row','return "<a href=\"index.php?intent=customers&id=$row[2]\">$d</a>";')),
			 array('db'=>'`cust`.`surname`', 'dt'=>4, 'field'=>'surname','formatter'=>create_function('$d,$row','return "<a href=\"index.php?intent=customers&id=$row[2]\">$d</a>";')),
			 array('db'=>'`quot`.`from_date`', 'dt'=>5, 'field'=>'from_date','formatter'=>create_function('$d,$row','return substr($d,8)."/".substr($d,5,2)."/".substr($d,0,4);')),
			 array('db'=>'`quot`.`to_date`', 'dt'=>6, 'field'=>'to_date','formatter'=>create_function('$d,$row','return substr($d,8)."/".substr($d,5,2)."/".substr($d,0,4);')),
			 array('db'=>'`quot`.`amount`', 'dt'=>7, 'field'=>'amount','formatter'=>create_function('$d,$row','return "€ ".number_format($d,2,",",".");'))
		);
		$edit_url='?intent=sales&id=';
		$delete_url='?action=save_form';
		$last_column=8;
		$id_prefix='sale';
		
		$joinQuery = " FROM `sales` AS `sale` JOIN `quotations` AS `quot` JOIN `customers` AS `cust` ON (`sale`.`quotation_id`= `quot`.`id` AND `quot`.`customer_id` = `cust`.`id`)";
		$extraWhere = '';        
	break;
	
	case 'contacts':
		$table = 'contacts';
		$columns = array(
			 array('db'=>'`contact`.`id`', 'dt'=>0, 'field'=>'id'),
			 array('db'=>'`contact`.`date`', 'dt'=>1, 'field'=>'date','formatter'=>create_function('$d,$row','return substr($d,8)."/".substr($d,5,2)."/".substr($d,0,4);')),
			 array('db'=>'`contact`.`customer_id`', 'dt'=>2, 'field'=>'customer_id'),
			 array('db'=>'`cust`.`surname`', 'dt'=>3, 'field'=>'surname','formatter'=>create_function('$d,$row','return "<a href=\"index.php?intent=customers&id=$row[2]\">$d</a>";')),
			 array('db'=>'`cust`.`email`', 'dt'=>4, 'field'=>'email','formatter'=>create_function('$d,$row','return "<a href=\"index.php?intent=customers&id=$row[2]\">$d</a>";')),
			 array('db'=>'`contact`.`holiday_region`', 'dt'=>5, 'field'=>'holiday_region'),
			 array('db'=>'`contact`.`request`', 'dt'=>6, 'field'=>'request'),
			 array('db'=>'`contact`.`status`', 'dt'=>7, 'field'=>'status','formatter'=>create_function('$d,$row','switch($d){case "1":$color="red";$msg="Ricevuto Contatto";break;case "2":$color="orange";$msg="Inviato Preventivo";break;case "3":$color="green";$msg="Conclusa vendita";break;} return "<i class=\"fa fa-circle {$color}\" title=\"{$msg}\" data-toggle=\"tooltip\"></i>";'))
		);
		$edit_url='?intent=contacts&id=';
		$delete_url='?action=save_form';
		$last_column=8;
		$id_prefix='contact';
		
		$joinQuery = " FROM `contacts` AS `contact` LEFT JOIN `customers` AS `cust` ON (`contact`.`customer_id` = `cust`.`id`)";
		$extraWhere = '';        
	break;
	
}
//$columns[]=array(
//	'db'=>'`'.$id_prefix.'`.`code`',
//	'dt'=>$last_column,
//	'field'=>'id',
//	/* Anonymous functions are available since PHP 5.3+ 
//	formatter'=>function($d,$row){
//		return "<a href=\"".$d."\">action</a>";
//	}*/
//	'formatter'=>create_function(
//		'$d,$row',
//		'global $edit_url,$delete_url,$table;
//		
//		$accomodations_form="
//			<form method=\"post\" action=\"{$delete_url}\" class=\"confirm_del\">
//				<button type=\"submit\" title=\"Elimina\" class=\"btn btn-danger btn-sm\" data-toggle=\"tooltip\"><i class=\"fa fa-trash-o\"></i></button>
//				<input type=\"hidden\" name=\"query\" value=\"update\">
//				<input type=\"hidden\" name=\"table\" value=\"{$table}\">
//				<input type=\"hidden\" name=\"id\" value=\"{$d}\">
//				<input type=\"hidden\" name=\"deleted\" value=\"1\">
//				<input type=\"hidden\" name=\"success_message\" value=\"Il record è stato cancellato\">
//				<input type=\"hidden\" name=\"error_message\" value=\"Errore imprevisto nella cancellazione del record\">
//				<input type=\"hidden\" name=\"redirect_to\" value=\"?intent={$table}\">
//			</form>";
//			
//			$default_form="
//				<form method=\"post\" action=\"{$delete_url}\" class=\"confirm_del\">
//					<button type=\"submit\" title=\"Elimina\" class=\"btn btn-danger btn-sm\" data-toggle=\"tooltip\"><i class=\"fa fa-trash-o\"></i></button>
//					<input type=\"hidden\" name=\"query\" value=\"delete from\">
//					<input type=\"hidden\" name=\"table\" value=\"{$table}\">
//					<input type=\"hidden\" name=\"id\" value=\"{$d}\">
//					<input type=\"hidden\" name=\"success_message\" value=\"Il record è stato cancellato\">
//					<input type=\"hidden\" name=\"error_message\" value=\"Errore imprevisto nella cancellazione del record\">
//					<input type=\"hidden\" name=\"redirect_to\" value=\"?intent={$table}\">
//				</form>";
//		
//		if($table=="accomodations"){
//			$form=$accomodations_form;
//		}else{
//			if($table=="quotations" and $row[sold]==1){
//				$form="";
//			}else{
//				$form=$default_form;
//			}
//		}
//		return 
//		"<a href=\"{$edit_url}{$d}\" title=\"Modifica\" class=\"btn btn-primary btn-sm\" data-toggle=\"tooltip\"><i class=\"fa fa-pencil-square-o\"></i></a>".$form;')
//);

// DB table to use


// Table's primary key


// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
//$columns = array(
//	array( 'db' => '`quot`.`id`', 'dt' => 0, 'field' => 'id' ),
//	array( 'db' => '`quot`.`date`',   'dt' => 1, 'field' => 'date',
//		'formatter'=>create_function(
//			'$d,$row',
//			'return substr($d,8)."/".substr($d,5,2)."/".substr($d,0,4);'
//	)),
//	array( 'db' => '`user`.`name`',  'dt' => 2, 'field' => 'name' ),
//	array( 'db' => '`user`.`surname`',  'dt' => 3, 'field' => 'surname' ),
//	array( 'db' => '`quot`.`type`',     'dt' => 4, 'field' => 'type'),
//	array( 'db' => '`quot`.`status`',     'dt' => 5, 'field' => 'status' ),
//	array( 'db' => '`quot`.`accomodations_id`',     'dt' => 6, 'field' => 'accomodations_id' )
//);

//additional columns


// SQL server connection information
$sql_details = array(
    'user' => 'linus_linus',
    'pass' => 'bF@O2;x*wp)P',
    'db'   => 'linus_records_2015',
    'host' => 'localhost'
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );


echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);