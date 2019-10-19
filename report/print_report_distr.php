<?php
session_start();
//var_dump($_SESSION['report']);
//var_dump($_SESSION['header']);
//var_dump($_SESSION['report_title']);
//echo "<pre>";
//var_dump($_SESSION['report']); die;
require_once "../lib/db_function.php";
if($_GET['format'] == "excel" && @$_SESSION['report']){
	//now generate the excel file for the report
	$file_name = time().".xlsx";
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
		
		$objPHPExcel->setActiveSheetIndex(0);
		
		$activeSheet = $objPHPExcel->getActiveSheet();
		/* 
		$activeSheet->getColumnDimension('A')->setWidth(22); */
		$activeSheet->getColumnDimension('B')->setAutoSize(true);
		
		$data = $_SESSION['report'];
		//try to write report header now
		$row = 1;
		
		$last_column = "AH";
		$first_column = "A";
		SpanCells($activeSheet,"A".$row.":".$last_column.$row,$align='center');
		$activeSheet->setCellValue($first_column.$row, $_SESSION['report_title']['title']);
		
		
		//start write data
		$start_row = ++$row;
		//echo $start_row;
		$data_count = 1;
		for($k=0;$k<count($data);$k++){
			//var_dump($data[$k]); echo "<br /><br /><br />"; continue;
			$first_column = "A";
			if(!isset($data[$k][1])){
				SpanCells($activeSheet,"A".$row.":AH".$row,$align='center');
				$activeSheet->setCellValue("A".$row, $data[$k][0]);
				//continue;
			}
			
			foreach($data[$k] as $v){
				if($k == 0)
					$v .= " ";
				$activeSheet->getColumnDimension($first_column)->setAutoSize(true);
				//if($)
				$activeSheet->setCellValue(($first_column++).$row, $v);
				//var_dump($v); echo "<br />";
				
				if($v === "SUB TOTAL"){
					//echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":I".$row,$align='center');
					$activeSheet->setCellValue("A".$row, $v);
				} else if($data[1] == ""){
					//echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":AH".$row,$align='center');
					$activeSheet->setCellValue("A".$row, $v);
					
					//echo "OKOKOKOK";
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
		$objWriter->save("./medecines/distrubution_".$file_name);
		//unset($_SESSION['report']);
}
?>
<html>
	<head>
		<title>Medicines</title>
	</head>
	<body>
<h2>Available Medicines please select one</h2>
			<input type=hidden name=error />
			<?php
				/*Display the Messages*/
				if(isset($errorMsg))
				{
					echo "<span class = 'error'>{$errorMsg}</span><br />";	
				}
				elseif(isset($successMsg))
				{
					echo "<span class = 'success'>{$successMsg}</span><br />";	
				}
				
				/////////////scan dir functions are going to be implemented here!!!
				
				//start scan process
				
				// require_the scan directory file
				require_once "../lib2/Scan/Class_ScanDir.php";
				
				// set the dir path
				$path = "./medecines";
				
				// instantiate the class.
				$Dir = new DirScan () ;
				
				// set filter to return only excel formats in the directory
				$Dir->SetFilterExt(array("xls","xlsx")) ;
				
				// enable filter
				$Dir->SetFilterEnable(true);
				
				// list all file extension disabled
				$Dir->SetFileExtListEnable(false);
				
				// enable scan sub directories
				$Dir->SetScanSubDirs(true);
				
				// enable Files Scanning
				$Dir->SetScanFiles(true);
				
				// enable full details
				$Dir->SetFullDetails(true);
				
				// run the scan
				$Dir->ScanDir($path,false);
				
				// display all the file found during scanning
				if(count($Dir->TabFiles) >0){
					$out = "<table border=1 style='border-collapse:collapse; padding:5px;'><tr><th>Type</th><th>Insurance</th><th>File</th><th>Size</th><!--<th>Created on</th>--></tr>"; $count=0;
					foreach ($Dir->TabFiles as $f) {
						$time_part = explode("_",$f['basename'])[1];
						$time = explode(".",$time_part)[0];
						//var_dump($time);
						//if(preg_match("/^".preg_replace("/ /","_",trim($db->select1cell("school_report_db`.`tbl_users","Name",array("ID"=>$_SESSION['u_id']),true)))."/",$f['filename']))
							$out .= "<tr><td>[".$f['extension']."]</td><td>".strtoupper(explode("_",$f['filename'])[0])."</td><td><a style='color:blue; text-decoration:none;' target='_blank' href='./".$f['dirname']."/".$f['filename']."'>Created on ".date("Y-m-d H:i:s",$time)."</a></td><td>".round($f['size']*(1/1024),1)." KB</td><!--<td align=right>".date('Y-m-d h:i:s',$f['datemodify'])."</td>--></tr>";
							//echo "<pre>";//.$f["filename"]."<br>";
							//	   print_r($f);
					}
					$out .= "</table>";
					echo $out;
				}
				
				//end scan process
				
			?>
			
</body>