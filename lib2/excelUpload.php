<?php
//var_dump($_SESSION);
//if(!@$_SESSION) session_start();
require_once 'PHPExcel/IOFactory.php';
require_once 'filesdealer.php';

class UploadExcel{

	private $objPHPExcel;
	private $filesDealer;
	
	function __construct($inputFileName){
		$this->filesDealer = new Load();
		try{
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$this->objPHPExcel = $objReader->load($inputFileName);
		}catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
	}
	private function getLocation($tbl="books",$data=null,$con=null,$alt=null, &$list=null){
		$list = $con->fieldList($tbl);
		#var_dump($data); echo "<br>";
		#var_dump($list); echo "<br>";
		#return;
		#var_dump($alt); echo "<br>";
		$locations = array();
		foreach($data as $key=>$value){
			#echo $value."<br><br>";
			if(in_array(ucfirst($value),$list)){
				$locations[$value] = $key;
				#echo $value."=>".$key." in array<br>";
			} else{
				#echo $value;
				foreach($alt as $key2=>$value2){
					#echo "==".$value2."<br />";
					if($value2 == $value){
						$locations[$key2] = $key;
						#echo $key2."=>".$key." {$value2}=={$value}<br>";
						break;
					} 
				}
			} 
		}
		#var_dump($locations);
		return $locations;
		#echo "<br><Br><br>";
	}
	function InsertExcelInTable($con='',$parameters=''){
		//  Get worksheet dimensions
		for($id=0;$id < $this->objPHPExcel->getSheetCount();$id++){

			$sheet = $this->objPHPExcel->getSheet($id);
			#var_dump($sheet);
			#var_dump($objPHPExcel->__numberOfSheets());
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();
			#echo $highestRow; echo "<br><br>"; break;
			if($highestRow > 1000){
				echo "<span class=error>Please Each File should have at most 1000 Records<br>The Found Records Are {$highestRow}!</span>";
				exit;
			}
			//  Loop through each row of the worksheet in turn
			$start = $parameters != 'book'?2:1;
			$address = null; $fields = array(); $errorbook = 0 ; $_SESSION['conflict'] = array(); $conflictData = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html>
	<head>
		<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
		<title>Library</title>
		<meta name='keywords' content='' />
		<meta name='description' content='' />
		<link href='../../style.css' rel='stylesheet' type='text/css' media='screen' />
		<script type='text/JavaScript' src='../../js/jquery.min.js' ></script>
		<script type='text/JavaScript' src='../../js/jquery.form.js' ></script>
		<script type='text/JavaScript' src='../../js.js' ></script>
	</head>
	<body>
		<a href='../uploadbook.php?format=excel'><img src='../../images/home.png' width=30px alt='Back' /></a>
		<table border=1><tr><th>Acc. No.</th><th>Author</th><th>Title</th><th>Publisher</th><th>Year</th><th>Language</th></tr>";
			for ($row = $start; $row <= $highestRow; $row++){ 
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
												NULL,
												TRUE,
												FALSE);
				#var_dump($rowData); echo "<br /><br />";
				#echo $label; break;
				/*****************    uploading books ********************/
				if($parameters == 'book'){
					if(count($rowData[0]) < 17) break;
					if($address == null){
						$address = $this->getLocation("books",$rowData[0],$con,array("ID"=>"Access Nr","Title"=>"Title and Subtitle","Collection"=>"Coll.","CollectionNumber"=>"Coll. No.","Publisher"=>"Editor","Publication_City"=>"Town","Publication_Year"=>"Year","CallNumber"=>"Call No.","Comment"=>"Observation"),$fields);
						continue;
					}
					#print_r($address);
					if($address){
						$data = array();
						#var_dump($address); exit;
						foreach($fields as $value){
							if($value != "Classification" && $value != "AccessNumber")$data[$value]=mysql_real_escape_string(preg_replace('/"/',"",trim($rowData[0][$address[$value]])));
							#echo $value."-- ";
						}
						$class = substr($data["CallNumber"],0,3);
						#echo $class;
						$data["Classification"] = $con->select1cell("classification_low","ID",array("Label"=>$class),true);
						$data['AccessNumber'] = $data['ID'];
						unset($data['ID']);
						//search for existing records
						$data['Campus'] = $con->select1cell("compus","ID",array("Location"=>$data['Campus']),true);
						#var_dump($data); die;
						#break;
						$copy = $con->select1cell("books","Copy",$c=array("AccessNumber"=>$data['AccessNumber'],"Title"=>$data['Title'],"CallNumber"=>$data['CallNumber'],"ISBN"=>$data['ISBN'],"Author"=>$data['Author']),true);
						$data['Copy'] += $copy;
						if($con->select1cell("books","AccessNumber",array("AccessNumber"=>$data['AccessNumber']),true)){
							$errorbook += 1;
							$in_db = $con->selectOneRowFromTable($tbl='books',array("AccessNumber"=>$data['AccessNumber']),$indexed=true);
							$_SESSION['conflict'][] = $rowData;
							$conflictData .= "<tr class=in_db ><td>{$in_db['AccessNumber']}</td><td>{$in_db['Author']}</td><td>{$in_db['Title']}</td><td>{$in_db['Publisher']}</td><td>{$in_db['Publication_Year']}</td><td>{$in_db['Language']}</td></tr>";
							$conflictData .= "<tr class=from_file style='color:#aa4343;' ><td style='color:#ee4343;'>{$data['AccessNumber']}</td><td style='color:#ee4343;'>{$data['Author']}</td><td style='color:#ee4343;'>{$data['Title']}</td><td style='color:#ee4343;'>{$data['Publisher']}</td><td style='color:#ee4343;'>{$data['Publication_Year']}</td><td style='color:#ee4343;'>{$data['Language']}</td></tr>";
						} else 
							if($data['Title'] != null) $con->InsertOrUpdate($tbl="books",$data,$id_increment=true,$condition=$c,$referencefield="ErrorCount",$replace=true);
						#print($data['Date']);
					} else{
						echo "No ADDRESS FOUND!<br />";
						break;
					}
					#echo $row." =|";
					continue;
				}
				
				
				/*****************  uploading classes ********************/
				
				#echo "Try!";
				if(count($rowData[0]) != 6) break;
				$data = array("Label"=>$label = (strlen((string)$rowData[0][0]) == 1?$rowData[0][0]."00":(string)$rowData[0][0]),"Description"=>mysql_real_escape_string($rowData[0][1]));
				#var_dump($data); echo "<br /><br />";
				$con->InsertIfNotExist($tbl="classification_high",$data,$condition=array("Label"=>$label),$auto_increment=false);
				$classid = $con->select1cell("classification_high","MainClassID",array("Label"=>$label),true);
				#echo $label; break;
				$label = strlen((string)$rowData[0][2]) == 2?"0".$rowData[0][2]:(strlen((string)$rowData[0][2]) == 1?"00".$rowData[0][2]:$rowData[0][2]);
				$data = array("MainClassID"=>$classid,"Label"=>$label,"Description"=>mysql_real_escape_string($rowData[0][3]));
				#var_dump($data); echo "<br /><br />";
				$con->InsertIfNotExist($tbl="classification_medium",$data,$condition=array("Label"=>$label),$auto_increment=false);
				$classid = $con->select1cell("classification_medium","MediumClassID",array("Label"=>$label),true);
				#if($classid == 9) echo $label."<br>";
				#echo $label; break;
				$label = strlen((string)$rowData[0][4]) == 2?"0".$rowData[0][4]:(strlen((string)$rowData[0][4]) == 1?"00".$rowData[0][4]:$rowData[0][4]);
				$data = array("MediumClassID"=>$classid,"Label"=>$label,"Description"=>mysql_real_escape_string($rowData[0][5]));
				$con->InsertIfNotExist($tbl="classification_low",$data,$condition=array("Label"=>$label),$auto_increment=false);
				#var_dump($data); echo "<br /><br />";
				#echo $label; break;
				//  Insert row data array into your database of choice here
			}
			
			
			if(--$row == $highestRow){
				//write new file;
				$filename = "file/".time().".html";
				$conflictData .= "</table></body></html>";
				//file handler
				$file_handler = fopen($filename,"w+");
				//lock the file
				if(flock($file_handler,LOCK_EX)){
					//write new data
					if(fwrite($file_handler,$conflictData) == false){
						//error while writing new data
						echo "<span class=error>Error While Write log file!</span>";
					}
					flock($file_handler,LOCK_UN);
				}
				fclose($file_handler);
				echo "Sheet No:".($id+1)." <span class=success>All found data are posted in database! <span class=error><a href='./{$filename}'>{$errorbook}</a> Found Conflict in Access Number <a href='./dwnld.php'><abbr title='Download the excel file!' ><img src='../images/download2.jpg' width=20px /></abbr></a></span></span><br />";
				$errorbook = 0;
			} else{
				echo "Sheet No:".($id+1)." <span class=error>Some data could not be posted in database! <span class=error>{$errorbook} Found Conflict in Access Number</span></span><br />";
				$errorbook = 0;
			}
		}
	
	}
}
?>