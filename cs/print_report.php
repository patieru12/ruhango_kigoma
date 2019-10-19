<?php
session_start();
//var_dump($_SESSION['report']);
//var_dump($_SESSION['header']);
//var_dump($_SESSION['report_title']);
//echo "<pre>";
//var_dump($_SESSION);
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
		$last_column = count($data[0]) == 19?"S":"A";
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
					echo $v; echo $row;echo "<br />";
					SpanCells($activeSheet,"A".$row.":I".$row,$align='center');
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
		$objWriter->save("./facture/".$file_name);
		unset($_SESSION['report']);
}
?>
<h2>Available Downloads please select one</h2>
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
				$path = "./facture";
				
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
					$out = "<table border=1 width=100%><tr><th>Type</th><th>File</th><th>Size</th><!--<th>Created on</th>--></tr>"; $count=0;
					foreach ($Dir->TabFiles as $f) {
						//if(preg_match("/^".preg_replace("/ /","_",trim($db->select1cell("school_report_db`.`tbl_users","Name",array("ID"=>$_SESSION['u_id']),true)))."/",$f['filename']))
							$out .= "<tr><td>[".$f['extension']."]</td><td><a href='./".$f['dirname']."/".$f['filename']."'>Created on ".date("Y-m-d H:i:s",(preg_replace(array("/_/","/.".$f['extension']."/"),array(" ",""),$f['basename'])))."</a></td><td>".$f['size']." Bytes</td><!--<td align=right>".date('Y-m-d h:i:s',$f['datemodify'])."</td>--></tr>";
							//echo "<pre>";//.$f["filename"]."<br>";
							//	   print_r($f);
					}
					$out .= "</table>";
					echo $out;
				}
				
				//end scan process
				
			?>