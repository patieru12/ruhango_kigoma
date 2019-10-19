<?php

session_start();

require_once "../../lib/db_function.php";
if("cs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//connect to preview function
require_once "./preview-function.php";

if(function_exists('previewAuto')){
	//die;
	previewAuto($_GET['type']);
	goto style;
}
//select all saved data
$printed_prescription = array();
//$printed_address = array();
$data_print = array();
$auto_diag = returnAllData($sql="SELECT * FROM auto_diagnostic WHERE Type='la' ORDER BY Type ASC",$con);
//var_dump($auto_diag);
//var_dump($_GET);
foreach($auto_diag as $a){
	if(!in_array("co_".$a['DiagnosticID'],$printed_prescription)){
		$printed_prescription[] = "co_".$a['DiagnosticID'];
	}
	if(!in_array("la_".$a['PrescriptionID'],$printed_prescription)){
		$printed_prescription[] = "la_".$a['PrescriptionID'];
	}
	$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']] = array("qty"=>$a['Quantity']);
	
	//check if the exam result has any medicine prescription
	$auto_md_from_exams = returnAllData($sql="SELECT * FROM auto_exams WHERE Type='md' && ResultID='{$a['PrescriptionID']}' ORDER BY Type ASC",$con);
	//now loop found medicines if any
	if($auto_md_from_exams){
		foreach($auto_md_from_exams as $amd){
			if(!in_array("md_".$amd['PrescriptionID'],$printed_prescription)){
				$printed_prescription[] = "md_".$a['PrescriptionID'];
				//Add the medicine in the printable array
				$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']] = array("qty"=>$amd['Quantity']);
				
				//check if the current medicines request any act
				$auto_acts_from_medicines = returnAllData($sql="SELECT * FROM auto_medicines WHERE Type='ac' && MedecineNameID='{$amd['PrescriptionID']}' ORDER BY Type ASC",$con);
				//loop in all found result if any
				if($auto_acts_from_medicines){
					foreach($auto_acts_from_medicines as $aam){
						//check if the act is printed any where
						if(!in_array("ac_".$aam['PrescriptionID'],$printed_prescription)){
							$printed_prescription[] = "md_".$aam['PrescriptionID'];
							//Add the medicine in the printable array
							$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']] = array("qty"=>$aam['Quantity']);
							
							//check if the current acts need some consumable to accomplish its task
							$auto_cons_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='cn' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
							if($auto_cons_from_acts){
								foreach($auto_cons_from_acts as $aca){
									if(!in_array("cn_".$aca['PrescriptionID'],$printed_prescription)){
										$printed_prescription[] = "cn_".$aca['PrescriptionID'];
										//Add the medicine in the printable array
										$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['cn_'.$aca['PrescriptionID']] = array("qty"=>$aca['Quantity']);
									}
								}
							}
							//check if the current acts need some medicines to accomplish its task
							$auto_medicines_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='md' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
							if($auto_medicines_from_acts){
								foreach($auto_medicines_from_acts as $ama){
									if(!in_array("md_".$ama['PrescriptionID'],$printed_prescription)){
										$printed_prescription[] = "md_".$ama['PrescriptionID'];
										//Add the medicine in the printable array
										$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['md_'.$ama['PrescriptionID']] = array("qty"=>$ama['Quantity']);
									}
								}
							}
							//check if the current acts need some acts to accomplish its task
							$auto_acts_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='ac' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
							if($auto_acts_from_acts){
								foreach($auto_acts_from_acts as $aaa){
									if(!in_array("ac_".$aaa['PrescriptionID'],$printed_prescription)){
										$printed_prescription[] = "ac_".$aaa['PrescriptionID'];
										//Add the medicine in the printable array
										$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['ac_'.$aaa['PrescriptionID']] = array("qty"=>$aaa['Quantity']);
									}
								}
							}
						}
					}
				}
				//check if the current medicines request any Consumable
				$auto_consum_from_medicines = returnAllData($sql="SELECT * FROM auto_medicines WHERE Type='cn' && MedecineNameID='{$amd['PrescriptionID']}' ORDER BY Type ASC",$con);
				//loop in all found result if any
				if($auto_consum_from_medicines){
					foreach($auto_consum_from_medicines as $acm){
						//check if the act is printed any where
						if(!in_array("cn_".$acm['PrescriptionID'],$printed_prescription)){
							$printed_prescription[] = "cn_".$acm['PrescriptionID'];
							//Add the medicine in the printable array
							$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['cn_'.$acm['PrescriptionID']] = array("qty"=>$aam['Quantity']);
							
						}
					}
				}
			}
		}
	}
	//check if the exam result has any Acts prescription
	$auto_acts_from_exams = returnAllData($sql="SELECT * FROM auto_exams WHERE Type='ac' && ResultID='{$a['PrescriptionID']}' ORDER BY Type ASC",$con);
	//now loop found medicines if any
	if($auto_acts_from_exams){
		foreach($auto_acts_from_exams as $aae){
			if(!in_array("ac_".$aae['PrescriptionID'],$printed_prescription)){
				$printed_prescription[] = "ac_".$aae['PrescriptionID'];
				//Add the medicine in the printable array
				$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$aae['PrescriptionID']] = array("qty"=>$amd['Quantity']);
				
				//check if the current acts request any medicines
				$auto_medicines_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='md' && ActNameID='{$aae['PrescriptionID']}' ORDER BY Type ASC",$con);
				//loop in all found result if any
				if($auto_medicines_from_acts){
					foreach($auto_medicines_from_acts as $ama){
						//check if the act is printed any where
						if(!in_array("md_".$ama['PrescriptionID'],$printed_prescription)){
							$printed_prescription[] = "md_".$ama['PrescriptionID'];
							//Add the medicine in the printable array
							$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$amd['PrescriptionID']]['md_'.$ama['PrescriptionID']] = array("qty"=>$aam['Quantity']);
							
						}
					}
				}
				//check if the current acts request any Consumable
				$auto_consum_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='cn' && ActNameID='{$aae['PrescriptionID']}' ORDER BY Type ASC",$con);
				//loop in all found result if any
				if($auto_consum_from_acts){
					foreach($auto_consum_from_acts as $aca){
						//check if the act is printed any where
						if(!in_array("cn_".$aca['PrescriptionID'],$printed_prescription)){
							$printed_prescription[] = "cn_".$aca['PrescriptionID'];
							//Add the medicine in the printable array
							$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$amd['PrescriptionID']]['cn_'.$aca['PrescriptionID']] = array("qty"=>$aca['Quantity']);
							
						}
					}
				}
			}
		}
	}
	//at the exam level check any medicine prescribed by the exam result
}

//for medicines related to diagnostic directly
$auto_diag = returnAllData($sql="SELECT * FROM auto_diagnostic WHERE Type='md' ORDER BY Type ASC",$con);
//var_dump($auto_diag);
if($auto_diag){
	foreach($auto_diag as $a){
		if(!in_array("co_".$a['DiagnosticID'],$printed_prescription)){
			$printed_prescription[] = "co_".$a['DiagnosticID'];
		}
		if(!in_array("md_".$a['PrescriptionID'],$printed_prescription)){
			$printed_prescription[] = "md_".$a['PrescriptionID'];
		}
		$data_print["co_".$a['DiagnosticID']]["md_".$a['PrescriptionID']] = array("qty"=>$a['Quantity']);
		continue;
		//check if the exam result has any medicine prescription
		$auto_md_from_exams = returnAllData($sql="SELECT * FROM auto_exams WHERE Type='md' && ResultID='{$a['PrescriptionID']}' ORDER BY Type ASC",$con);
		//now loop found medicines if any
		if($auto_md_from_exams){
			foreach($auto_md_from_exams as $amd){
				if(!in_array("md_".$amd['PrescriptionID'],$printed_prescription)){
					$printed_prescription[] = "md_".$a['PrescriptionID'];
					//Add the medicine in the printable array
					$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']] = array("qty"=>$amd['Quantity']);
					
					//check if the current medicines request any act
					$auto_acts_from_medicines = returnAllData($sql="SELECT * FROM auto_medicines WHERE Type='ac' && MedecineNameID='{$amd['PrescriptionID']}' ORDER BY Type ASC",$con);
					//loop in all found result if any
					if($auto_acts_from_medicines){
						foreach($auto_acts_from_medicines as $aam){
							//check if the act is printed any where
							if(!in_array("ac_".$aam['PrescriptionID'],$printed_prescription)){
								$printed_prescription[] = "md_".$aam['PrescriptionID'];
								//Add the medicine in the printable array
								$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']] = array("qty"=>$aam['Quantity']);
								
								//check if the current acts need some consumable to accomplish its task
								$auto_cons_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='cn' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
								if($auto_cons_from_acts){
									foreach($auto_cons_from_acts as $aca){
										if(!in_array("cn_".$aca['PrescriptionID'],$printed_prescription)){
											$printed_prescription[] = "cn_".$aca['PrescriptionID'];
											//Add the medicine in the printable array
											$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['cn_'.$aca['PrescriptionID']] = array("qty"=>$aca['Quantity']);
										}
									}
								}
								//check if the current acts need some medicines to accomplish its task
								$auto_medicines_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='md' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
								if($auto_medicines_from_acts){
									foreach($auto_medicines_from_acts as $ama){
										if(!in_array("md_".$ama['PrescriptionID'],$printed_prescription)){
											$printed_prescription[] = "md_".$ama['PrescriptionID'];
											//Add the medicine in the printable array
											$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['md_'.$ama['PrescriptionID']] = array("qty"=>$ama['Quantity']);
										}
									}
								}
								//check if the current acts need some acts to accomplish its task
								$auto_acts_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='ac' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
								if($auto_acts_from_acts){
									foreach($auto_acts_from_acts as $aaa){
										if(!in_array("ac_".$aaa['PrescriptionID'],$printed_prescription)){
											$printed_prescription[] = "ac_".$aaa['PrescriptionID'];
											//Add the medicine in the printable array
											$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['ac_'.$aaa['PrescriptionID']] = array("qty"=>$aaa['Quantity']);
										}
									}
								}
							}
						}
					}
					//check if the current medicines request any Consumable
					$auto_consum_from_medicines = returnAllData($sql="SELECT * FROM auto_medicines WHERE Type='cn' && MedecineNameID='{$amd['PrescriptionID']}' ORDER BY Type ASC",$con);
					//loop in all found result if any
					if($auto_consum_from_medicines){
						foreach($auto_consum_from_medicines as $acm){
							//check if the act is printed any where
							if(!in_array("cn_".$acm['PrescriptionID'],$printed_prescription)){
								$printed_prescription[] = "cn_".$acm['PrescriptionID'];
								//Add the medicine in the printable array
								$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['cn_'.$acm['PrescriptionID']] = array("qty"=>$aam['Quantity']);
								
							}
						}
					}
				}
			}
		}
		//check if the exam result has any Acts prescription
		$auto_acts_from_exams = returnAllData($sql="SELECT * FROM auto_exams WHERE Type='ac' && ResultID='{$a['PrescriptionID']}' ORDER BY Type ASC",$con);
		//now loop found medicines if any
		if($auto_acts_from_exams){
			foreach($auto_acts_from_exams as $aae){
				if(!in_array("ac_".$aae['PrescriptionID'],$printed_prescription)){
					$printed_prescription[] = "ac_".$aae['PrescriptionID'];
					//Add the medicine in the printable array
					$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$aae['PrescriptionID']] = array("qty"=>$amd['Quantity']);
					
					//check if the current acts request any medicines
					$auto_medicines_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='md' && ActNameID='{$aae['PrescriptionID']}' ORDER BY Type ASC",$con);
					//loop in all found result if any
					if($auto_medicines_from_acts){
						foreach($auto_medicines_from_acts as $ama){
							//check if the act is printed any where
							if(!in_array("md_".$ama['PrescriptionID'],$printed_prescription)){
								$printed_prescription[] = "md_".$ama['PrescriptionID'];
								//Add the medicine in the printable array
								$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$amd['PrescriptionID']]['md_'.$ama['PrescriptionID']] = array("qty"=>$aam['Quantity']);
								
							}
						}
					}
					//check if the current acts request any Consumable
					$auto_consum_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='cn' && ActNameID='{$aae['PrescriptionID']}' ORDER BY Type ASC",$con);
					//loop in all found result if any
					if($auto_consum_from_acts){
						foreach($auto_consum_from_acts as $aca){
							//check if the act is printed any where
							if(!in_array("cn_".$aca['PrescriptionID'],$printed_prescription)){
								$printed_prescription[] = "cn_".$aca['PrescriptionID'];
								//Add the medicine in the printable array
								$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$amd['PrescriptionID']]['cn_'.$aca['PrescriptionID']] = array("qty"=>$aca['Quantity']);
								
							}
						}
					}
				}
			}
		}
		//at the exam level check any medicine prescribed by the exam result
	}
}

//for act related to diagnostic directly
$auto_diag = returnAllData($sql="SELECT * FROM auto_diagnostic WHERE Type='ac' ORDER BY Type ASC",$con);
//var_dump($auto_diag);
if($auto_diag){
	foreach($auto_diag as $a){
		if(!in_array("co_".$a['DiagnosticID'],$printed_prescription)){
			$printed_prescription[] = "co_".$a['DiagnosticID'];
		}
		if(!in_array("ac_".$a['PrescriptionID'],$printed_prescription)){
			$printed_prescription[] = "ac_".$a['PrescriptionID'];
		}
		$data_print["co_".$a['DiagnosticID']]["md_".$a['PrescriptionID']] = array("qty"=>$a['Quantity']);
		//continue;
		//check if the exam result has any medicine prescription
		$auto_md_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='md' && ActNameID='{$a['PrescriptionID']}' ORDER BY Type ASC",$con);
		//now loop found medicines if any
		if($auto_md_from_acts){
			foreach($auto_md_from_acts as $ama){
				if(!in_array("md_".$ama['PrescriptionID'],$printed_prescription)){
					$printed_prescription[] = "md_".$ama['PrescriptionID'];
					//Add the medicine in the printable array
					$data_print["co_".$a['DiagnosticID']]["ac_".$a['PrescriptionID']]["md_".$ama['PrescriptionID']] = array("qty"=>$amd['Quantity']);
					continue;
					//check if the current medicines request any act
					$auto_acts_from_medicines = returnAllData($sql="SELECT * FROM auto_medicines WHERE Type='ac' && MedecineNameID='{$amd['PrescriptionID']}' ORDER BY Type ASC",$con);
					//loop in all found result if any
					if($auto_acts_from_medicines){
						foreach($auto_acts_from_medicines as $aam){
							//check if the act is printed any where
							if(!in_array("ac_".$aam['PrescriptionID'],$printed_prescription)){
								$printed_prescription[] = "md_".$aam['PrescriptionID'];
								//Add the medicine in the printable array
								$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']] = array("qty"=>$aam['Quantity']);
								
								//check if the current acts need some consumable to accomplish its task
								$auto_cons_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='cn' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
								if($auto_cons_from_acts){
									foreach($auto_cons_from_acts as $aca){
										if(!in_array("cn_".$aca['PrescriptionID'],$printed_prescription)){
											$printed_prescription[] = "cn_".$aca['PrescriptionID'];
											//Add the medicine in the printable array
											$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['cn_'.$aca['PrescriptionID']] = array("qty"=>$aca['Quantity']);
										}
									}
								}
								//check if the current acts need some medicines to accomplish its task
								$auto_medicines_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='md' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
								if($auto_medicines_from_acts){
									foreach($auto_medicines_from_acts as $ama){
										if(!in_array("md_".$ama['PrescriptionID'],$printed_prescription)){
											$printed_prescription[] = "md_".$ama['PrescriptionID'];
											//Add the medicine in the printable array
											$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['md_'.$ama['PrescriptionID']] = array("qty"=>$ama['Quantity']);
										}
									}
								}
								//check if the current acts need some acts to accomplish its task
								$auto_acts_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='ac' && ActNameID='{$aam['PrescriptionID']}' ORDER BY Type ASC",$con);
								if($auto_acts_from_acts){
									foreach($auto_acts_from_acts as $aaa){
										if(!in_array("ac_".$aaa['PrescriptionID'],$printed_prescription)){
											$printed_prescription[] = "ac_".$aaa['PrescriptionID'];
											//Add the medicine in the printable array
											$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['ac_'.$aam['PrescriptionID']]['ac_'.$aaa['PrescriptionID']] = array("qty"=>$aaa['Quantity']);
										}
									}
								}
							}
						}
					}
					//check if the current medicines request any Consumable
					$auto_consum_from_medicines = returnAllData($sql="SELECT * FROM auto_medicines WHERE Type='cn' && MedecineNameID='{$amd['PrescriptionID']}' ORDER BY Type ASC",$con);
					//loop in all found result if any
					if($auto_consum_from_medicines){
						foreach($auto_consum_from_medicines as $acm){
							//check if the act is printed any where
							if(!in_array("cn_".$acm['PrescriptionID'],$printed_prescription)){
								$printed_prescription[] = "cn_".$acm['PrescriptionID'];
								//Add the medicine in the printable array
								$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["md_".$amd['PrescriptionID']]['cn_'.$acm['PrescriptionID']] = array("qty"=>$aam['Quantity']);
								
							}
						}
					}
				}
			}
		}
		//check if the exam result has any Acts prescription
		$auto_acts_from_exams = returnAllData($sql="SELECT * FROM auto_exams WHERE Type='ac' && ResultID='{$a['PrescriptionID']}' ORDER BY Type ASC",$con);
		//now loop found medicines if any
		if($auto_acts_from_exams){
			foreach($auto_acts_from_exams as $aae){
				if(!in_array("ac_".$aae['PrescriptionID'],$printed_prescription)){
					$printed_prescription[] = "ac_".$aae['PrescriptionID'];
					//Add the medicine in the printable array
					$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$aae['PrescriptionID']] = array("qty"=>$amd['Quantity']);
					
					//check if the current acts request any medicines
					$auto_medicines_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='md' && ActNameID='{$aae['PrescriptionID']}' ORDER BY Type ASC",$con);
					//loop in all found result if any
					if($auto_medicines_from_acts){
						foreach($auto_medicines_from_acts as $ama){
							//check if the act is printed any where
							if(!in_array("md_".$ama['PrescriptionID'],$printed_prescription)){
								$printed_prescription[] = "md_".$ama['PrescriptionID'];
								//Add the medicine in the printable array
								$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$amd['PrescriptionID']]['md_'.$ama['PrescriptionID']] = array("qty"=>$aam['Quantity']);
								
							}
						}
					}
					//check if the current acts request any Consumable
					$auto_consum_from_acts = returnAllData($sql="SELECT * FROM auto_acts WHERE Type='cn' && ActNameID='{$aae['PrescriptionID']}' ORDER BY Type ASC",$con);
					//loop in all found result if any
					if($auto_consum_from_acts){
						foreach($auto_consum_from_acts as $aca){
							//check if the act is printed any where
							if(!in_array("cn_".$aca['PrescriptionID'],$printed_prescription)){
								$printed_prescription[] = "cn_".$aca['PrescriptionID'];
								//Add the medicine in the printable array
								$data_print["co_".$a['DiagnosticID']]["la_".$a['PrescriptionID']]["ac_".$amd['PrescriptionID']]['cn_'.$aca['PrescriptionID']] = array("qty"=>$aca['Quantity']);
								
							}
						}
					}
				}
			}
		}
		//at the exam level check any medicine prescribed by the exam result
	}
}
//echo "<pre>"; var_dump($data_print);
$sp_data = array(
				"co"=>array("tbl"=>"co_diagnostic","fld"=>"DiagnosticName",'cnd'=>"DiagnosticID"),
				"la"=>array("tbl"=>"la_result","fld"=>"ResultName",'cnd'=>"ResultID"),
				"ac"=>array("tbl"=>"ac_name","fld"=>"Name",'cnd'=>"ActNameID"),
				"md"=>array("tbl"=>"md_name","fld"=>"MedecineName",'cnd'=>"MedecineNameID"),
				"cn"=>array("tbl"=>"cn_name","fld"=>"MedecineName",'cnd'=>"MedecineNameID")
				);
if($data_print)
	echo "<table border=0 style='font-size:13px; width:100%'>";
	foreach($data_print as $main=>$submain){
		$data = (explode("_",$main)[0] == "la"?returnSingleField("SELECT la_exam.ExamName FROM la_result, la_exam WHERE la_result.ExamID = la_exam.ExamID && la_result.ResultID='".explode("_",$main)[1]."'","ExamName",true,$con).": ":"").returnSingleField("SELECT `".$sp_data[explode("_",$main)[0]]['fld']."` FROM `".$sp_data[explode("_",$main)[0]]['tbl']."` WHERE `".$sp_data[explode("_",$main)[0]]['cnd']."`='".explode("_",$main)[1]."'",$sp_data[explode("_",$main)[0]]['fld'],true,$con);
		echo "<tr><td colspan=5>{$data}</td></tr>";
		//check submain has some additional data
		if(is_array($submain)){
			$i=0;
			foreach($submain as $subsubmain=>$subsubsubmain){
				//var_dump($submain); echo "<hr />";
				$data = (explode("_",$subsubmain)[0] == "la"?returnSingleField("SELECT la_exam.ExamName FROM la_result, la_exam WHERE la_result.ExamID = la_exam.ExamID && la_result.ResultID='".explode("_",$subsubmain)[1]."'","ExamName",true,$con).": ":"").returnSingleField("SELECT `".$sp_data[explode("_",$subsubmain)[0]]['fld']."` FROM `".$sp_data[explode("_",$subsubmain)[0]]['tbl']."` WHERE `".$sp_data[explode("_",$subsubmain)[0]]['cnd']."`='".explode("_",$subsubmain)[1]."'",$sp_data[explode("_",$subsubmain)[0]]['fld'],true,$con);
				$data .= @$subsubsubmain['qty']?" ".$subsubsubmain['qty']:"";
				echo "<tr><td " .($i++ < count($subsubsubmain)?"class=vert":"class=vert_demi")."><div class=horz>&nbsp;</div></td><td colspan=4>{$data}</td></tr>";
				//check if next data can be handled
				if(is_array($subsubsubmain)){
					foreach($subsubsubmain as $ssssmain=>$sssssmain){
						if($ssssmain == 'qty')
							continue;
						
						$data = (explode("_",$ssssmain)[0] == "la"?returnSingleField("SELECT la_exam.ExamName FROM la_result, la_exam WHERE la_result.ExamID = la_exam.ExamID && la_result.ResultID='".explode("_",$ssssmain)[1]."'","ExamName",true,$con).": ":"").returnSingleField("SELECT `".$sp_data[explode("_",$ssssmain)[0]]['fld']."` FROM `".$sp_data[explode("_",$ssssmain)[0]]['tbl']."` WHERE `".$sp_data[explode("_",$ssssmain)[0]]['cnd']."`='".explode("_",$ssssmain)[1]."'",$sp_data[explode("_",$ssssmain)[0]]['fld'],true,$con);
						$data .= @$subsubsubmain['qty']?" ".$subsubsubmain['qty']:"";
						echo "<tr><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=3>{$data}</td></tr>";
						//check if we have new value
						if(is_array($sssssmain)){
							foreach($sssssmain as $s6main=>$s7main){
								if($s6main == "qty")
									continue;
								$data = (explode("_",$s6main)[0] == "la"?returnSingleField("SELECT la_exam.ExamName FROM la_result, la_exam WHERE la_result.ExamID = la_exam.ExamID && la_result.ResultID='".explode("_",$s6main)[1]."'","ExamName",true,$con).": ":"").returnSingleField("SELECT `".$sp_data[explode("_",$s6main)[0]]['fld']."` FROM `".$sp_data[explode("_",$s6main)[0]]['tbl']."` WHERE `".$sp_data[explode("_",$s6main)[0]]['cnd']."`='".explode("_",$s6main)[1]."'",$sp_data[explode("_",$s6main)[0]]['fld'],true,$con);
								$data .= @$s7main['qty']?" ".$s7main['qty']:"";
								echo "<tr><td class=vert></td><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td colspan=2>{$data}</td></tr>";
								//check if the data has some new data
								if(is_array($s7main)){
									foreach($s7main as $s8main=>$s9main){
										if($s8main == "qty")
											continue;
										$data = (explode("_",$s8main)[0] == "la"?returnSingleField("SELECT la_exam.ExamName FROM la_result, la_exam WHERE la_result.ExamID = la_exam.ExamID && la_result.ResultID='".explode("_",$s8main)[1]."'","ExamName",true,$con).": ":"").returnSingleField("SELECT `".$sp_data[explode("_",$s8main)[0]]['fld']."` FROM `".$sp_data[explode("_",$s8main)[0]]['tbl']."` WHERE `".$sp_data[explode("_",$s8main)[0]]['cnd']."`='".explode("_",$s8main)[1]."'",$sp_data[explode("_",$s8main)[0]]['fld'],true,$con);
										$data .= @$s9main['qty']?" ".$s9main['qty']:"";
										echo "<tr><td class=vert></td><td class=vert></td><td class=vert></td><td class=vert><div class=horz>&nbsp;</div></td><td>{$data}</td></tr>";
									}
								}
							}
						}
					}
				}
			}
		}
	}
	echo "</table>";
	style:
?>

<style>
	.vert, .vert_demi{
		background-image:url("./../images/v.png");
		background-position:center;
		background-repeat:repeat-y;
		
	}
	/* .vert_demi{
		background-image:url("./../images/v.png");
		background-position:19px -48px;;
		background-repeat:no-repeat;
		
	} */
	.horz{
		background-image:url("./../images/h.png");
		background-position:center;
		background-repeat:repeat-x;
		margin-left:50%;
	}
</style>