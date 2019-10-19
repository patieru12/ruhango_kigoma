<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
/*
if(!@$_GET['ins']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}*/
if(!preg_match("/^[A,B,C,D]/",$_GET['key']))
	$_GET['key'] = date("Ymd",time()).$_GET['key'];
//var_dump($_GET['key']);
$select = "";
if(@$_GET['key']){
	$select .= "&& (pa_records.DocID LIKE('%{$_GET['key']}%'))";
}
$_GET['key'] = strtoupper($_GET['key']);
$count_consult = count($conslt = formatResultSet($rslt=returnResultSet($sql="SELECT co_category.* from co_category",$con),$multirows=true,$con));
//select all patience related to the found key search
$patients = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT pa_info.* from pa_info, pa_records WHERE pa_records.PatientID=pa_info.PatientID {$select} LIMIT 0, 20",$con),$multirows=true,$con);
//var_dump($patients);
if($patients){
	?>
	<span class=styling></span>
	<table class=list border="1" style='width:100%'>
		<tr><th>Code</th><th>Name</th><th>Age</th><th>Father</th><th>Mother</th><th>Address</th></tr>
		<?php
		for($i=0;$i<count($patients);$i++){
			?>
			Name:<b><?php echo $patients[$i]['Name'] ?></b><br/>
			Age:<b><?php echo $patients[$i]['DateofBirth'] ?></b> Sex:<b>Male</b><br/>
			Family Chief:<b><?php echo returnSingleField("SELECT Name FROM pa_info WHERE N_ID='{$patients[$i]['FamilyCode']}'","Name",$data=true, $con); ?></b><br/>
			Document Number:<b><?php echo $_GET['key'] ?></b><br/>
			<b>Details</b><br />
			Consultation: 
			<?php
			foreach($conslt as $c){
				echo "<label><input type=radio name=cons value='{$c['ConsultationCategoryName']}'>{$c['ConsultationCategoryName']}</label> ";
				//$empty_cells .= "<td style='height:20px'></td>";
			}
			?>
			<br />
			Exam:
			<div id=exam1><b>Exam 1</b><input type=text name=exam1 value='' /></div>
			<?php
			$f_o;$m_o;
			$o_f = ommitStringPart($patients[$i]["Father"],14,$f_o);
			$o_m = ommitStringPart($patients[$i]["Mother"],14,$m_o);
			echo "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}'><td>{$patients[$i]["PatientID"]}</td><td>{$patients[$i]["Name"]}</td><td>{$patients[$i]["DateofBirth"]}</td><td ".($f_o?"title='{$patients[$i]["Father"]}'":"").">{$o_f}</td><td ".($m_o?"title='{$patients[$i]["Mother"]}'":"").">{$o_m}</td><td>{$patients[$i]["VillageID"]}</td></tr>";
		}
		?>
	<table>
	<?php
} else{
	
	echo "<span class=error-text>No Match Found</span>";
}
?>