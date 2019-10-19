<?php
session_start();
//var_dump($_SESSION);
require_once "../../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
echo "<script>window.location='../logout.php';</script>";
return;
}
//var_dump($_GET);
$_POST['ExamID'] = $_GET['exam'];
$_POST['ResultDate'] = $_GET['date'];
if (!isset($_POST['ExamID']) OR !isset($_POST['ResultDate']) ) {
	echo "<span class=error-text>Invalid Exam</span>";
	return;
}
//var_dump($_POST); die;
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

//map the selectable days to allow to make some necessary maps now
$start_day = "01";

$month = explode("-",$_POST['ResultDate'])[1];
$year = explode("-",$_POST['ResultDate'])[0];

$start_time = mktime(0,0,0,$month,1,$year);
$step = (60*60*24);
$valid_dates = array();

for($start_time; preg_match("/^{$year}-{$month}$/",date("Y-m",$start_time)); $start_time += $step){
	//$str .= "<label onclick='$(\".patient_found\").load(\"coartem_data.php?year=\" + $(\"#year\").val() + \"&month=\" + $(\"#month\").val() + \"&post=\" + $(\"#post\").val() + \"&day=".date("d",$start_time)."\");' class='flatbtn-blu".($_GET['day'] == date("d",$start_time)?" link_active":"")."' style='padding:0 10px;'>".date("d",$start_time)."</label>";
	
	$valid_dates[] = date("Y-m-d",$start_time);
}
//var_dump($_POST);
//select the exam identification
$exam = formatResultSet($rslt=returnResultSet("SELECT la_exam.ExamName, la_exam.ExamID FROM la_exam WHERE la_exam.ExamID='".(PDB($_POST['ExamID'],true,$con))."'",$con),$multirows=false,$con);
$insu = formatResultSet($rslt=returnResultSet("SELECT in_category.* FROM in_category",$con),$multirows=true,$con);
/* echo "Report: ".$exam['ExamName'];
echo "<br />Month: ".$_POST['ResultDate']; */
/* ?>
<table border=1 cellspacing=0>
	<tr><th rowspan=2>Date</th><th colspan=2>Positive</th><th colspan=2>Negative</th><th rowspan=2>Total</th></tr>
	<tr><th>Total</th><th>%</th><th>Total</th><th>%</th></tr>
	<?php */
	//loop all valid date now
	$total_positive = 0;
	$total_negative = 0; 
	foreach($valid_dates as $valid_date){
		/* echo "<tr>";
		echo "<td>{$valid_date}</td>"; */
		//select valid tariff data that are based when determining exam to save
		$price = array();
		if($insu){
			foreach($insu as $in){
				$price_data = formatResultSet($rslt=returnResultSet($d = "SELECT la_price.ExamPriceID FROM la_price WHERE la_price.ExamID='".(PDB($_POST['ExamID'],true,$con))."' && Date <= '{$valid_date}' && InsuranceTypeID='{$in['InsuranceCategoryID']}' ORDER BY Date DESC Limit 0, 1",$con),$multirows=false,$con);
				JoinArrays($price, $price_data, $price);
				//echo "all";
			}
		} else{
			//echo "<br /><span class=error-text>Some Parameter Are Missing!</span>";
			return;
		}
		//echo $d;
		//var_dump($price); die;
		//make the search query
		$cnd = "";
		if($price){
			$cnd = "("; $p_counter = 0;
			foreach($price as $p){
				//echo $p;
				if($p_counter++ > 0)
					$cnd .= " || ";
				$cnd .= "`la_records`.`ExamPriceID` = '{$p}'";
			}
			$cnd .= ")";
		} else{
			//echo "<br /><span class=error-text>Some Parameter Are Missing!</span>";
			return;
		}
		//echo $cnd."<br />";
		//die;
		//recherché le nombre des positive pour cette jour
		$all = "SELECT * FROM la_records WHERE ResultDate='{$valid_date}' && {$cnd}";
		//echo "<br />".$all."<br />";
		$total_data = formatResultSet($rslt=returnResultSet($all,$con),$multirows=true,$con);
		$total= count($total_data);
		
		//select positive result for the current exam
		$positive = 0; $negative = 0;
		if($total_data){
			foreach($total_data as $t_data){
				$p = "SELECT `la_result_record`.`ExamRecordID` FROM `la_result_record`, `la_result`, `la_records` WHERE `la_records`.`ExamRecordID`='{$t_data['ExamRecordID']}' && `la_result_record`.`ExamRecordID` = `la_records`.`ExamRecordID` && `la_result`.`ResultID` = `la_result_record`.`ResultID` && (`la_result`.`ResultName` != 'Negative' || `la_result`.`ResultName` != 'Negatif')";
				//echo $p."<br />";
				$positive_data = formatResultSet($rslt=returnResultSet($p,$con),$multirows=true,$con);
				
				$positive += count($positive_data);
				$neg = "SELECT `la_result_record`.`ExamRecordID` FROM `la_result_record`, `la_result`, `la_records` WHERE `la_records`.`ExamRecordID`='{$t_data['ExamRecordID']}' && `la_result_record`.`ExamRecordID` = `la_records`.`ExamRecordID` && `la_result`.`ResultID` = `la_result_record`.`ResultID` && (`la_result`.`ResultName` = 'Negative' || `la_result`.`ResultName` = 'Negatif')";
				//echo $p."<br />";
				$negative_data = formatResultSet($rslt=returnResultSet($neg,$con),$multirows=true,$con);
				
				$negative += count($negative_data);
				
			}
		}
		
		$total = $positive + $negative;
		$total_negative += $negative;
		$total_positive += $positive;
		$positive_percentage = $total>0?round(($positive * 100)/$total,1):0;
		$negative_percentage = $total>0?round(($negative * 100)/$total,1):0;
		/* echo "<td style='text-align:right; width:60px;'>".($positive?$positive:"")."</td>";
		echo "<td style='text-align:right; width:60px;'>".($positive_percentage?$positive_percentage." %":"")."</td>";
		echo "<td style='text-align:right; width:60px;'>".($negative?$negative:"")."</td>";
		echo "<td style='text-align:right; width:60px;'>".($negative_percentage?$negative_percentage." %":"")."</td>";
		echo "<td style='text-align:right; width:60px;'>".($total?$total:"")."</td>";
		echo "</tr>"; */
	}
	$all_total = $total_positive + $total_negative;
	$total_positive_percentage = $all_total?round(($total_positive * 100)/$all_total,1, PHP_ROUND_HALF_UP):0;
	$total_negative_percentage = $all_total?round(($total_negative * 100)/$all_total,1, PHP_ROUND_HALF_UP):0;
	/* ?>
	<tr>
		<td>Total</td>
		<th><?= $total_positive ?></th>
		<th><?= $total_positive_percentage ?> %</th>
		<th><?= $total_negative ?></th>
		<th><?= $total_negative_percentage ?> %</th>
		<th><?= $all_total ?></th>
	</tr>
</table>


<?php */
//var_dump($_POST);
$rows = array(array("Positive",$total_positive),array("Negative",$total_negative));
/*$con = mysql_connect("localhost","root","");
 
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db("demo", $con);

$result = mysql_query("SELECT name, val FROM web_marketing");

$rows = array();
while($r = mysql_fetch_array($result)) {
	$row[0] = $r[0];
	$row[1] = $r[1];
	array_push($rows,$row);
} */
//var_dump($rows);
print json_encode($rows, JSON_NUMERIC_CHECK);

mysql_close($con);
?> 
