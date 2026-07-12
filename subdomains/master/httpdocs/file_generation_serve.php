<?php
$main_root = dirname(dirname(dirname(dirname(__FILE__))));
$subdomain_root = dirname(__FILE__);

require_once $main_root . '/private/_common/classes/load_classes.php';
$cloud = new Images();
$app = new App($subdomain_root);
require_once $main_root.'/private/_common/intents/'.$_GET['intent'].'.php';
if (!$app->isLogged()) {
    header("HTTP/1.1 401 Unauthorized");
    exit(0);
} else {    
    
	$app->init();
	
	$columns=explode(',',$_GET['columns']);
	$columns_labels=$columns_data=$query_columns=[];
	foreach($columns as $column):
		$column_label=$app->cols[$column]['label'];
		$columns_data[]=$app->cols[$column];
		if(empty($app->intent->joins)):
			$temp_array=explode('.',$column);
			$column=end($temp_array);
			$columns_labels[]=$column_label;
		else:
			if(in_array($column_label,$columns_labels)):
				list($table_name,$column_name)=explode('.',$column);
				$column_label=$column_label.' '.@$app->data->grouped_intents[$table_name]->singular;
			endif;
			$columns_labels[]=$column_label;
			$column=$column.' as '.preg_replace('/[^A-Za-z0-9]/','',$column_label);
		endif;
		$query_columns[]=$column;
	endforeach;
	
	$q='select '.implode(',',$query_columns).' from '.$_GET['intent'];
	foreach($app->intent->joins as $join_table=>$on):
		$q.=" left join ".$join_table." on (".$on.") ";
	endforeach;
	
	$where='';
	if(@$_GET['search']!=''):
		$globalSearch=[];
		foreach($columns as $column):
			$globalSearch[]=" ".$column." like '%".addslashes($_GET['search'])."%' ";
		endforeach;
		$where = '('.implode(' OR ', $globalSearch).')';
	endif;
	if(isset($_GET['filter'])):
		$columnSearch=[];
		foreach($_GET['filter'] as $column_name=>$value):
			$columnSearch[]=" ".$column_name." like '%".addslashes($value)."%' ";
		endforeach;
		$where = $where === '' ?
			implode(' AND ', $columnSearch) :
			$where .' AND '. implode(' AND ', $columnSearch);
	endif;		
	if($where!==''):
		$q.=' WHERE '.$where;
	endif;
	
	$q.=' order by '.$_GET['order_by'].' '.$_GET['order'];
	$db=$app->get_db();
	//error_log('QUERY: '.$q);
	$data=$db->query($q);
	$format=$_GET['format'];
	$filename=$app->intent->title;
	$sheet=@$_GET['sheet']!=''? $_GET['sheet'] : 'A4';
	$orientation=@$_GET['orientation']!=''? $_GET['orientation'] : 'P';
	$size=@$_GET['size']!=''? $_GET['size'] : 12;
	$top=@$_GET['top']!=''? $_GET['top'] : 50;
	$right=@$_GET['right']!=''? $_GET['right'] : 50;
	$bottom=@$_GET['bottom']!=''? $_GET['bottom'] : 70;
	$left=@$_GET['left']!=''? $_GET['left'] : 50;
	$pdf_styles='
		<style type="text/css">
		body{font-family:Helvetica,"dejavu sans light";font-size:'.$size.'pt; line-height:18px;} 
		table{width:100%; margin:0; padding:0;} 
		th{background:#2d4154; color:#fff; text-align:left;}
		tr:nth-child(even){background:#f3f3f3} 
		@page{margin-bottom:'.$bottom.'pt; margin-top:'.$top.'pt; margin-left:'.$left.'pt; margin-right:'.$right.'pt;}
		</style>';
	$pdf='<h2>'.$filename.'</h2><table width="100%" cellpadding="2" cellspacing="0">';
	$pdf.='<thead><tr><th>'.join('</th><th>',$columns_labels).'</th></tr></thead><tbody>';
	while($row=mysqli_fetch_row($data)):
		$pdf.='<tr>';
		foreach($row as $index=>$cell):
			if(isset($columns_data[$index]['pdf_formatter'])):
				$cell=$columns_data[$index]['pdf_formatter']($cell,'');
			elseif(isset($columns_data[$index]['formatter']) and !isset($columns_data[$index]['formatter_hide_in_pdf'])):
				if($columns_data[$index]['type']=='file'){
					if($cell!='' or $cell!=null){
						$url_thumb_img = $cloud->getImageUrl($cell,'thumbnail');
						$cell = '<img src= "' . $url_thumb_img . '" style="max-height:75px"> ';
					}	
				} else {
					$cell=$columns_data[$index]['formatter']($cell,'');
				}
			endif;
			
			/*if(isset($columns_data[$index]['formatter']) and !isset($columns_data[$index]['formatter_hide_in_excel'])):
				if($columns_data[$index]['type']=='file'){
					$cell=App::htmlImg($cell,"","max-width:100px;");
				} else {
					$cell=$columns_data[$index]['formatter']($cell,'');
				}
			endif;*/
			$pdf.='<td>'.$cell.'</td>';
		endforeach;
		$pdf.='</tr>';
	endwhile;
	$pdf.='</tbody></table>';
	$pdf_file = '<html><head>'.$pdf_styles.'</head><body>'.$pdf.'</body></html>';
	
    
    $filename_gen = 'test';  
    //$filename_ext = pathinfo($filename_gen, PATHINFO_EXTENSION);
    //if($filename_ext==''){$filename_gen=$filename_gen.'.pdf';}
	$filename_gen=$filename_gen.'.pdf';
    $result = $cloud->convertToPdf($pdf_file);
	$fp = fopen($subdomain_root.'/'.$filename_gen, 'w');  //make sure the directory markdown.md is in and the result.pdf will go to has proper permissions
	fwrite($fp, $result);
	fclose($fp);

}