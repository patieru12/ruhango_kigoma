<?php
session_start();
require_once "../../lib/db_function.php";
// var_dump($_POST);
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$error = "";
$foundData = "";

// Quanter information here
$quaterName = date("Y",time()).$quaters[date("m",time())];
$quaterID 	= formatResultSet($rslt= returnResultSet("SELECT 	a.QuarterID AS QuarterID
																FROM la_quarters AS a
																WHERE QuarterName = '{$quaterName}'
																",$con), false, $con);
$qID = null;
if(!is_array($quaterID)){
	$qID = saveAndReturnID($sql="INSERT INTO la_quarters SET QuarterName = '{$quaterName}'", $con);
} else{
	$qID = $quaterID['QuarterID'];
}

$ConsultationRecordID = NULL;

foreach($_POST AS $key=>$value){
	if(preg_match("/laSample/", $key)){
		if(strlen($foundData) > 0){
			$foundData .= ":";
		}
		$patientID = str_replace("laSample_", "", $key);

		$foundData .= generateLaboIDs($patientID, $quaters, $ConsultationRecordID);
	}
}

if(strlen($foundData) <= 0){
	echo "<span class='error-text'>No Sample is taken</span>";
	return;
} else{
	?>
	<a href="./printSmallticket.php?data=<?= $foundData ?>" target="_blank" id="downloadSample">Print</a>
	<script type="text/javascript">
		setTimeout(function(e){
			LoadProfile("<?= $ConsultationRecordID ?>");
		}, 100);
		
	</script>
	<?php
}
?>