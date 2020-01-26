<?php
session_start();
//var_dump($_SESSION);
set_time_limit(0);
require_once "../lib/db_function.php";
$prefix = returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con);
if(!in_array($prefix , ["rcp","com", "ttl"]) ){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(!@$_GET['key']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$file_name = "./reports/{$_GET['key']}.xlsx";
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

					SUM(d.consultationAmount) AS consultationAmount,
					ROUND(SUM(e.laboratoryAmount), 1) AS laboratoryAmount,
					0 AS imageingAmount,
					SUM(g.hospitalizationAmount) AS hospitalizationAmount,
					ROUND(SUM(h.actsAmount), 1) AS actsConsummableAmount,
					0 AS ambulanceAmount,
					ROUND(SUM(l.consummableAmount), 1) AS otherConsumablesAmount,
					ROUND(SUM(f.buyingAmount), 1) AS buyingAmount,
					ROUND(SUM(f.medicineAmount), 1) AS medicineAmount,
					ROUND(SUM(f.medicineAmount) - SUM(f.buyingAmount), 1) AS incomesAmount,
					ROUND(SUM(k.otherServiceAmount), 1) AS otherServiceAmount,

					ROUND(( 	
						COALESCE(d.consultationAmount,0) + 
						ROUND(COALESCE(e.laboratoryAmount,0), 1) +
						ROUND(COALESCE(f.medicineAmount, 0), 1) +
						COALESCE(g.hospitalizationAmount,0) + 
						ROUND(COALESCE(h.actsAmount, 0), 1)
					), 1) AS totalAll,
					j.TicketPaid AS TicketPaid,
					ROUND( ( ( 	
						COALESCE(d.consultationAmount,0) + 
						ROUND(COALESCE(e.laboratoryAmount,0), 1) +
						ROUND(COALESCE(f.medicineAmount, 0), 1) +
						COALESCE(g.hospitalizationAmount,0) + 
						ROUND(COALESCE(h.actsAmount, 0), 1)
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
								WHERE a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
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
								WHERE a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
								GROUP BY a.PatientRecordID
					) AS e
					ON a.PatientRecordID = e.PatientRecordID
					LEFT JOIN (
						SELECT a.PatientRecordID AS PatientRecordID,
								SUM(a.buyingAmount) AS buyingAmount,
								SUM(a.medicineAmount) AS medicineAmount
								FROM (
									SELECT 	a.PatientRecordID AS PatientRecordID,
											SUM(c.Quantity*d.BuyingPrice) AS buyingAmount,
											SUM(c.Quantity*d.Amount) AS medicineAmount
											FROM pa_records AS a
											INNER JOIN co_records AS b
											ON a.PatientRecordID = b.PatientRecordID
											INNER JOIN md_records AS c
											ON b.ConsultationRecordID = c.ConsultationRecordID
											INNER JOIN md_price AS d
											ON c.MedecinePriceID = d.MedecinePriceID
											WHERE a.InsuranceNameID IN ('{$cbhiid}') && 
												  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') &&
												  a.FamilyCategory IN(1, 2) &&
												  d.MedecineNameID NOT IN(17, 18, 19, 20, 51, 83, 98)
											GROUP BY a.PatientRecordID
									UNION 
										SELECT 	a.PatientRecordID AS PatientRecordID,
												SUM(c.Quantity*d.BuyingPrice) AS buyingAmount,
												SUM(c.Quantity*d.Amount) AS medicineAmount
												FROM pa_records AS a
												INNER JOIN co_records AS b
												ON a.PatientRecordID = b.PatientRecordID
												INNER JOIN md_records AS c
												ON b.ConsultationRecordID = c.ConsultationRecordID
												INNER JOIN md_price AS d
												ON c.MedecinePriceID = d.MedecinePriceID
												WHERE a.InsuranceNameID IN ('{$cbhiid}') && 
													  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%') &&
													  a.FamilyCategory NOT IN(1, 2)
												GROUP BY a.PatientRecordID
									UNION 
										SELECT 	a.PatientRecordID AS PatientRecordID,
												SUM(c.Quantity*d.BuyingPrice) AS buyingAmount,
												SUM(c.Quantity*d.Amount) AS medicineAmount
												FROM pa_records AS a
												INNER JOIN co_records AS b
												ON a.PatientRecordID = b.PatientRecordID
												INNER JOIN md_records AS c
												ON b.ConsultationRecordID = c.ConsultationRecordID
												INNER JOIN md_price AS d
												ON c.MedecinePriceID = d.MedecinePriceID
												WHERE a.InsuranceNameID NOT IN ('{$cbhiid}') && 
													  a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
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
								WHERE a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
								GROUP BY a.PatientRecordID
					) AS g
					ON a.PatientRecordID = g.PatientRecordID
					LEFT JOIN (
						SELECT 	a.PatientRecordID AS PatientRecordID,
								SUM(c.Amount*b.Quantity) AS actsAmount
								FROM pa_records AS a
								INNER JOIN ac_records AS b
								ON a.PatientRecordID = b.PatientRecordID
								INNER JOIN ac_price AS c
								ON b.ActPriceID = c.ActPriceID
								WHERE a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
								GROUP BY a.PatientRecordID
					) AS h
					ON a.PatientRecordID = h.PatientRecordID
					INNER JOIN (
						SELECT 	a.PatientRecordID AS PatientRecordID,
								c.Amount AS otherServiceAmount
								FROM pa_records AS a
								INNER JOIN sy_records AS b
								ON a.PatientRecordID = b.PatientRecordID
								INNER JOIN sy_tarif AS c
								ON b.ProductPriceID = c.TarifID
								WHERE a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
					) AS k
					ON a.PatientRecordID = k.PatientRecordID
					LEFT JOIN (
						SELECT 	a.PatientRecordID AS PatientRecordID,
								SUM(c.Amount*b.Quantity) AS consummableAmount
								FROM pa_records AS a
								INNER JOIN cn_records AS b
								ON a.PatientRecordID = b.PatientRecordID
								INNER JOIN cn_price AS c
								ON b.MedecinePriceID = c.MedecinePriceID
								WHERE a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
								GROUP BY a.PatientRecordID
					) AS l
					ON a.PatientRecordID = l.PatientRecordID
					WHERE a.DateIn LIKE('{$_GET['year']}-{$_GET['month']}%')
					GROUP BY ServiceCode
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
		<tr><th>Service</th><th>Cons Cost</th><th>Lab</th><th>Imaging</th><th>Hosp.</th><th>Proc & Mat.</th><th>Ambul.</th><th>Consum.</th><th>Buying.</th><th>Selling.</th><th>Income.</th><th>Other Services.</th></tr>
		<!-- <tr><th>&nbsp;</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th><th>100%</th></tr> -->
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

		$last_column = "L";
		$first_column = "A";

		SpanCells($activeSheet,$first_column.$row.":".$last_column.$row,$align='center');
		$activeSheet->setCellValue($first_column.$row, $cbhiMonthlyBillReportTitleMonthly['title']);

		//start write data
		$start_row = ++$row;
		//echo $start_row;
		foreach($cbhiMonthlyBillDataHeaderMonthly AS $r){
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
				if(!preg_match("/Amount$/", $fieldName) && !in_array($fieldName, ["ServiceCode"])){
					continue;
				}
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
				<td>{$r["ServiceCode"]}</td>";

				$columsCounter = 0;

				echo "<td style='text-align: right; padding: 0 10px;'>".number_format($r['consultationAmount'])."</td>";
				$othersMainTotal += $r['consultationAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['consultationAmount']):$r['consultationAmount']);
				$columsCounter++;
				echo "<td style='text-align: right; padding: 0 10px;'>".number_format($r['laboratoryAmount'])."</td>";
				$othersMainTotal += $r['laboratoryAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['laboratoryAmount']):$r['laboratoryAmount']);
				$columsCounter++;
				echo "<td>&nbsp;</td>";
				$total[$columsCounter] = "";
				$columsCounter++;
				echo "<td style='text-align: right; padding: 0 10px;'>".number_format($r['hospitalizationAmount'])."</td>";
				$othersMainTotal += $r['hospitalizationAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['hospitalizationAmount']):$r['hospitalizationAmount']);
				$columsCounter++;
				echo "<td style='text-align: right; padding: 0 10px;'>".number_format($r['actsConsummableAmount'])."</td>";
				$othersMainTotal += $r['actsConsummableAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['actsConsummableAmount']):$r['actsConsummableAmount']);
				$columsCounter++;
				echo "<td></td>";
				$total[$columsCounter] = "";
				$columsCounter++;
				echo "<td></td>";
				$total[$columsCounter] = "";
				$columsCounter++;
				echo "<td style='text-align: right; padding: 0 10px;'>".number_format($r['buyingAmount'])."</td>";
				$medicinesMainTotal += $r['buyingAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['buyingAmount']):$r['buyingAmount']);
				$columsCounter++;
				echo "<td style='text-align: right; padding: 0 10px;'>".number_format($r['medicineAmount'])."</td>";
				$medicinesMainTotal += $r['medicineAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['medicineAmount']):$r['medicineAmount']);
				$columsCounter++;

				$incomesMD = $r['medicineAmount'] - $r['buyingAmount'];
				echo "<td style='text-align: right; padding: 0 10px;'>".number_format($incomesMD)."</td>";
				//$medicinesMainTotal += $r['medicineAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$incomesMD):$incomesMD);
				$columsCounter++;
				echo "<td style='text-align: right; padding: 0 10px;'>".number_format($r['otherServiceAmount'])."</td>";
				$medicinesMainTotal += $r['otherServiceAmount'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['otherServiceAmount']):$r['otherServiceAmount']);
				$columsCounter++;
				/*echo "<td>{$r['totalAll']}</td>";
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['totalAll']):$r['totalAll']);
				$columsCounter++;
				echo "<td>{$r['TicketPaid']}</td>";
				$othersMainTotal -=  $r['TicketPaid'];
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['TicketPaid']):$r['TicketPaid']);
				$columsCounter++;
				echo "<td>{$r['totalToBePaidBack']}</td>";
				$total[$columsCounter] = (@$total[$columsCounter]?($total[$columsCounter]+$r['totalToBePaidBack']):$r['totalToBePaidBack']);
				$columsCounter++;*/
			echo "</tr>";
			// $data[] = $ddd;
		}

		
		?>
		<tr><th>Total</th>
			<?php
			$first_column = "B";
			//SpanCells($activeSheet,"A".$row.":K".$row,$align='center');
			$activeSheet->setCellValue("A".$row, "TOTAL"); 
			foreach($total AS $t){
				$t = round($t, 2);
				echo "<th style='text-align: right; padding: 0 10px;'>".number_format($t)."</th>";
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
		
		$activeSheet->setTitle("Monthly Income Summary");


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