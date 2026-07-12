<?php
class CashBudget{
	
	public $db, $cash_budget;
	
	public function __construct(){
		global $app;
		$this->db=$app->get_db();
		$this->cash_budget=mysqli_fetch_object($this->db->query("select sum(amount) as cash_budget from archy_cash_budget"))->cash_budget;
	}
	
	public function export(){
		global $app;
		
		ini_set('memory_limit','256M');
		header('Content-Type: application/vnd.ms-excel');
		header('Set-Cookie: fileDownload=true; path=/');
		header('Cache-Control: max-age=60, must-revalidate');
		header('Content-Disposition: attachment;filename="Bilancio_di_Cassa.xlsx"');
		require_once $this->app->main_root.'/private/_common/classes/PHPExcel.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->freezePane('A2');
		$styleArray=array('font'=>array('bold'=>true,'color'=>array('rgb'=>'333333')));
		$excel_column='A';
		$columns_labels=['Data','Causale','Importo'];
		foreach($columns_labels as $index=>$column_title):
			$objPHPExcel->getActiveSheet()->SetCellValue($excel_column.'1', $column_title)->getStyle($excel_column.'1')->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($excel_column)->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getStyle($excel_column)->getAlignment()
				->setWrapText(true)
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$excel_column++;
		endforeach;
		$excel_column='A';for($i=0; $i<count($columns_labels)-1; $i++)$excel_column++;
		$objPHPExcel->getActiveSheet()->setAutoFilter('A1:'.$excel_column.'1');
		
		$q="
			select 
				c.date, c.amount,
				p.purpose
			from 
				archy_cash_budget as c
			left join
				archy_petty_cash_purposes as p
			on(c.purpose_id=p.id)
		";
		$start=$_GET['range_start']==''? '' : App::db_date($_GET['range_start']);
		$end=$_GET['range_end']==''? '' : App::db_date($_GET['range_end']);
		switch(true):
			case $start!='' and $end!='':
				$q.=" where (c.date between '".addslashes($start)."' and '".addslashes($end)."') ";
			break;
			case $start!='' and $end=='':
				$q.=" where c.date>='".addslashes($start)."' ";
			break;
			case $start=='' and $end!='':
				$q.=" where c.date<='".addslashes($end)."' ";
			break;
		endswitch;
		
		$q.=" order by c.date";
		$cash_budget=$this->db->query($q);
		$rowCount=2;
		$balance=0;
		while($cash=mysqli_fetch_object($cash_budget)):
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,App::latin_date($cash->date));
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$cash->purpose);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$cash->amount);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
			$rowCount++;
			$balance+=$cash->amount;
		endwhile;
		
		$bordered_style = array(
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				),
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_DOUBLE
				)
			),
			'font' => array(
				'bold' => true
			)
		);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$balance)->getStyle('A'.$rowCount.':C'.$rowCount)->applyFromArray($bordered_style);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':B'.$rowCount);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Bilancio di Cassa:');
		
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
		$objWriter->save('TEMP');
		echo file_get_contents('TEMP');
		unlink('TEMP');
		
	}
	
}
?>