<?php
session_start();
#var_dump($_SESSION);
$filename = "./".date('Ymd',time())." ".rand(1000,9999).".xlsx";
if(!file_exists($filename) && @$_SESSION['conflict']){
	//echo "Create file".$filename;
	
	//select all course attached to the current teacher
	$courses = $_SESSION['conflict'];
	//var_dump($courses);
	
	//require the PHPExcel library to allow writting new excel document
	require_once"../../lib2/PHPExcel/IOFactory.php";
	require_once"../../lib2/PHPExcel.php";
	#exit();
	//instantiate the PHPExcel object
	$objPHPExcel = new PHPExcel;
	
	//instantiate the writer object
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel, "Excel2007");
	
	//loop in all found courses to make the sheet on created file
	for($i=0;$i<count($courses);$i++){
	
		// Create a first sheet, representing data
		$objPHPExcel->setActiveSheetIndex($i);
		//set the excel file header;
		$header = array("A"=>0,"B"=>1,"C"=>2,"D"=>3,"E"=>4,"F"=>5,"G"=>6,"H"=>7,"I"=>8,"J"=>9,"K"=>10,"L"=>11,"M"=>12,"N"=>13,"O"=>14,"P"=>15,"Q"=>16,"R"=>17,"S"=>18);
		/* $objPHPExcel->getActiveSheet()->setCellValue('A1', "Date");
		$objPHPExcel->getActiveSheet()->setCellValue('B1', "Access Nr");
		$objPHPExcel->getActiveSheet()->setCellValue('C1', "Call No.");
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Author');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Title and Subtitle');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Coll.');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Coll. No.');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Language');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Town');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Editor');
		$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Year');
		$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Pages');
		$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Copy');
		$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Mode');
		$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Price');
		$objPHPExcel->getActiveSheet()->setCellValue('P1', 'Campus');
		$objPHPExcel->getActiveSheet()->setCellValue('Q1', 'ISBN1');
		$objPHPExcel->getActiveSheet()->setCellValue('R1', 'ISBN2');
		$objPHPExcel->getActiveSheet()->setCellValue('S1', 'Observation'); */
		
		#echo "<pre>";var_dump($courses); echo "</pre><br><br>";
		//for($studentcount=0;$studentcount<count($student);$studentcount++){
			#exit();
			for($rows=0;$rows<count($courses[$i]);$rows++){
				/* for($column='A';$column<='S';$column++){
					#var_dump($courses[$rows]);
					$value = $courses[$rows][0][$header[$column]];
					#echo $column.$rows."=>".$value."<br>";
					$objPHPExcel->getActiveSheet()->setCellValue($column.($rows+2),str_replace("\\","",$value));
				} */
				$column = 'A';
				foreach($courses[$i][$rows] as $ii=>$value){
					$objPHPExcel->getActiveSheet()->setCellValue(($column++).($rows+2),str_replace("\\","",$value));
				}
			}
		//}
		#$objPHPExcel->getActiveSheet()->setCellValue('A3', '1');
		#$objPHPExcel->getActiveSheet()->setCellValue('A4', '2');
		#$objPHPExcel->getActiveSheet()->setCellValue('A5', '3');

		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle("Error Found");

		// Create a new worksheet, after the default sheet
		$objPHPExcel->createSheet();
	}
	
	//write the file with the desired filename
	$objWriter->save($filename);
	
	//open the created file to write some new data
	//$objReader = PHPExcel_IOFactory::createReader("Excel2007");
	//load the created file
	//$objPHPExcel = $objReader->load($filename);
}
/* echo "<pre>";
var_dump($_SESSION['conflict']);
die; */
//unset the conflict session
unset($_SESSION['conflict']);
/////////////scan dir functions are going to be implemented here!!!/////////////////////////////////////
				
//start scan process

// require_the scan directory file
require_once "../../lib2/Scan/Class_ScanDir.php";

// set the dir path
$path = "../error";

// instantiate the class.
$Dir = new DirScan () ;

// set filter to return only excel formats in the directory
$Dir->SetFilterExt(array("xls","xlsx")) ;

// enable filter
$Dir->SetFilterEnable(true);

// list all file extension disabled
$Dir->SetFileExtListEnable(false);

// disable scan sub directories
$Dir->SetScanSubDirs(true);

// enable Files Scanning
$Dir->SetScanFiles(true);

// enable full details
$Dir->SetFullDetails(true);

// run the scan
$Dir->ScanDir($path,false);

// display all the file found during scanning
if(count($Dir->TabFiles) >0){
	$out = "<table border=1 style='border-collapse:collapse; width:50%'><tr><th>Type</th><th>File</th><th>Size</th><th>Created on</th></tr>"; $count=0;
	foreach ($Dir->TabFiles as $f) {
		//if(preg_match("/^".preg_replace("/ /","_",trim($db->select1cell("school_report_db`.`tbl_users","Name",array("ID"=>$_SESSION['u_id']),true)))."/",$f['filename']))
			$out .= "<tr><td>[".$f['extension']."]</td><td><a href='./".$f['filename']."'>".(preg_replace(array("/_/","/.".$f['extension']."/"),array(" ",""),$f['basename']))."</a></td><td>".$f['size']." Bytes</td><td align=right>".date('Y-m-d h:i:s',$f['datemodify'])."</td></tr>";
			//echo "<pre>";//.$f["filename"]."<br>";
			//	   print_r($f);
	}
	$out .= "</table>";
	echo $out;
} else{
	echo "No Error Log File Found!";
}
echo "<br />Click <a href='' onclick='window.close(); return false;' >here</a> to close";
//end scan process
?>