<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
echo "<script>window.location='../logout.php';</script>";
return;
}
if (!isset($_POST['ExamID']) OR !isset($_POST['ResultDate']) ) {
	echo "<script>window.location='./laboratory.php';</script>";
	return;
}

function age($date='')
{
	if ($date != "") {
		$ex = explode('-', $date);
		$year = $ex[0];
		$now = date("Y",time());
		return ($now - $year );
	}
	else return "";
}

//Get all price with ExamId 
$examId = $_POST['ExamID'];
$date = $_POST['ResultDate'];
$sql = "SELECT * FROM `la_price` WHERE `ExamID` = '{$examId}'";
$query = mysql_query($sql);
$ExamRecord = array('id'=>array(),'co'=>array());
$resf = 0;
$pid_ = array();
$sql_ = array();
$f = 1;

while($get = mysql_fetch_array($query)){
	$pid[$f] = $get['ExamPriceID'];
	$sql_[] = "SELECT ExamRecordID,ConsultationRecordID FROM la_records WHERE ExamPriceID='".$pid[$f]."' AND ResultDate = '{$date}'  ";	
	$f++;
}

$countsql = count($sql_);
$endPoint = $countsql - 1;
$startPoint = 0;
while ($startPoint <= $endPoint) {
	$_query = mysql_query($sql_[$startPoint]);
	if ($_query) {
		while ($get = mysql_fetch_array($_query)) {
			$ExamRecord['id'][] = $get['ExamRecordID'];
			$ExamRecord['co'][] = $get['ConsultationRecordID'];
		}
	}	
	$startPoint++;
}
?>

	<script>
		$("#filter").click(function(e){
			$(".patient_found").load("patient_list.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + prompt("Enter Filter Key",'<?= @$_GET['filter'] ?>').replace(/ /g,"%20"));
			return e.preventDefault();
		});
		$("#filter_remove").click(function(e){
			$("#filter_").val("");
			$(".patient_found").load("patient_list.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
			return e.preventDefault();
		});
	</script>
	<style>
		.td-r{
			text-align:center;
			cursor:pointer;
			color:green;
		}
		.td-r:hover{
			color:blue;
			text-decoration:underline;
		}
		.table tr:hover{
			background-color:#efefef;
			cursor:pointer;
		}
	</style>
	<div id='assigned_exam'></div>
	
<div style='max-height:430px;' id='note' >
	<span>Result Founded << <label style='font-weight: bold;'><?= count($ExamRecord['id']) ?></label> >> on Exam-code:<label style='font-weight: bold;'><?php echo $exam_name = returnSingleField("SELECT * FROM `la_exam` WHERE `ExamID` = '{$examId}' ",'ExamName'); ?></label> Date:<label style='font-weight: bold;'><?= $date ?></label></span>
	<span style='float:right;'><?= @$_GET['filter']?"<a href='#' id=filter_remove style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' ><img src='../images/filter_remove.png' /> Remove Filter</a>":"" ?><a href='#' id=filter style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' > <img src='../images/filter.png' /> Filter </a></span>
</div>

<div style='margin-top: 10px; max-height:400px; overflow:auto;'>

<table class='table tables' border='1' style='width: 100%;text-align: center;' >
	<thead style='font-weight: bold;'>
		<th>#</th>
		<th>DocID</th>
		<th>Name</th>
		<th>Age</th>
		<th>Sex</th>
		<th>District</th>
		<th>Sector</th>
		<th>Cell</th>
		<th>Village</th>
		<th class='th-r'>&nbsp;</th>
	</thead>
	<tbody>
		<?php
		/* $resultHolder = array();
		$endPoint = count($ExamRecord['id']) - 1;
		$startPoint = 0;
		$s = 0;
		while ($startPoint <= $endPoint) {
			$id_ex = $ExamRecord['id'][$startPoint];
			$id_co = $ExamRecord['co'][$startPoint];
 */
			$sql = "SELECT pa_records.DocID, pa_records.patientRecordID, pa_info.*,ad_village.VillageName,ad_cell.CellName,ad_sector.SectorName,ad_district.DistrictName FROM `pa_records`,`pa_info`,`ad_village`,`ad_cell`,`ad_sector`,`ad_district` WHERE pa_info.PatientID = pa_records.PatientID AND ad_village.ViillageID = pa_info.VillageID AND ad_cell.CellID = ad_village.CellID AND ad_sector.SectorID = ad_cell.SectorID AND ad_district.DistrictID = ad_sector.DistrictID AND pa_records.datein='{$date}' ORDER BY pa_info.name ASC ";	
			$query = mysql_query($sql)or die (mysql_error());
			if ($query) {
				$s=0;
				while ($get = mysql_fetch_assoc($query)) {
				?>
					<tr class='tr-p' >
						<td><?= ++$s; ?></td>
						<td><?= $get['DocID'] ?></td>
						<td><?= $get['Name'] ?></td>
						<td><?= age($get['DateofBirth']) ?></td>
						<td><?= $get['Sex'] ?></td>
						<td><?= $get['DistrictName'] ?></td>
						<td><?= $get['SectorName'] ?></td>
						<td><?= $get['CellName'] ?></td>
						<td><?= $get['VillageName'] ?></td>
						<td class='td-r' onclick='if(confirm("Assign <?= $exam_name ?> To <?= $get['Name'] ?>")){ AddExam("<?= $get['patientRecordID']?>","<?= $date?>","<?=$examId ?>"); }'>Add</td>
					</tr>
				<?php
				}
			}
		/* 	$startPoint++;
		} */
		?>
	</tbody>
</table>
</div>
<style type="text/css">
	.table{
		table-layout: inherit;
	}
	.tr-p td {
		padding: 5px;
	}
	.td-r,.th-r{
		text-align: left;
		padding-left: 10px;
	}
}
</style>
<script>
function AddExam(patient,date,examid)
{
$.post(
"create_sample.php",
{
patientRecordID:patient,
ResultDate:date,
ExamID:examid
},
function (result){
	$("#assigned_exam").html(result);
}
);}
</script>