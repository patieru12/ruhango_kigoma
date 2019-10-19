<?php
session_start();
//var_dump($_SESSION['report']);
//var_dump($_SESSION['header']);
//var_dump($_SESSION['report_title']);
//echo "<pre>";
set_time_limit(0);
//var_dump($_SESSION);
require_once "../lib/db_function.php";
$file_name = "./facture/{$_GET['in']}.xlsx";


if(strlen($_GET['key'])){
	$sql = "SELECT 	a.DateIn AS DateIn,
					a.DocID AS DocID,
					c.ServiceCode AS ServiceCode,
					a.FamilyCategory AS FamilyCategory,
					i.Name AS Name,
					a.InsuranceCardID AS InsuranceCardID,
					i.DateofBirth AS DateofBirth,
					i.Sex AS Sex,
					i.FamilyCode AS FamilyCode,
					a.HouseManagerID AS HouseManagerID,

					d.consultationAmount AS consultationAmount,
					ROUND(e.laboratoryAmount, 1) AS laboratoryAmount,
					0 AS imageingAmount,
					g.hospitalizationAmount AS hospitalizationAmount,
					ROUND(h.actsConsummableAmount, 1) AS actsConsummableAmount,
					0 AS ambulanceCost,
					0 AS otherConsumables,
					ROUND(f.medicineAmount, 1) AS medicineAmount,

					ROUND(( 	
						COALESCE(d.consultationAmount,0) + 
						ROUND(COALESCE(e.laboratoryAmount,0), 1) +
						ROUND(COALESCE(f.medicineAmount, 0), 1) +
						COALESCE(g.hospitalizationAmount,0) + 
						ROUND(COALESCE(h.actsConsummableAmount, 0), 1)
					), 1) AS totalAll,
					j.TicketPaid AS TicketPaid,
					ROUND( ( ( 	
						COALESCE(d.consultationAmount,0) + 
						ROUND(COALESCE(e.laboratoryAmount,0), 1) +
						ROUND(COALESCE(f.medicineAmount, 0), 1) +
						COALESCE(g.hospitalizationAmount,0) + 
						ROUND(COALESCE(h.actsConsummableAmount, 0), 1)
					) - j.TicketPaid) , 1) AS totalToBePaidBack,

					a.PatientRecordID AS PatientRecordID

					FROM pa_records AS a
					INNER JOIN se_records AS b
					ON a.PatientRecordID = b.PatientRecordID
					INNER JOIN se_name AS c
					ON b.ServiceNameID = c.ServiceNameID
					INNER JOIN pa_info AS i
					ON a.PatientID = i.PatientID
					INNER JOIN mu_tm AS j
					ON a.PatientRecordID = j.PatientRecordID
					INNER JOIN (
						SELECT 	a.PatientRecordID AS PatientRecordID,
								c.Amount AS consultationAmount
								FROM pa_records AS a
								INNER JOIN co_records AS b
								ON a.PatientRecordID = b.PatientRecordID
								INNER JOIN co_price AS c
								ON b.ConsultationPriceID = c.ConsultationPriceID
								WHERE a.InsuranceNameID='{$_GET['key']}' && 
									  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
					) AS d
					ON a.PatientRecordID = d.PatientRecordID
					LEFT JOIN (
						SELECT 	a.PatientRecordID AS PatientRecordID,
								SUM(d.Amount) AS laboratoryAmount
								FROM pa_records AS a
								INNER JOIN co_records AS b
								ON a.PatientRecordID = b.PatientRecordID
								INNER JOIN la_records AS c
								ON b.ConsultationRecordID = c.ConsultationRecordID
								INNER JOIN la_price AS d
								ON c.ExamPriceID = d.ExamPriceID
								WHERE a.InsuranceNameID='{$_GET['key']}' && 
									  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
								GROUP BY a.PatientRecordID
					) AS e
					ON a.PatientRecordID = e.PatientRecordID
					LEFT JOIN (
						SELECT a.PatientRecordID AS PatientRecordID,
								SUM(a.medicineAmount) AS medicineAmount
								FROM (
									SELECT 	a.PatientRecordID AS PatientRecordID,
											SUM(c.Quantity*d.Amount) AS medicineAmount
											FROM pa_records AS a
											INNER JOIN co_records AS b
											ON a.PatientRecordID = b.PatientRecordID
											INNER JOIN md_records AS c
											ON b.ConsultationRecordID = c.ConsultationRecordID
											INNER JOIN md_price AS d
											ON c.MedecinePriceID = d.MedecinePriceID
											WHERE a.InsuranceNameID='{$_GET['key']}' && 
												  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') &&
												  a.FamilyCategory IN(1, 2) &&
												  d.MedecineNameID NOT IN(17, 18, 19, 20)
											GROUP BY a.PatientRecordID
									UNION 
										SELECT 	a.PatientRecordID AS PatientRecordID,
												SUM(c.Quantity*d.Amount) AS medicineAmount
												FROM pa_records AS a
												INNER JOIN co_records AS b
												ON a.PatientRecordID = b.PatientRecordID
												INNER JOIN md_records AS c
												ON b.ConsultationRecordID = c.ConsultationRecordID
												INNER JOIN md_price AS d
												ON c.MedecinePriceID = d.MedecinePriceID
												WHERE a.InsuranceNameID='{$_GET['key']}' && 
													  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') &&
													  a.FamilyCategory NOT IN(1, 2)
												GROUP BY a.PatientRecordID
								) AS a
								GROUP BY a.PatientRecordID
						
					) AS f
					ON a.PatientRecordID = f.PatientRecordID
					LEFT JOIN (
						SELECT 	a.PatientRecordID AS PatientRecordID,
								SUM(c.Amount* DATEDIFF(b.EndDate , b.StartDate)) AS hospitalizationAmount
								FROM pa_records AS a
								INNER JOIN ho_record AS b
								ON a.PatientRecordID = b.RecordID
								INNER JOIN ho_price AS c
								ON b.HOPriceID = c.HOPriceID
								WHERE a.InsuranceNameID='{$_GET['key']}' && 
									  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
								GROUP BY a.PatientRecordID
					) AS g
					ON a.PatientRecordID = g.PatientRecordID
					LEFT JOIN (
						SELECT 	a.PatientRecordID AS PatientRecordID,
								SUM(a.actsAmount) AS actsConsummableAmount
								FROM (
									SELECT 	a.PatientRecordID AS PatientRecordID,
											SUM(c.Amount*b.Quantity) AS actsAmount
											FROM pa_records AS a
											INNER JOIN ac_records AS b
											ON a.PatientRecordID = b.PatientRecordID
											INNER JOIN ac_price AS c
											ON b.ActPriceID = c.ActPriceID
											WHERE a.InsuranceNameID='{$_GET['key']}' && 
												  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
											GROUP BY a.PatientRecordID
									UNION
										SELECT 	a.PatientRecordID AS PatientRecordID,
												SUM(c.Amount*b.Quantity) AS actsAmount
												FROM pa_records AS a
												INNER JOIN cn_records AS b
												ON a.PatientRecordID = b.PatientRecordID
												INNER JOIN cn_price AS c
												ON b.MedecinePriceID = c.MedecinePriceID
												WHERE a.InsuranceNameID ='{$_GET['key']}' && 
													  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
												GROUP BY a.PatientRecordID
								) AS a
								GROUP BY a.PatientRecordID
					) AS h
					ON a.PatientRecordID = h.PatientRecordID
					WHERE a.InsuranceNameID='{$_GET['key']}' && 
						  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
					ORDER BY a.DateIn ASC, 
							 a.DocNumber ASC
					";
	$data = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	// var_dump($patients); die();
	if(count($data) > 0){
		require_once "../lib2/PHPExcel/IOFactory.php";
		require_once "../lib2/PHPExcel.php";

		$objPHPExcel = new PHPExcel;
		
		//instantiate the writer object
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel, "Excel2007");
		
		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
		
		$sheetIndex = 0;
		/* Here we add the report summary page */
		$objPHPExcel->setActiveSheetIndex($sheetIndex);

		/*CREATE THE WORKSHEET FOR REPORT SUMMARY*/
		// Create a new worksheet, after the default sheet
		$objPHPExcel->createSheet();

		$sheetIndex++;
		$objPHPExcel->setActiveSheetIndex($sheetIndex);
		
		$activeSheet = $objPHPExcel->getActiveSheet();
		/* 
		$activeSheet->getColumnDimension('A')->setWidth(22); */
		$activeSheet->getColumnDimension('B')->setAutoSize(true);
		$row = 1;

		for($i=0;$i<count($cbhiMonthlyBillHeader);$i++){
			$simple_row = $cbhiMonthlyBillHeader[$i];
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
					$value = str_replace("GET_MONTH_HERE", $_GET['month'], $value);
					$value = str_replace("GET_YEAR_HERE", $_GET['year'], $value);
					$activeSheet->setCellValue($first_column.$row, $value);


				}
			}
			
				$row++;
		}
		
		//now write the report title
		//echo count($data[0]);
		$last_column = "V";
		$first_column = "A";

		SpanCells($activeSheet,$first_column.$row.":".$last_column.$row,$align='center');
		$activeSheet->setCellValue($first_column.$row, $cbhiMonthlyBillReportTitle['title']);

		//start write data
		$start_row = ++$row;
		//echo $start_row;
		foreach($cbhiMonthlyBillDataHeader AS $r){
			$first_column = "A";
			foreach($r AS $dataHeader){
				$activeSheet->setCellValue(($first_column++).$row, $dataHeader);
			}
			$row++;
		}
		$data_count = 1;
		for($k=0;$k<count($data);$k++){
			//var_dump($data[$k]); echo "<br /><br /><br />";
			$first_column = "A";
			$activeSheet->setCellValue(($first_column++).$row, $data_count++);
			foreach($data[$k] as $fieldName=>$v){
				if(in_array($fieldName, ["PatientRecordID"])){
					continue;
				}
				$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
				if(in_array($fieldName, ["InsuranceCardID", "HouseManagerID"])){
					$v = $v." ";
				}
				if(in_array($fieldName, ["Name", "FamilyCode"])){
					$v = ucwords(strtolower($v));
				}
				$activeSheet->setCellValue(($first_column++).$row, $v);
				//var_dump($v); echo "<br />";
				/*if($v === "SUB TOTAL"){
					//echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":K".$row,$align='center');
					$activeSheet->setCellValue("A".$row, $v);
				}else if($v === "TOTAL"){
					//echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":F".$row,$align='center');
					$activeSheet->setCellValue("A".$row, $v);
				}*/
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
	}
}
die();
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

		$activeSheet->setTitle("Voucher Summary");

		// Create a new worksheet, after the default sheet
		$objPHPExcel->createSheet();

		$sheetIndex++;
		$objPHPExcel->setActiveSheetIndex($sheetIndex);
		
		$activeSheet = $objPHPExcel->getActiveSheet();
		/* 
		$activeSheet->getColumnDimension('A')->setWidth(22); */
		$activeSheet->getColumnDimension('B')->setAutoSize(true);
		
		// $data = $_SESSION['report'];
		//try to write report header now
		$row = 1;

		for($i=0;$i<count($cbhiMonthlyBillHeader);$i++){
			$simple_row = $cbhiMonthlyBillHeader[$i];
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
		$last_column = "V";
		$first_column = "A";
		SpanCells($activeSheet,$first_column.$row.":".$last_column.$row,$align='center');
		$activeSheet->setCellValue($first_column.$row, $cbhiMonthlyBillReportTitle['title']);
		
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
	<a href="<?= $file_name ?>" id="getFile" target="_blank">download</a>
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