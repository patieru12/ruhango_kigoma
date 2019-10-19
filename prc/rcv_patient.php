
<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("prc" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_POST);
if(!trim($_POST['patientWeigth']) || $_POST['patientWeigth'] <= 0){
	echo "<span class='error-text'>Please Fill in Weight</span>";
}

if(!trim($_POST['patientIDSearch']) || $_POST['patientIDSearch'] <= 0){
	echo "<span class='error-text'>Provide the Code</span>";
}
// var_dump($_POST);

//check if the patience ID exist Before
$patienceID = PDB($_POST['patientIDSearch'], true, $con);
$sql = "SELECT 	a.*
				FROM pa_info AS a
				WHERE a.PatientID = '{$patienceID}'";
$data = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=false,$con);

// var_dump($data);
if(is_null($data)) {
	$sql = "SELECT 	MAX(a.PatientID) AS maxID
					FROM pa_info AS a
					";
	// echo $sql;
	$patienceID = returnSingleField($sql,$field="maxID",true, $con);
	// var_dump($lastID);
	$patienceID++;

	// Create the Patient info data
	$sql = "INSERT INTO pa_info SET PatientID={$patienceID}, Name='', DateofBirth='0000-00-00', Sex='', FamilyCode='', VillageID=1, AffiliateName='', Affectation='', phoneNumber=''";
	$patienceID = saveAndReturnID($sql, $con);
}


if($patienceID){
	$weight = PDB($_POST['patientWeigth'], true,$con);
	$temp 	= PDB($_POST['patientTemp'], true,$con);
	$lngth 	= PDB($_POST['patientLength'], true,$con);
	$muac 	= PDB($_POST['patientMUAC'], true,$con);
	$currentTime = time();
	$currentMonth = date("Y-m", time());
	$currentDate = date("Y-m-d", time());

	$sql = "SELECT 	MAX(a.monthlyID) AS maxID
					FROM pa_records AS a
					WHERE a.DateIn LIKE('{$currentMonth}%')
					";
	$monthlyID = returnSingleField($sql,$field="maxID",true, $con);
	$monthlyID++;
	
	$sql = "SELECT 	MAX(a.dailyID) AS maxID
					FROM pa_records AS a
					WHERE a.DateIn = '{$currentDate}'
					";
	$dailyID = returnSingleField($sql,$field="maxID",true, $con);
	$dailyID++;

	// Create patient Records info
	$sql = "INSERT INTO pa_records SET monthlyID='{$monthlyID}', dailyID='{$dailyID}', DocID='', PatientID='{$patienceID}', Weight='{$weight}', Temperature='{$temp}', DateIn=NOW(), VillageID=1, TimeIn='{$currentTime}', lngth='{$lngth}', muac='{$muac}'";
	$patienceID = saveAndReturnID($sql, $con);
	if($patienceID){
		echo "New Record Created at <br />".date("Y-m-d H:i:s", time());
		echo "<a id='print_data' target='_blank' href='./print_small_ticket.php?record_id={$patienceID}'>Done</a>";
		?>
		<script type="text/javascript">
			setTimeout(function(e){
				$("#print_data")[0].click();
			}, 600);
		</script>
		<?php
	}
}
?>