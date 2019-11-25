<?php
session_start();
require_once "../../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$error = "";
if(preg_match("/\d/", $_GET['patientID'])){
	$patientID = $_GET['patientID'];
	$patient = formatResultSet($rslt=returnResultSet("SELECT 	a.*,
																b.CategoryID AS insuranceCategory,
																c.Name AS patientName,
																c.DateofBirth AS DateofBirth,
																b.InsuranceName AS InsuranceName,
																d.TypeofPayment AS TypeofPayment,
																d.ValuePaid AS ValuePaid,
																c.phoneNumber AS phoneNumber
																FROM pa_records AS a
																INNER JOIN in_name AS b
																ON a.InsuranceNameID = b.InsuranceNameID
																INNER JOIN pa_info AS c
																ON a.PatientID = c.PatientID
																INNER JOIN in_price AS d
																ON b.InsuranceNameID = d.InsuranceNameID
																INNER JOIN co_records AS e
																ON a.PatientRecordID = e.PatientRecordID
																WHERE e.ConsultationRecordID ='{$patientID}'
																", $con), false, $con);

	$examRecords = formatResultSet($rslt=returnResultSet($sql = "SELECT 	a.ExamPriceID AS priceID,
																			c.ExamName AS examName,
																			a.ExamNumber AS examNumber,
																			a.ResultNumber AS resultNumber,
																			a.ExamRecordID AS ExamRecordID,
																			e.resultName AS resultName,
																			a.status AS status,
																			a.MonthlyID AS MonthlyID,
																			a.sampleTaken AS sampleTaken
																			FROM la_records AS a
																			INNER JOIN la_price AS b
																			ON a.ExamPriceID = b.ExamPriceID
																			INNER JOIN la_exam AS c
																			ON b.ExamID = c.ExamID
																			INNER JOIN co_records AS d
																			ON a.ConsultationRecordID = d.ConsultationRecordID
																			LEFT JOIN (
																				SELECT 	a.ExamRecordID,
																						GROUP_CONCAT(b.ResultName SEPARATOR ';' ) AS resultName
																						FROM la_result_record AS a
																						INNER JOIN la_result AS b
																						ON a.ResultID = b.ResultID
																						GROUP BY ExamRecordID
																			) AS e 
																			ON a.ExamRecordID = e.ExamRecordID
																			WHERE a.ConsultationRecordID={$patientID}
																			", $con), true, $con);
	// var_dump($examRecords);
	// echo strtolower();
	if(is_array($examRecords)){
		$tbData= "";
		foreach($examRecords AS $e){
			$tbData .= "<tr>";
				$tbData .= "<td>".(is_null($e['MonthlyID'])?"<input type='checkbox' name='laSample_{$e['ExamRecordID']}' title='' checked />":"&nbsp;")." </td>";
				$tbData .= "<td>{$e['MonthlyID']} </td>";
				$tbData .= "<td>{$e['examName']}</td>";
				$tbData .= "<td>{$e['resultName']}</td>";
				$tbData .= "<td>
								".(!is_null($e['sampleTaken'])?"<a style='color:blue; text-decoration:none;' onclick='".($e['status']==0 && strtolower($patient['InsuranceName']) == "private" && 0?"alert(\"This is not Paid.\");":"AllowToAddResult({$e['ExamRecordID']});")."return false;' href='?{$e['ExamRecordID']}' title='Add Result on the Exam'><img src='../images/edit.png' /></a>":"")."
								".(is_null($e['MonthlyID'])?"&nbsp;<a href='?{$e['ExamRecordID']}' onclick='return false;' title='This is exam is not examined'><img src='../images/delete.png'></a></a>":"")."
							</td>";
			$tbData .= "</tr>";
		}
		$tbData .= "
			<tr>
				<td colspan='5'>
					<label><input type='checkbox' name='printSampleTicket' onclick='saveSelectedSample(); return false;'>Print Taken Sample</a></label><div id='printLink' style='color:#000;'>Print</div>
				</td>
			</tr>";
		echo $tbData;
	} else{
		echo "<tr><td colspan=4><span class='error-text'>No Exam is Prescribed!</span></td></tr>";
	}
}
?>

<style type="text/css">
	#printLink{
		color:#fff;

	}

	#printLink a{
		color: #fff;
	}
</style>

<script type="text/javascript">
	/*$("#requestExamID").keydown(function(e){
		return e.preventDefault();
	});*/
	function saveSelectedSample(){
		var formContent = $("#printSelectedSamples").serialize();

		$.ajax({
			type: "POST",
			url: "./pa/printSelectedSamples.php",
			data: formContent + "&url=ajax",
			cache: false,
			success: function(result){
				$("#printLink").html(result);
				setTimeout(function(e){
					$("#downloadSample")[0].click();
				}, 50);
				
			},
			error: function(err){
				alert("An error occured while requesting Result Saver Service\nPlease Try Again Later");
				console.log(err.responseText);
			}
		});
	}
	function AllowToAddResult(recordID){
		// The Result Here
		var url = "./add-exam-form.php?recordID=" + recordID;
		$.getJSON( url, function(data) {
			// console.log(data);
			$("#requestRecordID").val(recordID);
			$("#requestExamID").val(data.id);
			$("#requestExamName").val(data.examName);
			$("#requestExamResult").val(data.resultName);
		}).done(function(){
			console.log("Requested Completed Succesfully!");
			// if(activateAutoFill){
			$("#requestExamResult").autocomplete("./auto/results.php", {
				selectFirst: true
			}, "labo");
			// }
		}).fail(function(error){
			console.log(error);
		}).always(function(){
		});
	}
</script>