<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}

//var_dump($_POST);

$results = explode(";",$_POST['result']);
//var_dump($results);
if($results){
	foreach($results as $a){
		if($a){
			//check for the result id and after save the result
			if(!$r_id = returnSingleField("SELECT ResultID From la_result WHERE ResultName='".trim($a)."' && ExamID='{$_POST['exam_id']}'","ResultID",true,$con)){
				//save new Exam result and return its id
				$r_id = saveAndReturnID($sql="INSERT INTO la_result SET ResultName='".PDB($a,true,$con)."', ExamID='{$_POST['exam_id']}'", $con);
			}
			
			if($r_id){
				//save new exam records and verify everything
				saveData("INSERT la_result_record SET ExamRecordID='{$_POST['la_id']}', ResultID='{$r_id}'",$cons);
			}
		}
	}
}
?>
<span style='color:green'>All Data Saved</span>
<?php
if(@$_POST['activity'] == "save_close"){
	?>
	<br /><span style='color:orange'>Now Closing</span>
	<script>
		$(".close").click();
	</script>
	<?php
}
?>