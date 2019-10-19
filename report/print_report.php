<?php
session_start();
//var_dump($_SESSION['report']);
//var_dump($_SESSION['header']);
//var_dump($_SESSION['report_title']);
//echo "<pre>";
set_time_limit(0);
//var_dump($_SESSION);
require_once "../lib/db_function.php";
$prefix = returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con);

$file_name = "./facture/{$_GET['in']}.xlsx";
if($_GET['format'] == "excel" && @$_SESSION['report']){
	//now generate the excel file for the report
	//echo $file_name;
		//include phpExcel library in the file
		require_once "../lib2/PHPExcel/IOFactory.php";
		require_once "../lib2/PHPExcel.php";
		//instantiate the PHPExcel object
		$objPHPExcel = new PHPExcel;
		
		//instantiate the writer object
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel, "Excel2007");
		
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		
		$sheetIndex = 0;
		/* Here we add the report summary page */
		$objPHPExcel->setActiveSheetIndex($sheetIndex);

		$activeSheet = $objPHPExcel->getActiveSheet();
		$data = @$_SESSION['report_summary'];
		$row = 1;
		for($k=0;$k<count($data);$k++){
			//var_dump($data[$k]); echo "<br /><br /><br />";
			$first_column = "A";
			$activeSheet->getColumnDimension('A')->setWidth(28);
			foreach($data[$k] as $v){
				// $activeSheet->getColumnDimension($first_column)->setAutoSize(true);
				$activeSheet->setCellValue(($first_column++).$row, $v);
				//var_dump($v); echo "<br />";
				/*if($v === "SUB TOTAL"){
					//echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":J".$row,$align='center');
					$activeSheet->setCellValue("A".$row, $v);
				}else if($v === "TOTAL"){
					//echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":F".$row,$align='center');
					$activeSheet->setCellValue("A".$row, $v);
				}*/
			}
			$row++;
		}

		$activeSheet->setTitle("Voucher Summary");

		// Create a new worksheet, after the default sheet
		$objPHPExcel->createSheet();

		$sheetIndex++;
		$objPHPExcel->setActiveSheetIndex($sheetIndex);
		
		$activeSheet = $objPHPExcel->getActiveSheet();
		/* 
		$activeSheet->getColumnDimension('A')->setWidth(22); */
		$activeSheet->getColumnDimension('B')->setAutoSize(true);
		
		$data = $_SESSION['report'];
		//try to write report header now
		$row = 1;
		for($i=0;$i<count($_SESSION['header']);$i++){
			$simple_row = $_SESSION['header'][$i];
			//var_dump($simple_row); echo "<br />";
			$first_column = 'B';
			SpanCells($activeSheet,"B".$row.":D".$row,$align='left');
			SpanCells($activeSheet,"E".$row.":G".$row,$align='left');
			//set the value for B1
			$count = 0;
			foreach($simple_row as $title_=>$value){
				if($count++ == 0){
					$activeSheet->setCellValue($first_column.$row, $title_);
					$first_column = 'E';
					if(preg_match("/^CODE/",$title_))
						$value = $value." ";
					//var_dump(is_integer($value)); echo $value;
					$activeSheet->setCellValue($first_column.$row, $value);
					
				} else{
					$first_column = 'K';
					$activeSheet->setCellValue($first_column.$row, $title_);
					$first_column = 'L';
					SpanCells($activeSheet,"L".$row.":N".$row,$align='left');
					$activeSheet->setCellValue($first_column.$row, $value);
				}
			}
			
				$row++;
		}
		
		//now write the report title
		//echo count($data[0]);
		$last_column = count($data[0]) == 19?"S":(count($data[0]) == 18?"R":(count($data[0]) == 16?"P":(count($data[0]) == 22?"V":"A")));
		$first_column = "A";
		SpanCells($activeSheet,"A".$row.":".$last_column.$row,$align='center');
		$activeSheet->setCellValue($first_column.$row, $_SESSION['report_title']['title']);
		
		
		//start write data
		$start_row = ++$row;
		//echo $start_row;
		$data_count = 1;
		for($k=0;$k<count($data);$k++){
			//var_dump($data[$k]); echo "<br /><br /><br />";
			$first_column = "A";
			foreach($data[$k] as $v){
				$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
				$activeSheet->setCellValue(($first_column++).$row, $v);
				//var_dump($v); echo "<br />";
				if($v === "SUB TOTAL"){
					//echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":K".$row,$align='center');
					$activeSheet->setCellValue("A".$row, $v);
				}else if($v === "TOTAL"){
					//echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":F".$row,$align='center');
					$activeSheet->setCellValue("A".$row, $v);
				}
			}
			$row++;
		}
		
		
		
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF000000'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle("A".($start_row).":".$last_column.(--$row))->applyFromArray($styleArray);
		
		SpanCells($activeSheet,"B".($row + 3).":F".($row + 3),$align='left');
		$activeSheet->setCellValue("B".($row + 3), "Prepared By: ".returnSingleField("SELECT Name FROM sy_users WHERE UserID='{$_SESSION['user']['UserID']}'","Name",$data=true, $con));
		
		$activeSheet->setTitle(date("Y-m-d",time()));

		// Create a new worksheet, after the default sheet
		$objPHPExcel->createSheet();
			
		//write the file with the desired filename
		$objWriter->save($file_name);
		unset($_SESSION['report']);
		unset($_SESSION['report_summary']);
		unset($_SESSION['header(string)']);
}
if(file_exists($file_name)){
	// Here Create the link to dowload the file.
	echo "<span class='error-text'>File Found Please Wait.</span>";
	?>
	<a href="<?= (in_array($prefix, array("com"))?"../../report/":"./") ?><?= $file_name ?>" id="getFile" target="_blank">download</a>
	<script type="text/javascript">
		setTimeout(function(){
			$("#getFile")[0].click();
		},500);
	</script>
	<?php
} else{
	// Print the error cause no file.
	echo "<span class='error-text'>No File created</span>";
}
?>