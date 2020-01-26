<?php
session_start();
require_once "../lib/db_function.php";
// Check if the current Consultant has a registerd to be used
$registerId = returnSingleField("SELECT id FROM sy_register WHERE consultantId='{$_SESSION['user']['UserID']}'", "id", true, $con);
if(!$registerId){
	echo "<script> window.location='../se_select.php?msg=select the register please and service'; </script>";
	return;
}
// var_dump($_GET);
$patientID = $_GET['recordid'];

//Here Check if the Patience has a TB Record
$tbINfo = formatResultSet($rslt=returnResultSet("SELECT 	a.id
															FROM tb_records AS a
															WHERE PatientRecordID = '{$patientID}'
															", $con), false, $con);
if(!$tbINfo){
	saveData("INSERT INTO tb_records SET PatientRecordID='{$patientID}', Date=NOW()", $con);
}
$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
															b.CategoryID AS insuranceCategory,
															c.Name AS patientName,
															c.DateofBirth AS DateofBirth,
															b.InsuranceName AS InsuranceName,
															d.TypeofPayment AS TypeofPayment,
															d.ValuePaid AS ValuePaid,
															c.phoneNumber AS phoneNumber,
															c.sex AS patientGender,
															b.InsuranceNameID AS InsuranceNameID,
															c.phoneNumber AS phoneNumber,
															e.VillageName AS VillageName,
															f.CellName AS CellName,
															g.SectorName AS SectorName,
															h.DistrictName AS DistrictName,
															c.FamilyCode AS FamilyCode,
															COALESCE(i.RegisterNumber, '')AS RegisterNumber,
															i.ConsultationRecordID AS ConsultationRecordID,
															k.ServiceCode AS ServiceCode,
															COALESCE(l.id, '') AS tbId,
															m.id AS tbRecordId,
															m.coughDuration AS coughDuration,
															m.fever AS fever,
															m.night_sweat AS night_sweat,
															m.weight_loss AS weight_loss,
															m.tb_contact AS tb_contact,
															m.hiv AS hiv,
															m.prisoner AS prisoner,
															m.presumptive_case AS presumptive_case,
															m.tst AS tst,
															m.labo_result AS labo_result,
															m.comment AS comment
															FROM pa_records AS a
															INNER JOIN in_name AS b
															ON a.InsuranceNameID = b.InsuranceNameID
															INNER JOIN pa_info AS c
															ON a.PatientID = c.PatientID
															INNER JOIN in_price AS d
															ON b.InsuranceNameID = d.InsuranceNameID
															INNER JOIN ad_village AS e
															ON a.VillageID = e.ViillageID
															INNER JOIN ad_cell AS f
															ON e.CellID = f.CellID
															INNER JOIN ad_sector AS g
															ON f.SectorID = g.SectorID
															INNER JOIN ad_district AS h
															ON g.DistrictID = h.DistrictID
															INNER JOIN co_records AS i
															ON a.PatientRecordID = i.PatientRecordID
															INNER JOIN se_records AS j
															ON a.PatientRecordID = j.PatientRecordID
															INNER JOIN se_name AS k
															ON j.ServiceNameID = k.ServiceNameID
															INNER JOIN tb_records AS l
															ON a.PatientRecordID = l.PatientRecordID
															LEFT JOIN tb_co_records AS m
															ON l.id = m.tbId
															WHERE a.PatientRecordID ='{$patientID}'
															", $con), false, $con);
// Check if the patient has any records in tb_co_records
// var_dump($patient);
?>

Name:<b><?= $patient['patientName'] ?></b><br/>
Date Of Birth: <?= $patient['DateofBirth'] ?>&nbsp;&nbsp;&nbsp;Age: <?= $myAge = getAge($patient['DateofBirth'],$notation=1, $current_date=$patient['DateIn'])  ?><br/>
Gender:<b><?= $patient['patientGender'] ?></b><br/>
<h2>Symptoms</h2>
<table style="width: 100%">
	<tr>
		<th> Cough Duration[days]</th>
		<th> Fever</th>
		<th> Night Sweat</th>
		<th> Weight Losss</th>
	</tr>
	<tr>
		<td style="text-align: center;">
			<input type="text" id="coughDuration" name="coughDuration" value='<?= $patient['coughDuration'] ?>' >
		</td>
		<td class="text-center">
			<input type="checkbox" id="fever" name="fever" <?= $patient['fever']?"checked":"" ?>>
		</td>
		<td class="text-center">
			<input type="checkbox" id="night_sweat" name="night_sweat" <?= $patient['night_sweat']?"checked":"" ?>>
		</td>
		<td class="text-center">
			<input type="checkbox" id="weight_loss" name="weight_loss" <?= $patient['weight_loss']?"checked":"" ?>>
		</td>
	</tr>
</table>
<h2>High risk group</h2>
<table style="width: 100%">
	<tr>
		<th> < 15 years</th>
		<th> ≥ 55 years</th>
		<th> TB Contact</th>
		<th> HIV+</th>
		<th> Prisonner</th>
	</tr>
	<tr>
		<td style="text-align: center;">
			<?php
			if(preg_match("/yrs$/", $myAge)){
				// echo "The Year Pattern Found.";
				$onlyAge = preg_replace("/yrs$/", "", $myAge);
				// echo " ".$onlyAge;
				if($onlyAge < 15){
					?>
					<span class="fa fa-check text-danger"></span>
					<?php
				} else{
					?>
					<span class="fa fa-times text-success"></span>
					<?php
				}
			} else{
				?>
				<span class="fa fa-check text-danger"></span>
				<?php
			}
			?>
		</td>
		<td style="text-align: center;">
			<?php
			if(preg_match("/yrs$/", $myAge)){
				// echo "The Year Pattern Found.";
				$onlyAge = preg_replace("/yrs$/", "", $myAge);
				// echo " ".$onlyAge;
				if($onlyAge >= 55){
					?>
					<span class="fa fa-check text-danger"></span>
					<?php
				} else{
					?>
					<span class="fa fa-times text-success"></span>
					<?php
				}
			} else{
				?>
				<span class="fa fa-times text-success"></span>
				<?php
			}
			?>
		</td>
		<td class="text-center">
			<input type="checkbox" id="tb_contact" name="tb_contact" <?= $patient['tb_contact']?"checked":"" ?>>
		</td>
		<td class="text-center">
			<input type="checkbox" id="hiv" name="hiv" <?= $patient['hiv']?"checked":"" ?>>
		</td>
		<td class="text-center">
			<input type="checkbox" id="prisoner" name="prisoner" <?= $patient['prisoner']?"checked":"" ?>>
		</td>
	</tr>
</table>
<h2>TB Presumptive case</h2>
<table style="width: 100%;">
	<tr>
		<th>Yes</th>
		<th>No</th>
	</tr>
	<tr>
		<td class="text-center">
			<input type="checkbox" id="presumptive_case_yes" name="presumptive_case_yes" <?= $patient['presumptive_case']?"checked":"" ?>>
		</td>
		<td class="text-center">
			<input type="checkbox" id="presumptive_case_no" name="presumptive_case_no" <?= !is_null($patient['presumptive_case']) && !$patient['presumptive_case']?"checked":"" ?>>
		</td>
	</tr>
</table>
<h2>Tuberculin Skin Test</h2>
<table style="width: 100%;">
	<tr>
		<td class="text-right">Inducation in mm</td>
		<td class="text-left">
			<input type="text" id="tst" name="tst" value='<?= $patient['tst'] ?>' >
		</td>
	</tr>
</table>
<h2>Laboratory Results</h2>
<table style="width: 100%;">
	<tr>
		<th>Pos.</th>
		<th>Neg.</th>
		<th>Not Done</th>
	</tr>
	<tr>
		<td class="text-center">
			<input type="checkbox" id="labo_result_pos" name="labo_result_pos" <?= $patient['labo_result']==1?"checked":"" ?>>
		</td>
		<td class="text-center">
			<input type="checkbox" id="labo_result_neg" name="labo_result_neg" <?= $patient['labo_result']==2?"checked":"" ?>>
		</td>
		<td class="text-center">
			<input type="checkbox" id="labo_result_not_done" name="labo_result_not_done" <?= $patient['labo_result'] == 3	?"checked":"" ?>>
		</td>
	</tr>
</table>
<h2>Observation</h2>
<table style="width: 100%;">
	<tr>
		<td class="text-center">
			<textarea type="text" style="width:100%;" id="comment" name="comment" ><?= $patient['comment'] ?></textarea>
		</td>
	</tr>
</table>
<div class="result"></div>
<input type="button" name="saveAll" id="saveAll" value="Save" class="flatbtn-blu" />
<script type="text/javascript">
	$(document).ready(function(e){
		$("#coughDuration").blur(function(e){
			// alert("please save the coughDuration info");
			$.ajax({
				type: "POST",
				url: "./tb/save_cough.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&coughDuration=" + $("#coughDuration").val() + "&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#fever").change(function(e){
			// alert("Fever info changed update your db now");
			var feverInfo = $("#fever").prop("checked")?1:0;
			$.ajax({
				type: "POST",
				url: "./tb/save_fever.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&fever=" + feverInfo + "&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#night_sweat").change(function(e){
			// alert("Fever info changed update your db now");
			var night_sweatINfo = $("#night_sweat").prop("checked")?1:0;
			$.ajax({
				type: "POST",
				url: "./tb/save_sweat.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&night_sweat=" + night_sweatINfo + "&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#weight_loss").change(function(e){
			// alert("Fever info changed update your db now");
			var weight_lossINfo = $("#weight_loss").prop("checked")?1:0;
			$.ajax({
				type: "POST",
				url: "./tb/save_weight.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&weight_loss=" + weight_lossINfo + "&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#tb_contact").change(function(e){
			// alert("Fever info changed update your db now");
			var tb_contactINfo = $("#tb_contact").prop("checked")?1:0;
			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + tb_contactINfo + "&field=tb_contact&message=TB Contact&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#hiv").change(function(e){
			// alert("Fever info changed update your db now");
			var hivINfo = $("#hiv").prop("checked")?1:0;
			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + hivINfo + "&field=hiv&message=HIV Positive Case&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#prisoner").change(function(e){
			// alert("Fever info changed update your db now");
			var prisonerINfo = $("#prisoner").prop("checked")?1:0;
			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + prisonerINfo + "&field=prisoner&message=Prisonner Case&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#presumptive_case_yes").change(function(e){
			//
			var yesChecked = $("#presumptive_case_yes").prop("checked")?1:0;
			var presumptive_case;
			if(yesChecked){
				$("#presumptive_case_no").removeProp("checked");
				presumptive_case = 1;
			} else{
				$("#presumptive_case_no").prop("checked",":true");
				presumptive_case = 0;
			}

			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + presumptive_case + "&field=presumptive_case&message=Presumptive info&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#presumptive_case_no").change(function(e){
			//
			var noChecked = $("#presumptive_case_no").prop("checked")?1:0;
			var presumptive_case;
			if(noChecked){
				$("#presumptive_case_yes").removeProp("checked");
				presumptive_case = 0;
			} else{
				$("#presumptive_case_yes").prop("checked",":true");
				presumptive_case = 1;
			}

			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + presumptive_case + "&field=presumptive_case&message=Presumptive info&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#tst").blur(function(e){
			// alert("please save the coughDuration info");
			var tstInfo = $(this).val();
			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + tstInfo + "&field=tst&message=TST info&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#labo_result_pos").change(function(e){
			//
			var posChecked = $("#labo_result_pos").prop("checked")?1:0;
			var labo_result;
			if(posChecked){
				$("#labo_result_neg").removeProp("checked");
				$("#labo_result_not_done").removeProp("checked");
				labo_result = 1;
			} else{
				labo_result = null;
			}

			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + labo_result + "&field=labo_result&message=Laboratory Results info&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#labo_result_neg").change(function(e){
			//
			var negChecked = $("#labo_result_neg").prop("checked")?1:0;
			var labo_result;
			if(negChecked){
				$("#labo_result_pos").removeProp("checked");
				$("#labo_result_not_done").removeProp("checked");
				labo_result = 2;
			} else{
				labo_result = null;
			}

			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + labo_result + "&field=labo_result&message=Laboratory Results info&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#labo_result_not_done").change(function(e){
			//
			var notChecked = $("#labo_result_not_done").prop("checked")?1:0;
			var labo_result;
			if(notChecked){
				$("#labo_result_pos").removeProp("checked");
				$("#labo_result_neg").removeProp("checked");
				labo_result = 3;
			} else{
				labo_result = null;
			}

			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + labo_result + "&field=labo_result&message=Laboratory Results info&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#comment").blur(function(e){
			// alert("please save the coughDuration info");
			var tstInfo = $(this).val();
			$.ajax({
				type: "POST",
				url: "./tb/save_param.php",
				data: "tbRecordId=<?= $patient['tbRecordId'] ?>&tbId=<?= $patient['tbId'] ?>&value=" + tstInfo + "&field=comment&message=Observation info&url=ajax",
				cache: false,
				success: function(result){
					$(".result").html(result)
					//console.log(result);
				},
				error: function(err){
					console.log(err.responseText());
				}
			});
		});

		$("#saveAll").click(function(e){
			$(".result").html("<img src='../images/loading.gif' alt='Saving information' />");
			setTimeout(function(){
				$(".result").html("<span class='success'>All information in the form were kept &nbsp;&nbsp;&nbsp;Thanks.</span>");
			}, 1000);
		});

	});
</script>