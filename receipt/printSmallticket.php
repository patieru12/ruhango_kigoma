<?php
$stringContent = "<span style='font-family:arial; font-size:10px; border:0px solid green;'>******".(date("Y-m-g H:i:s", time()))."*******<br />Ruberandinda Patience<br />Code: <br />-----------------------------------------------<br />My Data are here<hr />Again test<hr />If success by a bottle<hr />Thanks.....</span>";
// require_once "../lib/mpdf57/mpdf.php";

$pdf = new mPDF("","A8",0,'',6,2,2);

$pdf->Open();

$pdf->AddPage("P");

$pdf->SetFont("Arial","N",10);
// $pdf->SetMargins($left=15,$right=15,$top=15) ;


$pdf->WriteHTML($stringContent);
$filename = "./data.pdf";
//echo $filename;
$pdf->Output(); 
exit;
session_start();
require_once "./../lib/db_function.php";
// var_dump($_POST);
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$data = str_replace(":", ", ", $_GET['data']); //$_GET['data'];
// echo $data;
$sql = "SELECT 	a.ExamRecordID,
				DATE_FORMAT(FROM_UNIXTIME(a.sampleTaken), '%Y-%m-%d %H:%i:%s') AS takenAt,
				c.ExamName AS ExamName
				FROM la_records AS a
				INNER JOIN la_price AS b
				ON a.ExamPriceID = b.ExamPriceID
				INNER JOIN la_exam AS c
				ON b.ExamID = c.ExamID
				WHERE a.ExamRecordID IN({$data})
				";
				// echo $sql;
$records = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
?>
Date: <b><?= $records[0]['takenAt'] ?></b><br />
<table border='1'>
	<tr>
		<td style="text-align: center;">Id</td>
		<td style="text-align: center;">Exam</td>
		<td style="text-align: center;">Result</td>
	</tr>
	<?php
	for($i=0; $i<count($records); $i++){
		echo "<tr>
			<td>".($i+1).".</td>
			<td>".substr($records[$i]['ExamName'], 0, 27)."</td>
			<td style='width:200px;'>&nbsp;</td>
			</tr>";
	}
	?>
</table>
<style type="text/css">
	table{
		border-collapse: collapse;
	}
	table td{
		padding-right: 10px;
		padding-top:4px;
		padding-bottom:4px;
	}
</style>
<script type="text/javascript">
	setTimeout(function(e){
		window.print();
	}, 500);
</script>