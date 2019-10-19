<?php
session_start();
//var_dump($_SESSION);
set_time_limit(0);
require_once "../lib/db_function.php";
$prefix = returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con);
if(!in_array($prefix , array("rcp","com")) ){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(!@$_GET['key']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$file_name = "./facture/{$_GET['key']}.xlsx";
$select = "";
$post = "";

$posts = explode("_", $_GET['post']);
//var_dump($posts);
$count = count($post);
$current = 1;
$sys = "("; $sys_s = 0;
$ok = false;
foreach($posts as $pst){
	$ps = returnSingleField($sql="SELECT CenterName FROM sy_center WHERE CenterID='{$pst}'",$field="CenterName",$data=true, $con);
	//var_dump($ps);
	if($ps != null){
		$ok = true;
		if($post && $current++ == $count)
			$post .= " And ";
		else
			$post .= " ";
		$post .= $ps;
		if($sys_s++ > 0)
			$sys .= " || ";
		$sys .= "sy_center.CenterName = '{$ps}'";
	}
}
$sys .= ")";
if(!$ok){
	echo "<span class=error>No Post Selected</span>";
	return;
}
//var_dump($_GET);
//echo $sys;
if(strlen($_GET['key'])){
	$sql = "SELECT 	COALESCE(a.cbhiMonthlyID,'') AS monthlyID,
					a.DocID AS DocID,
					a.DateIn AS DateIn,
					c.ServiceCode AS ServiceCode,
					a.FamilyCategory AS FamilyCategory,
					i.Name AS Name,
					COALESCE(a.InsuranceCardID, a.applicationNumber, '') AS InsuranceCardID,
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
												  d.MedecineNameID NOT IN(17, 18, 19, 20, 51, 83, 98)
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
	// echo $sql; die();
	$patients = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	//select all possible information on the comming id
	
if($patients){
	
	?>
	<b class=visibl>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
	</style>
	<span class=styling></span>
	<div style='max-height:85%; padding-top:5px; border:0px solid #000; overflow:auto;'>
	<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
		<tr><th>N<sup>o</sup></th><th>Number</th><th>Date</th><th>Service</th><th>Cat.</th><th>Name</th><th>ID Number</th><th>Age</th><th>Sex</th><th>House Holder</th><th>ID Number of Household</th><th>Cons Cost</th><th>Lab</th><th>Imaging</th><th>Hosp.</th><th>Proc & Mat.</th><th>Ambul.</th><th>Consum.</th><th>Drugs.</th><th>Total</th><th>Co-payment</th><th>Amount after verif.</th></tr>
		<tr><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>200 RWF / 10%</th><th></th></tr>
		<?php
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
		$activeSheet = $objPHPExcel->getActiveSheet();
		/*CREATE THE WORKSHEET FOR REPORT SUMMARY*/
		$activeSheet->setTitle("Summary");
		
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

		$total = array();
		$medicinesMainTotal = 0;
		$othersMainTotal 	= 0;
		for($i=0;$i<count($patients);$i++){
			$r = $patients[$i];
			/*HERE WRITE EVERY THING IN EXCEL ROW*/
			$first_column = "A";
			// $activeSheet->setCellValue(($first_column++).$row, $data_count++);
			foreach($r as $fieldName=>$v){
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
			}
			$row++;
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'>
				<td>{$r["monthlyID"]}</td>
				<td>{$r["DocID"]}</td>
				<td>{$r["DateIn"]}</td>
				<td>{$r["ServiceCode"]}</td>
				<td>{$r['FamilyCategory']}</td>
				<td>{$patients[$i]["Name"]}</td>
				<td>{$r["InsuranceCardID"]}</td>
				<td>".($patients[$i]["DateofBirth"] == "0000-00-00"?"":$patients[$i]["DateofBirth"])."</td>
				<td>{$patients[$i]["Sex"]}</td>
				<td>{$patients[$i]["FamilyCode"]}</td>
				<td>".$r['HouseManagerID']."</td>";

				$columsCounter = 0;

				echo "<td>{$r['consultationAmount']}</td>";
				$othersMainTotal += $r['consultationAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['consultationAmount']):$r['consultationAmount']);
				$columsCounter++;
				echo "<td>{$r['laboratoryAmount']}</td>";
				$othersMainTotal += $r['laboratoryAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['laboratoryAmount']):$r['laboratoryAmount']);
				$columsCounter++;
				echo "<td>&nbsp;</td>";
				$total[$columsCounter] = "";
				$columsCounter++;
				echo "<td>{$r['hospitalizationAmount']}</td>";
				$othersMainTotal += $r['hospitalizationAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['hospitalizationAmount']):$r['hospitalizationAmount']);
				$columsCounter++;
				echo "<td>{$r['actsConsummableAmount']}</td>";
				$othersMainTotal += $r['actsConsummableAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['actsConsummableAmount']):$r['actsConsummableAmount']);
				$columsCounter++;
				echo "<td></td>";
				$total[$columsCounter] = "";
				$columsCounter++;
				echo "<td></td>";
				$total[$columsCounter] = "";
				$columsCounter++;
				echo "<td>{$r['medicineAmount']}</td>";
				$medicinesMainTotal += $r['medicineAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['medicineAmount']):$r['medicineAmount']);
				$columsCounter++;
				echo "<td>{$r['totalAll']}</td>";
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['totalAll']):$r['totalAll']);
				$columsCounter++;
				echo "<td>{$r['TicketPaid']}</td>";
				$othersMainTotal -=  $r['TicketPaid'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['TicketPaid']):$r['TicketPaid']);
				$columsCounter++;
				echo "<td>{$r['totalToBePaidBack']}</td>";
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['totalToBePaidBack']):$r['totalToBePaidBack']);
				$columsCounter++;
			echo "</tr>";
			// $data[] = $ddd;
		}

		
		?>
		<tr><th colspan=11>Total</th>
			<?php
			$first_column = "L";
			SpanCells($activeSheet,"A".$row.":K".$row,$align='center');
			$activeSheet->setCellValue("A".$row, "TOTAL"); 
			foreach($total AS $t){
				echo "<th>{$t}</th>";
				$activeSheet->setCellValue(($first_column++).$row, $t);
			}
			$row++;
			?>
		</tr>
		<?php
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

		$reportSummaryData = array();
		$reportSummaryData[] = array("HEALTH FACILITY:", "", "", strtoupper($organisation));
		$reportSummaryData[] = array("CODE H F:", "", "", strtoupper($organisation_code_minisante));
		$reportSummaryData[] = array("RSSB/CBHI INVOICE NUMBER:", "", "", strtoupper($organisation_code_minisante));
		$reportSummaryData[] = array("TIN:", "", "", strtoupper($organisation_tin));
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("","", "   T O T A L       B I L L");
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("Rwanda Social Board (RSSB) has to pay ");
		$reportSummaryData[] = array("To                 ".strtoupper($organisation)."           The sum of") ;
		$mainTotal = $total[(count($total) - 1)];
		$mainTotal = round($mainTotal, 0);

		$reportSummaryData[] = array("(In Figure)","", number_format($mainTotal)." Frw" ) ;
		$reportSummaryData[] = array("In word","", getEnglishNumber($mainTotal)." Rwandan Francs" ) ;
		$reportSummaryData[] = array("for all medical care given its affiliate" ) ;
		$reportSummaryData[] = array("This amount includes" ) ;
		$medicinesMainTotal  = round($medicinesMainTotal, 0);

		$reportSummaryData[] = array("(In Figure)","", number_format($medicinesMainTotal)." Frw" ) ;
		$reportSummaryData[] = array("In word","", getEnglishNumber($medicinesMainTotal)." Rwandan Francs" ) ;
		$reportSummaryData[] = array("for all medicines" ) ;
		$othersMainTotal 	 = round($othersMainTotal, 0);
		
		$reportSummaryData[] = array("(In Figure)","", number_format($othersMainTotal)." Frw" ) ;
		$reportSummaryData[] = array("In word","", getEnglishNumber($othersMainTotal)." Rwandan Francs" ) ;
		$reportSummaryData[] = array("for all medical procedures, investigation and other services" ) ;
		$reportSummaryData[] = array( $month[((int)$_GET['month'])].", ".$_GET['year'] ) ;
		$reportSummaryData[] = array("This amount will put into account number ".strtoupper($organisation_account_number) ) ;
		$reportSummaryData[] = array("At ".strtoupper($organisation_bank_name) ) ;
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("","", "Done at ".strtoupper($client).", Date ".date("Y F d") );
		$reportSummaryData[] = array("","", strtoupper($organisation_represantative));
		$reportSummaryData[] = array("","", 'Titulaire of '.strtoupper($organisation));
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("(Names signature for responsable & stamp of ..)");

		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("Amount approved after reconciliation");
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("(in figures)", "" , "");
		$reportSummaryData[] = array("(in words)", "" , "... ... ... ... ... ... ... ... ... ... ...");
		$reportSummaryData[] = array("", "" , "... ... ... ... ... ... ... ... ... ... ...");
		$reportSummaryData[] = array("");
		$reportSummaryData[] = array("Date & Signature");
		$reportSummaryData[] = array("Names");
		$reportSummaryData[] = array("Post");

		$sheetIndex = 0;
		/* Here we add the report summary page */
		$objPHPExcel->setActiveSheetIndex($sheetIndex);
		$activeSheet = $objPHPExcel->getActiveSheet();
		
		$row = 1;
		for($k=0;$k<count($reportSummaryData);$k++){
			//var_dump($data[$k]); echo "<br /><br /><br />";
			$first_column = "A";
			$activeSheet->getColumnDimension('A')->setWidth(28);
			foreach($reportSummaryData[$k] as $v){
				// $activeSheet->getColumnDimension($first_column)->setAutoSize(true);
				$activeSheet->setCellValue(($first_column++).$row, $v);			}
			$row++;
		}

		//write the file with the desired filename
		$objWriter->save($file_name);
		?>
	</table>
	</div></b>
	<?php
	if(count($data)>0){
		?>
		<style>
			.img_links{
				height:40px; 
				width:40px; 
				cursor:pointer;
			}
			.img_links:hover{
				/* height:37px;  */
				border-bottom:3px solid red;
			}
		</style>
		<a href='<?= (in_array($prefix, array("com"))?"../../report/":"./") ?><?= $file_name ?>' target="_blank" class='btn btn-success'> <i class="fa fa-files-o"></i> View in EXCEL </a>
		<?php
	}
} else{
	echo "<span class=error-text>No Patient in the selected month {$_GET['month']}/{$_GET['year']} at selected station {$post}</span>";
}
}
?>
<div id=upload_out></div>