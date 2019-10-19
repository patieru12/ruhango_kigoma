<?php
	session_start();
	require_once "../lib/db_function.php";
	//var_dump($_GET);
	$data = <<<ALLDATA
	<style>
		.list{
			width:100%;
		}
		.list tr:hover{
			background-color:#ddd;
			
		}
	</style>
ALLDATA;
	$sql = "";
	// var_dump($_GET); die();
	if(@$_GET['filter'] && trim($_GET['keyword'])){
		$keyword = PDB($_GET['keyword'], true, $con);
		$date = date("Y-m-d", time());
		$time = time();
		// && b.InsuranceNameID = 1 && b.ReceptionistID= 0 && b.cbhiAgent IS NOT NULL && b.DateIn = '{$date}
		$sql = "SELECT 	a.*,
						COALESCE(b.InsuranceCardID, b.applicationNumber, '') AS InsuranceCardID,
						b.FamilyCategory AS FamilyCategory,
						b.InsuranceNameID AS InsuranceNameID,
						b.DateIn AS DateIn,
						b.HouseManagerID AS HouseManagerID,
						c.VillageName AS VillageName,
						d.CellName AS CellName,
						e.SectorName AS SectorName,
						f.DistrictName AS DistrictName,
						b.PatientRecordID AS PatientRecordID,
						COALESCE(b.Weight, '') AS PatientWeight,
						COALESCE(b.Temperature, '') AS PatientTemperature,
						COALESCE(b.lngth, '') AS PatientLngth,
						COALESCE(b.muac, '') AS PatientMuac,
						g.InsuranceName AS InsuranceName,
						h.FormFile AS FormFile,
						b.cbhiAgent AS cbhiAgent,
						b.ReceptionistID AS ReceptionistID
						FROM pa_info AS a
						LEFT JOIN pa_records AS b
						ON a.PatientID = b.PatientID
						INNER JOIN in_name AS g
						ON b.InsuranceNameID = g.InsuranceNameID
						INNER JOIN ad_village AS c
						ON a.VillageID = c.ViillageID
						INNER JOIN ad_cell AS d
						ON c.CellID = d.CellID
						INNER JOIN ad_sector AS e
						ON d.SectorID = e.SectorID
						INNER JOIN ad_district AS f
						ON e.DistrictID = f.DistrictID
						INNER JOIN in_forms AS h
						ON b.InsuranceNameID = h.InsuranceNameID
						WHERE (a.Name LIKE('%{$keyword}%') || b.InsuranceCardID LIKE('%{$keyword}%') || a.FamilyCode LIKE('%{$keyword}%'))
						ORDER BY PatientRecordID ASC
						";
		// return;
	} else {
		// Here Search for Patient
		$date = date("Y-m-d", time());
		$time = time();
		$sql = "SELECT 	a.*,
						COALESCE(b.InsuranceCardID, b.applicationNumber, '') AS InsuranceCardID,
						b.FamilyCategory AS FamilyCategory,
						b.InsuranceNameID AS InsuranceNameID,
						b.DateIn AS DateIn,
						b.HouseManagerID AS HouseManagerID,
						c.VillageName AS VillageName,
						d.CellName AS CellName,
						e.SectorName AS SectorName,
						f.DistrictName AS DistrictName,
						b.PatientRecordID AS PatientRecordID,
						COALESCE(b.Weight, '') AS PatientWeight,
						COALESCE(b.Temperature, '') AS PatientTemperature,
						COALESCE(b.lngth, '') AS PatientLngth,
						COALESCE(b.muac, '') AS PatientMuac,
						g.InsuranceName AS InsuranceName,
						h.FormFile AS FormFile,
						b.cbhiAgent AS cbhiAgent,
						b.ReceptionistID AS ReceptionistID
						FROM pa_info AS a
						LEFT JOIN pa_records AS b
						ON a.PatientID = b.PatientID
						INNER JOIN in_name AS g
						ON b.InsuranceNameID = g.InsuranceNameID
						INNER JOIN ad_village AS c
						ON a.VillageID = c.ViillageID
						INNER JOIN ad_cell AS d
						ON c.CellID = d.CellID
						INNER JOIN ad_sector AS e
						ON d.SectorID = e.SectorID
						INNER JOIN ad_district AS f
						ON e.DistrictID = f.DistrictID
						INNER JOIN in_forms AS h
						ON b.InsuranceNameID = h.InsuranceNameID
						WHERE b.InsuranceNameID = 1 && b.ReceptionistID= 0 && b.cbhiAgent IS NOT NULL && b.DateIn = '{$date}'
						ORDER BY PatientRecordID ASC
						";
		// echo $sql;
	}
	//select all active diagnostic now
	$diagnostic = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
	// var_dump($diagnostic);
	if($diagnostic){
		$data .= "<table border=0 class=list-1 style='width:100%;'>";
		$patients= $diagnostic;
		for($i=0; $i<count($diagnostic); $i++){
			$doc_id = $diagnostic[$i];
			$dname_now = str_replace("'", " ", $patients[$i]["Name"]);
			$cname_now = str_replace("'", " ", $patients[$i]["FamilyCode"]);
			$es = ""; $pst_search = ""; $pst_add = ""; $fcat = "";
			
			$fcat .= <<<CAT
					$("#fcategory").val("{$patients[$i]["FamilyCategory"]}");
					$("#cbhi_category{$patients[$i]["FamilyCategory"]}").prop("checked", "true");
CAT;

			if($doc_id)
			$es = <<<STR
			 $(".found_link").html("<span class=error>Existing Created On {$doc_id['DateIn']} <a href=\"../forms/{$doc_id['FormFile']}?records={$doc_id['PatientRecordID']}\" target=\"_blank\">Open It?</a> {$pst_search}</span>");
STR;
			$str = <<<STR
			 $("#patient_search").val("{$patients[$i]["InsuranceCardID"]}");
			 $("#pa").val("{$patients[$i]["InsuranceCardID"]}");
			 {$es}
			 $("#name").val("{$dname_now}");
			 $("#patient_id").val("{$patients[$i]["PatientID"]}");
			 $("#age").val("{$patients[$i]["DateofBirth"]}");
			 $("#father").val("{$cname_now}");
			 
			 $("#update_").val("1");
			 $("#update").attr("checked",":true");
			 if("{$patients[$i]["Sex"]}" == "Male" || "{$patients[$i]["Sex"]}" == "M"){
				$("#male").attr("checked",":true");
			 }
			 if("{$patients[$i]["Sex"]}" == "Female" || "{$patients[$i]["Sex"]}" == "F"){
				$("#female").attr("checked",":true");
			 }
			 $("#district").val("{$patients[$i]["DistrictName"]}");
			 $("#sector").val("{$patients[$i]["SectorName"]}");
			 $("#cell").val("{$patients[$i]["CellName"]}");
			 $("#village").val("{$patients[$i]["VillageName"]}");
			 $("#weight").val("{$patients[$i]["PatientWeight"]}");
			 $("#temperature").val("{$patients[$i]["PatientTemperature"]}");
			 $("#length").val("{$patients[$i]["PatientLngth"]}");
			 $("#muac").val("{$patients[$i]["PatientMuac"]}");
			 $("#phoneNumber").val("{$patients[$i]['phoneNumber']}");
			 $("#fatherID").val("{$patients[$i]['HouseManagerID']}");
			 {$pst_add}
			 {$fcat}
STR;

if($doc_id['ReceptionistID'] != 0){
				$str = <<<RECEIVED
					$(".found_link").html("<span class=error style=\"font-size:32px;\">Received Before!</span>"); return;
RECEIVED;
			} else if($doc_id['DateIn'] != date("Y-m-d", time())){
				$str = <<<RECEIVED
					$(".found_link").html("<span class=error style=\"font-size:32px;\">Not registered to day!"); return;
RECEIVED;
			} else if(is_null($doc_id['cbhiAgent'])){
				$str = <<<RECEIVED
					$(".found_link").html("<span class=error style=\"font-size:32px;\">The Patient is not registered by CBHI Agent"); return;
RECEIVED;
			}
			$data .= "
				<tr class='activePatient' onclick='{$str}' >
					<td style='padding-bottom:5px; border-bottom: 1px solid #000;'>".$diagnostic[$i]['Name']." &nbsp;&nbsp; Age: ".getAge($patients[$i]['DateofBirth'],$notation=1, $current_date=date("Y-m-d", time()))."<br />".$diagnostic[$i]['FamilyCode']." &nbsp;&nbsp; <span class=error>Last Date: ".$diagnostic[$i]['DateIn']."</span></td>
				</tr>";
			//echo "<div style='cursor:pointer;' ".($ommit?"title='{$diagnostic[$i]['MedecineName']}'":"").">".$display." {$diagnostic[$i]['Quantity']}</div>";
		}
		$data .= "</table>";
	} else{
		$data .= "<span class=error-text >No Patient on the List</span>";
	}

	$numberOfPatient = count($diagnostic);
if(@$_GET['records']){
	$data .= <<<AUTOCLICK
	<script type="text/javascript">
		setTimeout(function(){
			LoadProfile({$_GET['records']});
		}, 100);
	</script>
AUTOCLICK;
}
if(@$_GET['response'] == 'ajax'){
	echo json_encode(array('foundPatient'=>$numberOfPatient, 'foundText'=>$data));
} else{
	echo $data;
}