<?php
require '../../includes/functions.php';
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

* Easy set variables */
$db2=new mysqli(_HOST,_USER,_PASSWORD,_DB_NAME);
$db2->set_charset("utf8");


$edit_url='';
$delete_url='';

switch($_GET['intent']){
	case 'customers':
		// DB table to use
		$table = 'customers';
		// Table's primary key
		$primaryKey = 'id';
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case simple
		// indexes
		$columns = array(
			 array('db'=>'id', 'dt'=>0,
			 	'formatter'=>create_function(
					'$d,$row',
					'return "{$d} <a href=\"?intent=customer_history&user_id={$d}\" title=\"Vedi storico cliente\" style=\"float:right\"><i class=\"fa fa-calendar\"></i></a>";'
			 )),
			 array('db'=>'name', 'dt'=>1),
			 array('db'=>'surname', 'dt'=>2),
			 array('db'=>'email', 'dt'=>3,
			 	'formatter'=>create_function(
					'$d,$row',
					'return "<a href=\"mailto:{$d}\">{$d}</a>";'
			 )),
			 array('db'=>'phone', 'dt'=>4),
			 array('db'=>'country', 'dt'=>5),
			 array('db'=>'language', 'dt'=>6)
		);
		$edit_url='?intent=customers&id=';
		$delete_url='?action=save_form';
		$last_column=7;
	break;
		
	case 'accomodations':
		$table = 'accomodations';
		$primaryKey = 'id';
		$columns = array(
			 array('db'=>'id', 'dt'=>0),
			 array('db'=>'name', 'dt'=>1),
			 array('db'=>'region', 'dt'=>2),
			 array('db'=>'province', 'dt'=>3),
			 array('db'=>'city', 'dt'=>4),
			 array('db'=>'zone_id', 'dt'=>5),
			 array('db'=>'type', 'dt'=>6),
			 array('db'=>'status', 'dt'=>7),
			 array('db'=>'class', 'dt'=>8),
		);
		
		$edit_url='?intent=accomodations&id=';
		$delete_url='?action=save_form';
		$last_column=9;
	break;
	
	case 'agencies':
		$table = 'agencies';
		$primaryKey = 'id';
		$columns = array(
			 array('db'=>'id', 'dt'=>0),
			 array('db'=>'company_name', 'dt'=>1),
			 array('db'=>'representative', 'dt'=>2),
			 array('db'=>'email', 'dt'=>3,
			 	'formatter'=>create_function(
					'$d,$row',
					'return "<a href=\"mailto:{$d}\">{$d}</a>";'
				)),
			 array('db'=>'phone', 'dt'=>4),
		);
		
		$edit_url='?intent=agencies&id=';
		$delete_url='?action=save_form';
		$last_column=5;
	break;
	
	case 'generic':
		$table = $_GET['table'];
		$primaryKey = 'id';
		$columns = array();
		$result=$db2->query("show columns from ".$table);
		$index=0;
		while($row=mysqli_fetch_object($result)){
			$array=array('db'=>$row->Field, 'dt'=>$index);
			array_push($columns,$array);
			$index++;
		}
		$edit_url='?intent='.$table.'&id=';
		$delete_url='?action=save_form';
		$last_column=$index;
	break;
}
//additional columns
$columns[]=array(
	'db'=>'id',
	'dt'=>$last_column,
	/* Anonymous functions are available since PHP 5.3+ 
	formatter'=>function($d,$row){
		return "<a href=\"".$d."\">action</a>";
	}*/
	'formatter'=>create_function(
		'$d,$row',
		'global $edit_url,$delete_url,$table;
		return 
		"<a href=\"{$edit_url}{$d}\" title=\"Modifica\" class=\"btn btn-primary btn-sm\" data-toggle=\"tooltip\"><i class=\"fa fa-pencil-square-o\"></i></a>
		<form method=\"post\" action=\"{$delete_url}\" class=\"confirm_del\">
			<button type=\"submit\" title=\"Elimina\" class=\"btn btn-danger btn-sm\" data-toggle=\"tooltip\"><i class=\"fa fa-trash-o\"></i></button>
			<input type=\"hidden\" name=\"query\" value=\"delete from\">
			<input type=\"hidden\" name=\"table\" value=\"{$table}\">
			<input type=\"hidden\" name=\"id\" value=\"{$d}\">
			<input type=\"hidden\" name=\"success_message\" value=\"Il record è stato cancellato\">
			<input type=\"hidden\" name=\"error_message\" value=\"Errore imprevisto nella cancellazione del record\">
			<input type=\"hidden\" name=\"redirect_to\" value=\"?intent={$table}\">
		</form>";')
);
// SQL server connection information
$sql_details = array(
    'user' => _USER,
    'pass' => _PASSWORD,
    'db'   => _DB_NAME,
    'host' => _HOST
);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
*/
require('ssp.class.php');

echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);