<?php
session_start(); 
//var_dump($_SESSION); die;

require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($field="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(!@$_GET['key'])
	return;
$field = "";
$tbl = "";
$condition = "";
$tr = "";
$commas = false;
$width = 0; $v=0; $c=0; $s=0; $d=0;
switch($_GET['level']){
	case 4:
		$field .= "ad_village.VillageName";
		$tbl .= "ad_village";
		$tr .= "<th>Village</th>";
		$condition .= "ad_village.VillageName LIKE('%{$_GET['key']}%')";
		$commas = true;
		$width += 25;
		$v = 1;
	case 3:
		if($commas){
			$field .= ", ";
			$tbl .= ", ";
			$condition .= " && ad_village.CellID = ad_cell.CellID";
		} else{
			$commas = true;
			$condition .= "ad_cell.CellName LIKE('%{$_GET['key']}%')";
		}
		$field .= "ad_cell.CellName";
		$tbl .= "ad_cell";
		$tr .= "<th>Cell</th>";
		$width += 25;
		$c = 1;
	case 2:
		if($commas){
			$field .= ", ";
			$tbl .= ", ";
			$condition .= " && ad_cell.SectorID = ad_sector.SectorID";
		} else{
			$commas = true;
			$condition .= "ad_sector.SectorName LIKE('%{$_GET['key']}%')";
		}
		$field .= "ad_sector.SectorName";
		$tbl .= "ad_sector";
		$tr .= "<th>Sector</th>";
		$width += 25;
		$s = 1;
	case 1:
		if($commas){
			$field .= ", ";
			$tbl .= ", ";
			$condition .= " && ad_sector.DistrictID = ad_district.DistrictID";
		} else{
			$commas = true;
			$condition .= "ad_district.DistrictName LIKE('%{$_GET['key']}%')";
		}
		$field .= "ad_district.DistrictName";
		$tbl .= "ad_district";
		$tr .= "<th>District</th>";
		$width += 25;
		$d = 1;
		
}

$sql = "SELECT ".$field. " FROM ".$tbl." WHERE " . $condition."";
	
//echo $sql;
$found = returnAllData($sql,$con);
//var_dump($found);
?>
<table class=list style='width:<?= $width ?>%' onclick='$(".address_search").html("");' >
	<tr><th>ID</th><?= $tr ?></tr>
	<?php
	$i = 0;
	if($found){
	foreach($found as $f){
		$script = "";
		if($d)
			$script .= <<<DD
				$("#district").val("{$f["DistrictName"]}");
DD;
		if($s)
			$script .= <<<DD
			$("#sector").val("{$f["SectorName"]}");
DD;
		if($c)
			$script .= <<<DD
			$("#cell").val("{$f["CellName"]}");
DD;
		if($v)
			$script .= <<<DD
			$("#village").val("{$f["VillageName"]}");
DD;

		echo "<tr id='id{$i}' onclick='{$script}'><td>".(++$i)."</td>";
		foreach($f as $d)
			echo "<td>{$d}</td>";
		echo "</tr>";
	}
	}
	?>
</table>