<?php
//include the db_function file
require_once "../lib/db_function.php";
//var_dump($_POST);
//check if required field are their
if(!mysql_real_escape_string(trim($_POST['name']))){
	echo "<span class=error-text>Empty Patient Name</span>";
	return;
}
$_POST['name'] = mysql_real_escape_string(trim($_POST['name']));
$_POST['father'] = mysql_real_escape_string(trim($_POST['father']));
$_POST['mother'] = mysql_real_escape_string(trim($_POST['mother']));
$_POST['position'] = mysql_real_escape_string(trim($_POST['position']));

if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$_POST['dob'])){
	echo "<span class=error-text>Invalid Birth date format</span>";
	return;
}

if(date("Y-m-d",time()) < $_POST['dob']){
	echo "<span class=error-text>Invalid Birth Date</span>";
	
	return;
}
//now any required field is OK we need some other to track the patient
//var_dump($_POST);
if(trim($_POST['nid']) && (strlen($_POST['nid']) != 16 || !is_numeric($_POST['nid']) )){
	//national ID is there then validate it
	$_POST['nid'] = "";
}
if(trim($_POST['familychief']) && (strlen($_POST['familychief']) != 16 || !is_numeric($_POST['familychief']) )){
	//national ID is there then validate it
	$_POST['familychief'] = "";
}

//check if the patient exist and return his information to the receptionis
$sql = "SELECT PatientID FROM pa_info WHERE Name='{$_POST['name']}' && Father='{$_POST['father']}' && Mother='{$_POST['mother']}' && 
		DateofBirth='{$_POST['dob']}' && N_ID='{$_POST['nid']}' && FamilyCode='{$_POST['familychief']}'";
if($patient_id = returnSingleField($sql,$field="PatientID",$data=true, $con=null)){
	echo "<script>window.location='./?key={$patient_id}';</script>";
	return;
}
$sql = "INSERT INTO pa_info SET Name='{$_POST['name']}', Father='{$_POST['father']}', Mother='{$_POST['mother']}', 
		DateofBirth='{$_POST['dob']}', N_ID='{$_POST['nid']}', FamilyCode='{$_POST['familychief']}', FamilyPosition='{$_POST['position']}'";
if($patient_id = saveAndReturnID($sql, $con)){
	echo "<script>window.location='./?key={$patient_id}';</script>";
	return;
}
echo "<span class=error-text><br />Unable to save Patient</span>";
return;
?>