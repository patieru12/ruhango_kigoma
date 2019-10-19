<?php
session_start();
require_once "../lib/db_function.php";
if("lab" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$diag = returnAllData("SELECT id AS RegisterID, name AS registerName FROM la_register ORDER BY RegisterID ASC",$con);
$printed = ""; $i=0;
//var_dump($diag);
if($diag){
	
	//echo "<div style='background-color:#4f4a41; color:#eee; font-weight:bold; padding:2px; border-top-left-radius:5px; border-top-right-radius:5px; margin-top:10px; text-align:center'>Exams</div><div style='border:1px solid #4f4a41; padding-bottom:5px; border-bottom-left-radius:5px; border-bottom-right-radius:5px;'>";
	foreach($diag as $d){
		if($d['RegisterID'] == 1){
			?>
			<label><input type=checkbox name='general_register' class='all_data' onclick='if($("#i<?= $d['RegisterID'] ?>").prop("checked")){ checkAll(); } else { uncheckAll();  }' id='i<?= $d['RegisterID'] ?>' value='<?= $d["RegisterID"] ?>'><?= $d['registerName']; ?></label><br />
			<?php
		} else {
			?>
			<label><input type=checkbox class='all_data' onclick='if($("#i<?= $d['RegisterID'] ?>").prop("checked")){ checkOneRegister("<?= $d['RegisterID'] ?>"); } else { uncheckOneRegister("<?= $d['RegisterID'] ?>");  }' id='i<?= $d['RegisterID'] ?>' value='<?= $d["RegisterID"] ?>'><?= $d['registerName']; ?></label><br />
			<?php
		}
	}
}
?>