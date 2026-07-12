<?php
class Report{
	
	public $app;
	
	public function __construct(){
		
		global $app;
		$this->app=$app;
		
	}
	
	public function store_pdf($report,$report_id){
		
		require_once $this->app->root.'classes/mpdf/vendor/autoload.php';
		$report='
			<html>
			<head>
			<style type="text/css">
			body{background:#FFF;color:#000;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;}
			</style>
			</head>
			<body>'.$report.'</body>
			</html>';
		
		$filename=$this->app->subdomain_root.'/assets/documents/reports/'.$report_id.'.pdf';
		if(is_file($filename)):
			unlink($filename);
		endif;
		$mpdf = new \mPDF('utf-8', 'A4-P');
		$mpdf->WriteHTML($report);
		$mpdf->Output($filename,'F');
		
	}
	
	public function store($name,$report){
		
		$db=$this->app->get_db();
		$db->query("insert into archy_reports (name,report) values ('".addslashes($name)."','".addslashes($report)."')");
		return $db->insert_id;
		
	}
	
}
?>