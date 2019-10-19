<?php
function Handlers($type){
	//var_dump($type);
	switch($type){
		case "dg":
			return array("File"=>"auto-diag.php");
			break;
		case "la":
			return array("File"=>"auto-exams.php");
			break;
		case "md":
			return array("File"=>"auto-md.php");
			break;
		case "ac":
			return array("File"=>"auto-act.php");
			break;
		default:
			;//echo "<script>alert('Some Data Could Not Be Processed Call Developer Team For help');</script>";
	}
}
function FieldCondition($type){
	//var_dump($type);
	switch($type){
		case "dg":
			return "DiagnosticID";
			break;
		case "la":
			return "ResultID";
			break;
		case "md":
		case "cn":
			return "MedecineNameID";
			break;
		case "ac":
			return "ActNameID";
			break;
		default:
			;//echo "<script>alert('Some Data Could Not Be Processed Call Developer Team For help');</script>";
	}
}
function DeleteCondition($type){
	//var_dump($type);
	switch($type){
		case "dg":
			return "DiagnosticID";
			break;
		case "la":
			return "ResultID";
			break;
		case "md":
		case "cn":
			return "MedecineNameID";
			break;
		case "ac":
			return "ActNameID";
			break;
		default:
			;//echo "<script>alert('Some Data Could Not Be Processed Call Developer Team For help');</script>";
	}
}
function DeleteConditionSubLayer($type){
	//var_dump($type);
	switch($type){
		case "dg":
		case "la":
		case "md":
		case "cn":
		case "ac":
			return "AutoActID";
			break;
		default:
			;//echo "<script>alert(' Some Data Could Not Be Processed Call Developer Team For help');</script>";
	}
}

function tb_switch_data_top($type, $condition=null){
	$names = array("cn"=>"Consumable");
	switch($type){
		case "dg":
			return returnAllData($sql="SELECT * FROM auto_diagnostic ".($condition != null?$condition:"")." ORDER BY DiagnosticID ASC, Type ASC",$con);
			break;
		case "la":
			return returnAllData($sql="SELECT * FROM auto_exams ".($condition != null?$condition:"")." ORDER BY ResultID ASC, Type ASC",$con);
			break;
		case "ac":
			return returnAllData($sql="SELECT * FROM auto_acts ".($condition != null?$condition:"")." ORDER BY ActNameID ASC",$con);
			break;
		case "md":
			return returnAllData($sql="SELECT * FROM auto_medicines ".($condition != null?$condition:"")." ORDER BY MedecineNameID ASC",$con);
			break;
		default:
			echo "<span class=error-text>".$names[$type]." is not defined</span><br /><span class=success>If necessary contact Developer Team</span><br />";
			return null;
			
	}
}

function tb_switch_data($type,$value){
	switch($type){
		case "la":
			return returnAllData($sql="SELECT * FROM auto_exams WHERE ResultID='{$value}' ORDER BY ResultID ASC, Type ASC",$con);
			break;
		case "ac":
			return returnAllData($sql="SELECT * FROM auto_acts WHERE ActNameID='{$value}' ORDER BY ActNameID ASC",$con);
			break;
		case "md":
			return returnAllData($sql="SELECT * FROM auto_medicines WHERE MedecineNameID='{$value}' ORDER BY MedecineNameID ASC",$con);
			break;
		case "cn":
			//no more search is allowed this the end1
			return false;
			break;
		default:
			echo $type." is not defined";
			return null;
			
	}
}

function tb_switch($type,$value){
	switch($type){
		case "dg":
			return returnSingleField("SELECT DiagnosticName FROM co_diagnostic WHERE DiagnosticID='".$value."'","DiagnosticName",true,$con);
			break;
		case "md":
			return returnSingleField("SELECT MedecineName FROM md_name WHERE MedecineNameID='".$value."'","MedecineName",true,$con);
			break;
		case "la":
			return returnSingleField("SELECT la_result.ResultName, la_exam.ExamName FROM la_result, la_exam WHERE la_result.ExamID = la_exam.ExamID && la_result.ResultID='".$value."'", "ExamName",true,$con)."_".returnSingleField("SELECT la_result.ResultName, la_exam.ExamName FROM la_result, la_exam WHERE la_result.ExamID = la_exam.ExamID && la_result.ResultID='".$value."'", "ResultName",true,$con);
			break;
		case "ac":
			return returnSingleField("SELECT Name FROM ac_name WHERE ActNameID='".$value."'","Name",true,$con);
			break;
		case "cn":
			return returnSingleField("SELECT MedecineName FROM cn_name WHERE MedecineNameID='".$value."'","MedecineName",true,$con);
			break;
		default:
			return "No Definition";
	}
}

function previewAuto($top="dg"){
	//echo "Preview on Top MD";
	$lavels = array();
	//check if the registered diagnostic has some additional exam result registered
	$auto_dg = tb_switch_data_top($top);
	//$auto_dg = returnAllData($sql="SELECT * FROM auto_diagnostic ORDER BY DiagnosticID ASC, Type ASC",$con);
	$table = false;
	//var_dump($auto_dg);
	//return;
	if($auto_dg){
		$table = true;
		echo "<table border=0 style='font-size:15px; width:100%'>";
		foreach($auto_dg as $dg){
			if(!@$levels[0] || !in_array($dg[FieldCondition($top)],$levels[0])){
				echo "<tr>";
					echo "<td colspan=5>".($name = str_replace("'"," ",tb_switch($top,$dg[FieldCondition($top)]))).($top != "cn"?"<a href='./".Handlers($top)['File']."?delete=".$dg[DeleteCondition($top)]."&tb=".DeleteCondition($top)."' onclick='return confirm(\"Delete {$name}\");' title='Delete From Automatic Prescription'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td>";
				echo "</tr>";
				$levels[0][] = $dg[FieldCondition($top)];
			}
			
			echo "<tr><td class=vert><div class=horz>&nbsp;</div></td><td colspan=4>{$dg['Quantity']} ".($name = str_replace("'"," ",tb_switch($dg['Type'],$dg['PrescriptionID'])))."<a href='./".Handlers($top)['File']."?update=".$dg[DeleteConditionSubLayer($dg['Type'])]."&tb=".DeleteConditionSubLayer($dg['Type'])."' title='Edit {$name}'><img src='../images/edit.png' style='padding-left:5px;' /></a>".($dg['Type'] != "cn"?"<a href='./".Handlers($dg['Type'])['File']."?delete=".$dg['PrescriptionID']."&tb=".DeleteCondition($dg['Type'])."' onclick='return confirm(\"Delete From Automatic Prescription\");' title='Delete {$name}'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td></tr>";
			//check if the found data has other sub data
			$auto_la = tb_switch_data($dg['Type'],$dg['PrescriptionID']); //returnAllData($sql="SELECT * FROM auto_exams WHERE ResultID='{$dg['PrescriptionID']}' && Type='md' ORDER BY ResultID ASC",$con);
			//var_dump($auto_la); return;
			//check if the found exam result has any registered medicines prescription
			//$auto_la = returnAllData($sql="SELECT * FROM auto_exams WHERE ResultID='{$dg['PrescriptionID']}' && Type='md' ORDER BY ResultID ASC",$con);
			if($auto_la){
				foreach($auto_la as $la){
					//echo "<tr><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=4>".returnSingleField("SELECT MedecineName FROM md_name WHERE MedecineNameID='".$la['PrescriptionID']."'","MedecineName",true,$con)."{$la['Type']}</td></tr>";
					echo "<tr><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=4>{$la['Quantity']} ".($name = str_replace("'"," ",tb_switch($la['Type'],$la['PrescriptionID'])))."<a href='./".Handlers($dg['Type'])['File']."?update=".$la[DeleteConditionSubLayer($la['Type'])]."&tb=".DeleteConditionSubLayer($la['Type'])."' title='Edit {$name}'><img src='../images/edit.png' style='padding-left:5px;' /></a>".($la['Type'] != "cn"?"<a href='./".Handlers($la['Type'])['File']."?delete=".$la['PrescriptionID']."&tb=".DeleteCondition($la['Type'])."' onclick='return confirm(\"Delete From Automatic Prescription\");' title='Delete {$name}'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td></tr>";
					//check if the found data has any other prescription
					
					$auto_md = tb_switch_data($la['Type'],$la['PrescriptionID']);
					//var_dump($auto_md);
					//check if the found medicines has any act prescription
					//$auto_md = returnAllData($sql="SELECT * FROM auto_medicines WHERE MedecineNameID='{$la['PrescriptionID']}' && Type='ac' ORDER BY MedecineNameID ASC",$con);
					if($auto_md){
						foreach($auto_md as $md){
							echo "<tr><td class=vert></td><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=2>{$md['Quantity']} ".($name = str_replace("'"," ",tb_switch($md['Type'],$md['PrescriptionID'])))."<a href='./".Handlers($la['Type'])['File']."?update=".$md[DeleteConditionSubLayer($md['Type'])]."&tb=".DeleteConditionSubLayer($md['Type'])."' title='Edit {$name}'><img src='../images/edit.png' style='padding-left:5px;' /></a>".($md['Type'] != "cn"?"<a href='./".Handlers($md['Type'])['File']."?delete=".$md['PrescriptionID']."&tb=".DeleteCondition($md['Type'])."' onclick='return confirm(\"Delete From Automatic Prescription\");' title='Delete {$name}'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td></tr>";
							//check if the found date has any other prescription
							$auto_ac = tb_switch_data($md['Type'],$md['PrescriptionID']);
							//echo "<tr><td class=vert></td><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=2>"; var_dump($auto_ac); echo "</td></tr>";
							//check if the found act has any medicines prescription
							//$auto_ac = returnAllData($sql="SELECT * FROM auto_acts WHERE ActNameID='{$md['PrescriptionID']}' && Type='md' ORDER BY ActNameID ASC",$con);
							if($auto_ac){
								foreach($auto_ac as $ac){
									echo "<tr><td class=vert></td><td class=vert></td><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td>{$ac['Quantity']} ".($name = str_replace("'"," ",tb_switch($ac['Type'],$ac['PrescriptionID'])))."<a href='./".Handlers($md['Type'])['File']."?update=".$ac[DeleteConditionSubLayer($ac['Type'])]."&tb=".DeleteConditionSubLayer($ac['Type'])."' title='Edit {$name}'><img src='../images/edit.png' style='padding-left:5px;' /></a>".($ac['Type'] != "cn"?"<a href='./".Handlers($ac['Type'])['File']."?delete=".$ac['PrescriptionID']."&tb=".DeleteCondition($ac['Type'])."' onclick='return confirm(\"Delete From Automatic Prescription\");' title='Delete {$name}'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td></tr>";
									//please no more search that is enough
								}
							}
						}
					}
				}
			}
			
		}
		//echo "</table>";
	}
	
	if($table){
		echo "</table>";
	} else{
		echo "<span class=error-text>No Automatic Prescription Found</span>";
	}
}

function prescribeAuto($top,$value,&$return_data=null,$age=-1, $wght=-1){
	//echo "Preview on Top MD";
	$lavels = array();
	//check if the registered diagnostic has some additional exam result registered
	$auto_dg = tb_switch_data_top($top);
	//$auto_dg = returnAllData($sql="SELECT * FROM auto_diagnostic ORDER BY DiagnosticID ASC, Type ASC",$con);
	$table = false;
	//var_dump($auto_dg);
	//return;
	if($auto_dg){
		$table = true;
		//echo "<table border=0 style='font-size:15px; width:100%'>";
		foreach($auto_dg as $dg){
			/* if(!@$levels[0] || !in_array($dg[FieldCondition($top)],$levels[0])){
				echo "<tr>";
					echo "<td colspan=5>".($name = str_replace("'"," ",tb_switch($top,$dg[FieldCondition($top)]))).($top != "cn"?"<a href='./".Handlers($top)['File']."?delete=".$dg[DeleteCondition($top)]."&tb=".DeleteCondition($top)."' onclick='return confirm(\"Delete {$name}\");' title='Delete From Automatic Prescription'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td>";
				echo "</tr>";
				$levels[0][] = $dg[FieldCondition($top)];
			}
			 */
			//echo "<tr><td class=vert><div class=horz>&nbsp;</div></td><td colspan=4>{$dg['Quantity']} ".."<a href='./".Handlers($top)['File']."?update=".$dg[DeleteConditionSubLayer($dg['Type'])]."&tb=".DeleteConditionSubLayer($dg['Type'])."' title='Edit {$name}'><img src='../images/edit.png' style='padding-left:5px;' /></a>".($dg['Type'] != "cn"?"<a href='./".Handlers($dg['Type'])['File']."?delete=".$dg['PrescriptionID']."&tb=".DeleteCondition($dg['Type'])."' onclick='return confirm(\"Delete From Automatic Prescription\");' title='Delete {$name}'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td></tr>";
			//if the type is medicines the filter with the corresponding defined rules
			
			if($dg['Type'] == 'md_'){
				//check if the patient meet required condition
				$cnd_check = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT auto_medicines_condition.* FROM auto_medicines_condition WHERE auto_medicines_condition.MedecineNameID	='{$dg['PrescriptionID']}'",$con),$multirows=false,$con);
				//var_dump($cnd_check);
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
			$name = str_replace("'"," ",tb_switch($dg['Type'],$dg['PrescriptionID']));
			$data = array("name"=>explode("_",$name)[0],"Qty"=>$dg['Quantity']);
			if($dg['Type'] == "la"){
				$data['resultname'] = explode("_",$name)[1];
			}
			$return_data[$dg['Type']][] = $data;
			//check if the found data has other sub data
			$auto_la = tb_switch_data($dg['Type'],$dg['PrescriptionID']); //returnAllData($sql="SELECT * FROM auto_exams WHERE ResultID='{$dg['PrescriptionID']}' && Type='md' ORDER BY ResultID ASC",$con);
			//var_dump($auto_la); return;
			//check if the found exam result has any registered medicines prescription
			//$auto_la = returnAllData($sql="SELECT * FROM auto_exams WHERE ResultID='{$dg['PrescriptionID']}' && Type='md' ORDER BY ResultID ASC",$con);
			if($auto_la){
				foreach($auto_la as $la){
					//echo "<tr><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=4>".returnSingleField("SELECT MedecineName FROM md_name WHERE MedecineNameID='".$la['PrescriptionID']."'","MedecineName",true,$con)."{$la['Type']}</td></tr>";
					//echo "<tr><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=4>{$la['Quantity']} ".()."<a href='./".Handlers($dg['Type'])['File']."?update=".$la[DeleteConditionSubLayer($la['Type'])]."&tb=".DeleteConditionSubLayer($la['Type'])."' title='Edit {$name}'><img src='../images/edit.png' style='padding-left:5px;' /></a>".($la['Type'] != "cn"?"<a href='./".Handlers($la['Type'])['File']."?delete=".$la['PrescriptionID']."&tb=".DeleteCondition($la['Type'])."' onclick='return confirm(\"Delete From Automatic Prescription\");' title='Delete {$name}'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td></tr>";
					//check if the found data has any other prescription
					
					$name = str_replace("'"," ",tb_switch($la['Type'],$la['PrescriptionID']));
					
					$data = array("name"=>explode("_",$name)[0],"Qty"=>$la['Quantity']);
					if($la['Type'] == "la"){
						$data['resultname'] = explode("_",$name)[1];
					}
					$return_data[$la['Type']][] = $data;
					
					$auto_md = tb_switch_data($la['Type'],$la['PrescriptionID']);
					//var_dump($auto_md);
					//check if the found medicines has any act prescription
					//$auto_md = returnAllData($sql="SELECT * FROM auto_medicines WHERE MedecineNameID='{$la['PrescriptionID']}' && Type='ac' ORDER BY MedecineNameID ASC",$con);
					if($auto_md){
						foreach($auto_md as $md){
							//echo "<tr><td class=vert></td><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=2>{$md['Quantity']} ".()."<a href='./".Handlers($la['Type'])['File']."?update=".$md[DeleteConditionSubLayer($md['Type'])]."&tb=".DeleteConditionSubLayer($md['Type'])."' title='Edit {$name}'><img src='../images/edit.png' style='padding-left:5px;' /></a>".($md['Type'] != "cn"?"<a href='./".Handlers($md['Type'])['File']."?delete=".$md['PrescriptionID']."&tb=".DeleteCondition($md['Type'])."' onclick='return confirm(\"Delete From Automatic Prescription\");' title='Delete {$name}'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td></tr>";
							$name = str_replace("'"," ",tb_switch($md['Type'],$md['PrescriptionID']));
							$data = array("name"=>explode("_",$name)[0],"Qty"=>$md['Quantity']);
							if($md['Type'] == "la"){
								$data['resultname'] = explode("_",$name)[1];
							}
							$return_data[$md['Type']][] = $data;
					
							//check if the found date has any other prescription
							$auto_ac = tb_switch_data($md['Type'],$md['PrescriptionID']);
							//echo "<tr><td class=vert></td><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=2>"; var_dump($auto_ac); echo "</td></tr>";
							//check if the found act has any medicines prescription
							//$auto_ac = returnAllData($sql="SELECT * FROM auto_acts WHERE ActNameID='{$md['PrescriptionID']}' && Type='md' ORDER BY ActNameID ASC",$con);
							if($auto_ac){
								foreach($auto_ac as $ac){
									//echo "<tr><td class=vert></td><td class=vert></td><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td>{$ac['Quantity']} ".($name = str_replace("'"," ",tb_switch($ac['Type'],$ac['PrescriptionID'])))."<a href='./".Handlers($md['Type'])['File']."?update=".$ac[DeleteConditionSubLayer($ac['Type'])]."&tb=".DeleteConditionSubLayer($ac['Type'])."' title='Edit {$name}'><img src='../images/edit.png' style='padding-left:5px;' /></a>".($ac['Type'] != "cn"?"<a href='./".Handlers($ac['Type'])['File']."?delete=".$ac['PrescriptionID']."&tb=".DeleteCondition($ac['Type'])."' onclick='return confirm(\"Delete From Automatic Prescription\");' title='Delete {$name}'><img src='../images/delete.png' style='padding-left:5px;' /></a>":"")."</td></tr>";
									//please no more search that is enough
									$name = str_replace("'"," ",tb_switch($ac['Type'],$ac['PrescriptionID']));
									$data = array("name"=>explode("_",$name)[0],"Qty"=>$ac['Quantity']);
									if($ac['Type'] == "la"){
										$data['resultname'] = explode("_",$name)[1];
									}
									$return_data[$ac['Type']][] = $data;
					
								}
							}
						}
					}
				}
			}
			
		}
		//echo "</table>";
	}
	
}
?>