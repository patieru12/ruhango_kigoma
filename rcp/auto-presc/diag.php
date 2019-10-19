<?php
session_start(); 
//var_dump($_SESSION); die;

require_once "../../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_REQUEST);
//select all automatic prescription related to the current diagnostic
$auto_presc = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT auto_diagnostic.* FROM auto_diagnostic, co_diagnostic WHERE auto_diagnostic.DiagnosticID=co_diagnostic.DiagnosticID && co_diagnostic.DiagnosticName = '".PDB($_POST['data'],true,$con)."'",$con),$multirows=false,$con);
//var_dump($auto_presc);
//echo $sql;
//connect to preview function
require_once "../../cs/auto/preview-function.php";

if(function_exists('prescribeAuto')){
	//die;
	$data = array();
	prescribeAuto("dg",$auto_presc['DiagnosticID'],$data,$_POST['age'],$_POST['weight']);
	//goto style;
	echo json_encode($data);
	return;
}
//var_dump($_POST);
$auto_presc = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT auto_diagnostic.* FROM auto_diagnostic, co_diagnostic WHERE auto_diagnostic.DiagnosticID=co_diagnostic.DiagnosticID && co_diagnostic.DiagnosticName = '".PDB($_POST['data'],true,$con)."'",$con),$multirows=true,$con);
//loop all found content and try to filter some information
$data = null;
$d = 0;
if($auto_presc){
	foreach($auto_presc as $presc){
		//if the found prescription is a medicine check rules
		if($presc['Type'] == 'md'){
			//if the medicine does not match required value discard it
			//var_dump($_POST);
			//get the row condition for the medicine in condition
			$condition = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT auto_medicines_condition.* FROM auto_medicines_condition WHERE auto_medicines_condition.MedecineNameID	='{$presc['PrescriptionID']}'",$con),$multirows=false,$con);
			//var_dump($condition);
			$signs = explode(";",$condition['ConditionSign']);
			$values = explode(";",$condition['ConditionValue']);
			
			//var_dump($signs,$values);
			if(count($signs) == 2 && count($values) == 2){
				//var_dump($signs); echo count($signs);
				//check if the provided value match the condition
				if(!$rules_id = returnSingleField($sql = "SELECT AutoActID FROM auto_medicines_condition WHERE AutoActID = '{$condition['AutoActID']}' && Type='{$condition['Type']}' && {$_POST[$condition['Type']]} {$signs[0]} {$values[0]} && {$_POST[$condition['Type']]} {$signs[1]} {$values[1]}","AutoActID",true,$con)){
					//echo $sql;
					continue;
				}
				//echo $sql;
			} else if(count($signs) == 1 && count($values) == 1){
				//check if the provided value match the condition
				if(!$rules_id = returnSingleField($sql = "SELECT AutoActID FROM auto_medicines_condition WHERE AutoActID = '{$condition['AutoActID']}' && Type='{$condition['Type']}' && {$_POST[$condition['Type']]} {$signs[0]} {$values[0]}","AutoActID",true,$con)){
					
					continue;
				}
				//echo $sql;
			}
		}
		//put the prescription in the right position
		$sub_data = array();
		$sub_data["name"] = returnSingleField("SELECT `{$tables[$presc['Type']]['tb']}`.`{$tables[$presc['Type']]['fld_v']}` FROM `{$tables[$presc['Type']]['tb']}`".(@$tables[$presc['Type']]['sp']?$tables[$presc['Type']]['sp']:"")." WHERE `{$tables[$presc['Type']]['fld_c']}`='{$presc['PrescriptionID']}'",$tables[$presc['Type']]['fld_v'],true,$con);
		$sub_data["qty"] = $presc['Quantity'];//returnSingleField("SELECT `{$tables[$presc['Type']]['tb']}`.`{$tables[$presc['Type']]['fld_v']}` FROM `{$tables[$presc['Type']]['tb']}` WHERE `{$tables[$presc['Type']]['fld_c']}`='{$presc['PrescriptionID']}'",$tables[$presc['Type']]['fld_v'],true,$con);
		if(!isset($data[$presc['Type']]))
			$data[$presc['Type']] = array($sub_data);
		else{
			$data[$presc['Type']][] = $sub_data;
		}
		//$data[$d++] = $sub_data;
	}
	/* $i = 
	foreach($additional as $ads){
		
		
	} */
}
echo json_encode($data);
?>