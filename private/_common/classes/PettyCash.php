<?php
class PettyCash{
	
	public $app, $db, $books;
	
	public
	$id,
	$date,
	$petty_cash_purpose_id,
	$petty_cash, 
	$petty_cash_type_id,
	$amount,
	$type,
	$attachment,
	$document_table, 
	$document_id,
	$taxable_amount,
	$created_at,
	$updated_at;
	
	public
	$latin_date;
	
	public function __construct(){
		
		global $app;
		$this->app=&$app;
		$this->db=$app->get_db();
		$this->books=[];
		$books=$this->db->query("select id,type from archy_petty_cash_types order by id");
		while($book=mysqli_fetch_object($books)):
			$balance=mysqli_fetch_object($this->db->query("select sum(amount) as sum_amount from archy_petty_cash_book where petty_cash_type_id=".$book->id));
			$tmp=new stdClass();
			$tmp->id=$book->id;
			$tmp->label=$book->type;
			$tmp->balance=$balance->sum_amount;
			$this->books[$book->id]=$tmp;
        endwhile;
        
        $this->cost_centres=[];
		$cost_centres=$this->db->query("select id,cost_centre from archy_cost_centres order by cost_centre");
		while($cost_centre=mysqli_fetch_object($cost_centres)):
			$balance=mysqli_fetch_object($this->db->query("select sum(amount) as sum_amount from archy_petty_cash_book where petty_cash_purpose_id=".$cost_centre->id));
			$tmp=new stdClass();
			$tmp->id=$cost_centre->id;
			$tmp->label=$cost_centre->cost_centre;
			$tmp->balance=$balance->sum_amount;
			$this->cost_centres[$cost_centre->id]=$tmp;
		endwhile;
		
		$params=func_get_args();
		$params_count=func_num_args();
		if(method_exists($this,$f='__construct'.$params_count)):
			call_user_func_array(array($this,$f),$params);
		endif;
		
	}
	
	public function __construct1($id){
		
		$columns=mysqli_fetch_object($this->db->query("
			select * from archy_petty_cash_book where id=".$id)
		);
		
		foreach($columns as $name=>$value):
			$this->$name=$value;
		endforeach;
		
		$this->latin_date=App::latin_date($this->date);
		
	}
	
	public function add($petty_cash_row) {
        $q="insert into archy_petty_cash_book (".join(',',array_keys($petty_cash_row)).") values (".join(',',array_values($petty_cash_row)).")";
        $ret=$this->db->query($q);
        show_query_error($ret,$this->db,$q);
		$new_petty_cash=new PettyCash($this->db->insert_id);
		return $new_petty_cash;
	}
	
	public function on_insert(){

		$amount=$_POST['main']['amount'];
		
		switch($_POST['main']['document_table']):
			
			case 'archy_invoices':
				
				$this->app->invoice_balance($_POST['main']['document_id']);
				/*$invoice=new Invoice($_POST['main']['document_id']);
				$paid=$invoice->paid+$amount;
				$this->app->update_document_payments($_POST['main']['document_table'],$invoice->total,$paid,$_POST['main']['document_id']);*/
				
			break;
			
			case 'archy_tax_receipts':
				
				$receipt=new Receipt($_POST['main']['document_id']);
				$paid=$receipt->paid+$amount;
				$this->app->update_document_payments($_POST['main']['document_table'],$receipt->total,$paid,$_POST['main']['document_id']);
				
			break;
			
			case 'archy_passive_receipts':
				
				$receipt=new PassiveReceipt($_POST['main']['document_id']);
				$paid=$receipt->paid+abs($amount);
				$this->app->update_document_payments($_POST['main']['document_table'],abs($receipt->total),$paid,$_POST['main']['document_id']);
				
			break;
			
			case 'archy_payables':
				
				$payable=mysqli_fetch_object($this->db->query("select paid,total from archy_payables where id=".$_POST['main']['document_id']));
				$paid=$payable->paid+abs($amount);
				$this->app->update_document_payments($_POST['main']['document_table'],$payable->total,$paid,$_POST['main']['document_id']);
				
			break;
			
			case 'archy_credit_notes':
			
				$this->app->invoice_balance($_POST['main']['document_id']);
				/*$credit_note=new CreditNote($_POST['main']['document_id']);
				$paid=$credit_note->paid+$amount;
				$this->app->update_document_payments($_POST['main']['document_table'],$credit_note->total,$paid,$_POST['main']['document_id']);*/
			
			break;
			
		endswitch;

	}
	
	public function before_delete(){
		
		if($this->document_table!='' and $this->document_id!=0):
			
			switch($this->document_table):
				
				case 'archy_invoices':
					
					$this->stored_invoice_id=$this->document_id;
					/*$invoice=new Invoice($this->document_id);
					$paid=$invoice->paid-$this->amount;
					$this->app->update_document_payments($this->document_table,$invoice->total,$paid,$this->document_id);*/
					
				break;
				
				case 'archy_tax_receipts':
					
					$receipt=new Receipt($this->document_id);
					$paid=$receipt->paid-$this->amount;
					$this->app->update_document_payments($this->document_table,$receipt->total,$paid,$this->document_id);
					
				break;
				
				case 'archy_passive_receipts':
					
					$receipt=new PassiveReceipt($this->document_id);
					$paid=$receipt->paid-abs($this->amount);
					$this->app->update_document_payments($this->document_table,abs($receipt->total),$paid,$this->document_id);
					
				break;
				
				case 'archy_payables':
					
					$payable=mysqli_fetch_object($this->db->query("select paid,total from archy_payables where id=".$this->document_id));
					$paid=$payable->paid-abs($this->amount);
					$this->app->update_document_payments($this->document_table,$payable->total,$paid,$this->document_id);
					
				break;
				
				case 'archy_credit_notes':
					
					$this->stored_invoice_id=$this->app->get_invoice_id_by_credit_note_id($this->document_id);
					/*$credit_note=new CreditNote($this->document_id);
					$paid=$credit_note->paid-$this->amount;
					$this->app->update_document_payments($this->document_table,$credit_note->total,$paid,$this->document_id);*/
					
				break;
				
			endswitch;
			
		endif;
		
	}
	
	public function after_delete(){
		
		if(isset($this->stored_invoice_id)):
			
			$this->app->invoice_balance($this->stored_invoice_id);
			
		endif;		
		
	}
	
	public function before_update(){
		
		if($_POST['main']['document_table']!=$this->document_table or $_POST['main']['document_id']!=$this->document_id):
			
			//reset old document
			switch($this->document_table):
				
				case 'archy_invoices':
					
					$this->stored_previous_invoice_id=$this->document_id;
					/*$invoice=new Invoice($this->document_id);
					//cancel old amount
					$paid=$invoice->paid-$this->amount;
					//$unpaid=$invoice->unpaid+$this->amount;
					$this->app->update_document_payments($this->document_table,$invoice->total,$paid,$this->document_id);*/
					
				break;
				
				case 'archy_tax_receipts':
					
					$receipt=new Receipt($this->document_id);
					$paid=$receipt->paid-$this->amount;
					$this->app->update_document_payments($this->document_table,$receipt->total,$paid,$this->document_id);
					
				break;
				
				case 'archy_passive_receipts':
					
					$receipt=new PassiveReceipt($this->document_id);
					$paid=$receipt->paid-abs($this->amount);
					$this->app->update_document_payments($this->document_table,abs($receipt->total),$paid,$this->document_id);
					
				break;
				
				case 'archy_payables':
					
					$payable=mysqli_fetch_object($this->db->query("select paid,total from archy_payables where id=".$this->document_id));
					$paid=$payable->paid-abs($this->amount);
					$this->app->update_document_payments($this->document_table,$payable->total,$paid,$this->document_id);
					
				break;
				
				case 'archy_credit_notes':
					$this->stored_previous_invoice_id=$this->app->get_invoice_id_by_credit_note_id($this->document_id);
					/*$credit_note=new CreditNote($this->document_id);
					$paid=$credit_note->paid-$this->amount;
					$this->app->update_document_payments($this->document_table,$credit_note->total,$paid,$this->document_id);*/
					
				break;
				
			endswitch;
			
			//update new document
			switch($_POST['main']['document_table']):
				
				case 'archy_invoices':
					
					$this->stored_invoice_id=$_POST['main']['document_id'];
					/*$invoice=new Invoice($_POST['main']['document_id']);
					$paid=$invoice->paid+$_POST['main']['amount'];
					//$unpaid=$invoice->unpaid-$_POST['main']['amount'];
					$this->app->update_document_payments($_POST['main']['document_table'],$invoice->total,$paid,$_POST['main']['document_id']);*/
					
				break;
				
				case 'archy_tax_receipts':
					
					$receipt=new Receipt($_POST['main']['document_id']);
					$paid=$receipt->paid+$_POST['main']['amount'];
					$this->app->update_document_payments($_POST['main']['document_table'],$receipt->total,$paid,$_POST['main']['document_id']);
					
				break;
				
				case 'archy_passive_receipts':
					
					$receipt=new PassiveReceipt($_POST['main']['document_id']);
					$paid=$receipt->paid+abs($_POST['main']['amount']);
					$this->app->update_document_payments($_POST['main']['document_table'],abs($receipt->total),$paid,$_POST['main']['document_id']);
					
				break;
				
				case 'archy_payables':
					
					$payable=mysqli_fetch_object($this->db->query("select paid,total from archy_payables where id=".$_POST['main']['document_id']));
					$paid=$payable->paid+abs($_POST['main']['amount']);
					$this->app->update_document_payments($_POST['main']['document_table'],$payable->total,$paid,$_POST['main']['document_id']);
					
				break;				
				
				case 'archy_credit_notes':
					
					$this->stored_invoice_id=$this->app->get_invoice_id_by_credit_note_id($_POST['main']['document_id']);
					/*$credit_note=new CreditNote($_POST['main']['document_id']);
					$paid=$credit_note->paid+$_POST['main']['amount'];
					$this->app->update_document_payments($_POST['main']['document_table'],$credit_note->total,$paid,$_POST['main']['document_id']);*/
					
				break;					
				
			endswitch;
			
		else:
			
			if($_POST['main']['amount']!=$this->amount):
				
				switch($this->document_table):
					
					case 'archy_invoices':
						
						$this->stored_invoice_id=$this->document_id;
						/*$invoice=new Invoice($this->document_id);
						//cancel old amount
						$paid=$invoice->paid-$this->amount;
						//calculate new amounts
						$paid+=$_POST['main']['amount'];
						$this->app->update_document_payments($this->document_table,$invoice->total,$paid,$this->document_id);*/
						
					break;
					
					case 'archy_tax_receipts':
						
						$receipt=new Receipt($this->document_id);
						$paid=$receipt->paid-$this->amount;
						$paid+=$_POST['main']['amount'];
						$this->app->update_document_payments($this->document_table,$receipt->total,$paid,$this->document_id);
						
					break;
					
					case 'archy_passive_receipts':
						
						$receipt=new PassiveReceipt($this->document_id);
						$paid=$receipt->paid-abs($this->amount);
						$paid+=abs($_POST['main']['amount']);
						$this->app->update_document_payments($this->document_table,abs($receipt->total),$paid,$this->document_id);
						
					break;
					
					case 'archy_payables':
						
						$payable=mysqli_fetch_object($this->db->query("select paid,total from archy_payables where id=".$this->document_id));
						$paid=$payable->paid-abs($this->amount);
						$paid+=abs($_POST['main']['amount']);
						$this->app->update_document_payments($this->document_table,$payable->total,$paid,$this->document_id);
						
					break;
					
					case 'archy_credit_notes':
						
						$this->stored_invoice_id=$this->app->get_invoice_id_by_credit_note_id($this->document_id);
						/*$credit_note=new CreditNote($this->document_id);
						$paid=$credit_note->paid-$this->amount;
						$paid+=$_POST['main']['amount'];
						$this->app->update_document_payments($this->document_table,$credit_note->total,$paid,$this->document_id);*/
						
					break;
					
				endswitch;
				
			endif;
			
		endif;
		
	}
	
	public function after_update(){
		
		if(isset($this->stored_invoice_id)):
			$this->app->invoice_balance($this->stored_invoice_id);
		endif;
		
		if(isset($this->stored_previous_invoice_id)):
			$this->app->invoice_balance($this->stored_previous_invoice_id);
		endif;
		
	}
	
	public function export(){
		error_reporting(0);
		
		global $app;
		
		ini_set('memory_limit','256M');
		header('Content-Type: application/vnd.ms-excel');
		header('Set-Cookie: fileDownload=true; path=/');
		header('Cache-Control: max-age=60, must-revalidate');
		header('Content-Disposition: attachment;filename="Prima_Nota.xlsx"');
		require_once $this->app->main_root.'/private/_common/classes/PHPExcel.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->freezePane('A2');
		$styleArray=array('font'=>array('bold'=>true,'color'=>array('rgb'=>'333333')));
		$excel_column='A';
		$columns_labels=['Data','Matrice','Categoria','Voce di Costo','Descrizione','Importo','Libro'];
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
		
		$books_id=empty($_GET['books_id'])? 0 : join(',',$_GET['books_id']);
		$cost_centres_id=empty($_GET['cost_centres_id'])? 0 : join(',',$_GET['cost_centres_id']);
        $q="
            select 
                p.date, cm.matrice, cc.category as categoria, c.cost_centre as vocedicosto, p.petty_cash, p.amount, t.type as book
            from 
                archy_petty_cash_book as p
                left join archy_petty_cash_types as t on(p.petty_cash_type_id=t.id)
                left join archy_cost_centres as c on(p.petty_cash_purpose_id=c.id)
                left join archy_cost_centres_categories as cc on(c.category_id=cc.id)
                left join archy_cost_matrice as cm on(cc.cost_matrice_id=cm.id)                  
            where p.petty_cash_type_id in(".$books_id.")
              and p.petty_cash_purpose_id in(".$cost_centres_id.")
		";
		$start=$_GET['range_start']==''? '' : App::db_date($_GET['range_start']);
		$end=$_GET['range_end']==''? '' : App::db_date($_GET['range_end']);
		switch(true):
			case $start!='' and $end!='':
				$q.=" and (p.date between '".addslashes($start)."' and '".addslashes($end)."') ";
			break;
			case $start!='' and $end=='':
				$q.=" and p.date>='".addslashes($start)."' ";
			break;
			case $start=='' and $end!='':
				$q.=" and p.date<='".addslashes($end)."' ";
			break;
		endswitch;
		
		$opening_balance=$start!=''? 
			mysqli_fetch_object($this->db->query("select sum(amount) as amount from archy_petty_cash_book where date<'".addslashes($start)."' and petty_cash_type_id in(".$books_id.") and petty_cash_purpose_id in(".$cost_centres_id.")"))->amount 
			: 0;
		$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
		$objPHPExcel->getActiveSheet()->SetCellValue('A2','Saldo iniziale')->getStyle('A2')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->SetCellValue('F2',$opening_balance)->getStyle('F2')->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
		
        $q.=" order by p.petty_cash_type_id, p.date";
        //error_log("@@@@@@@@@@@@@@@@@@@ export: ".$q);
		$petty_cash=$this->db->query($q);
		$rowCount=3;
		while($cash=mysqli_fetch_object($petty_cash)):
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,App::latin_date($cash->date));
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$cash->matrice);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$cash->categoria);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,$cash->vocedicosto);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount,$cash->petty_cash);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,$cash->amount);
			$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount,$cash->book);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
			$rowCount++;
			$opening_balance+=$cash->amount;
		endwhile;
		
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Saldo finale')->getStyle('A'.$rowCount)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,$opening_balance)->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
		
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
		$objWriter->save('TEMP');
		echo file_get_contents('TEMP');
		unlink('TEMP');
		
	}

    public function export_refa(){
		error_reporting(0);
		
		global $app;
		
		ini_set('memory_limit','256M');
		header('Content-Type: application/vnd.ms-excel');
		header('Set-Cookie: fileDownload=true; path=/');
		header('Cache-Control: max-age=60, must-revalidate');
		header('Content-Disposition: attachment;filename="REFA.xlsx"');
		require_once $this->app->main_root.'/private/_common/classes/PHPExcel.php';
		
		$objPHPExcel = new PHPExcel();
//		$objPHPExcel->getActiveSheet()->freezePane('A2');
		$styleArray=array('font'=>array('bold'=>true,'color'=>array('rgb'=>'333333')));

        // Intestazione ENTRATE
        $excel_column='A';
        $rowCount=1;
		$columns_labels=['Entrate','Att.tà Ist.','Att.tà Comm.','Totale'];
		foreach($columns_labels as $index=>$column_title):
			$objPHPExcel->getActiveSheet()->SetCellValue($excel_column.$rowCount, $column_title)->getStyle($excel_column.$rowCount)->applyFromArray($styleArray);
			$objPHPExcel->getActiveSheet()->getColumnDimension($excel_column)->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getStyle($excel_column)->getAlignment()
				->setWrapText(true)
				->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
			$excel_column++;
		endforeach;

//      $excel_column='A';for($i=0; $i<count($columns_labels)-1; $i++)$excel_column++;
//      $objPHPExcel->getActiveSheet()->setAutoFilter('A1:'.$excel_column.'1');
		
		$books_id=empty($_GET['books_id'])? 0 : join(',',$_GET['books_id']);
		$cost_centres_id=empty($_GET['cost_centres_id'])? 0 : join(',',$_GET['cost_centres_id']);
        
        /*
        Sistemato problema sul campo type:
        - ASSOCIAZIONE: se NON valorizzo il campo "istuituzionale/commerciale" => ""
        - NON ASSOCIAZIONI: vale sempre NULL
        */        
        
        $q="
           select if(p.amount>0,0,1) as in_out,
                cc.category as categoria, 
                sum(if(p.type=0 OR p.type='',p.amount,0)) as amount_I, 
                sum(if(p.type=1 OR p.type is null,p.amount,0)) as amount_C
             from 
                 archy_petty_cash_book as p
                 left join archy_petty_cash_types as t on(p.petty_cash_type_id=t.id)
                 left join archy_cost_centres as c on(p.petty_cash_purpose_id=c.id)
                 left join archy_cost_centres_categories as cc on(c.category_id=cc.id)
                 left join archy_cost_matrice as cm on(cc.cost_matrice_id=cm.id)  
            where p.petty_cash_type_id in(".$books_id.")
              and p.petty_cash_purpose_id in(".$cost_centres_id.")
		";
		$start=$_GET['range_start_refa']==''? '' : App::db_date($_GET['range_start_refa']);
		$end=$_GET['range_end_refa']==''? '' : App::db_date($_GET['range_end_refa']);
		switch(true):
			case $start!='' and $end!='':
				$q.=" and (p.date between '".addslashes($start)."' and '".addslashes($end)."') ";
			break;
			case $start!='' and $end=='':
				$q.=" and p.date>='".addslashes($start)."' ";
			break;
			case $start=='' and $end!='':
				$q.=" and p.date<='".addslashes($end)."' ";
			break;
		endswitch;
		
        /*
		$opening_balance=$start!=''? 
			mysqli_fetch_object($this->db->query("select sum(amount) as amount from archy_petty_cash_book where date<'".addslashes($start)."' and petty_cash_type_id in(".$books_id.") and petty_cash_purpose_id in(".$cost_centres_id.")"))->amount 
			: 0;
		$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
		$objPHPExcel->getActiveSheet()->SetCellValue('A2','Saldo iniziale')->getStyle('A2')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->SetCellValue('F2',$opening_balance)->getStyle('F2')->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
		*/
        $opening_balance_amount_Entrate_I=0;
        $opening_balance_amount_Entrate_C=0;
        $opening_balance_amount_Uscite_I=0;
        $opening_balance_amount_Uscite_C=0;
        $in_out=0;

        $q.=" group by if(p.amount>0,0,1), cc.category order by in_out, category ";
        //error_log("@@@@@@@@@@@@@@@@@@@ export: ".$q);
		$petty_cash=$this->db->query($q);
		while($cash=mysqli_fetch_object($petty_cash)):
            if($in_out!=$cash->in_out) {
                $in_out=$cash->in_out;

                $rowCount++;
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Tot. Entrate')->getStyle('A'.$rowCount)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$opening_balance_amount_Entrate_I)->getStyle('B'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$opening_balance_amount_Entrate_C)->getStyle('C'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,$opening_balance_amount_Entrate_I+$opening_balance_amount_Entrate_C)->getStyle('D'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);

                // Intestazione USCITE
                $excel_column='A';
                $rowCount+=2;
                $columns_labels=['Uscite','Att.tà Ist.','Att.tà Comm.','Totale'];
                foreach($columns_labels as $index=>$column_title):
                    $objPHPExcel->getActiveSheet()->SetCellValue($excel_column.$rowCount, $column_title)->getStyle($excel_column.$rowCount)->applyFromArray($styleArray);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($excel_column)->setAutoSize(true);
                    $objPHPExcel->getActiveSheet()->getStyle($excel_column)->getAlignment()
                        ->setWrapText(true)
                        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    $excel_column++;
                endforeach;
            }
            $rowCount++;
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,$cash->categoria);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$cash->amount_I);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$cash->amount_C);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,$cash->amount_I+$cash->amount_C);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
            $objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
            $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
			($cash->in_out==0)?$opening_balance_amount_Entrate_I+=$cash->amount_I:$opening_balance_amount_Uscite_I+=$cash->amount_I;
			($cash->in_out==0)?$opening_balance_amount_Entrate_C+=$cash->amount_C:$opening_balance_amount_Uscite_C+=$cash->amount_C;
		endwhile;

		//$objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);
        $rowCount++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Tot. Uscite')->getStyle('A'.$rowCount)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$opening_balance_amount_Uscite_I)->getStyle('B'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$opening_balance_amount_Uscite_C)->getStyle('C'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,$opening_balance_amount_Uscite_I+$opening_balance_amount_Uscite_C)->getStyle('D'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);

        $rowCount+=2;
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,'Saldo Ris. Gestione')->getStyle('A'.$rowCount)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$opening_balance_amount_Entrate_I+$opening_balance_amount_Uscite_I)->getStyle('B'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,$opening_balance_amount_Entrate_C+$opening_balance_amount_Uscite_C)->getStyle('C'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,$opening_balance_amount_Entrate_I+$opening_balance_amount_Entrate_C+$opening_balance_amount_Uscite_I+$opening_balance_amount_Uscite_C)->getStyle('D'.$rowCount)->getNumberFormat()->setFormatCode("€ #,##0.00;[red]-€ #,##0.00");
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
		$objWriter->save('TEMP');
		echo file_get_contents('TEMP');
		unlink('TEMP');
		
	}

}
