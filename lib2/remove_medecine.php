<?php
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		;//$uri = 'https://';
	} else {
		echo "<font color=red>Not Allowed to make those changes to this data</font>";
		die;
	}
	
	$str = sha1(md5(@$_POST['password']));
	//echo ];
	//echo $str."<br />";
	$str1 = strrev(substr($str,0,(strlen($str)/2)));
	$str1 .= strrev(substr($str,(strlen($str)/2)));
	$str2 = strrev($str1);
	//echo $str1."<br />";
	//echo $str2."<br />";
	$pwd = sha1($str2);
	//echo $str; die;
	
if(!@$_POST['checked']){
	?>
	<form action="" method="post">
		<input type=password name='password' placeholder="Enter Password to check" />
		<input type=submit name='checked' value='Check' />
	</form>
	<?php
	die;
} else if($pwd != "19fbb4b7c54d1de174d5715504ee0f12bc147fc0"){

	echo "<font color=red>Not Allowed to make those changes to this data</font>";
	die;
} //die;
require_once "../lib/db_function.php";
$md_name = "aas 100 mg";
$md_price = 40;
$amount = $md_price + ($md_price * 20/100);
$md_cate = 1;
$md_embalage = 1;
//get the medecine name id
$md_id = returnAllData("SELECT MedecineNameID FROM  md_name WHERE MedecineName='{$md_name}'",$con);
//returnSingleField(,$field="HORecordID",$data=true, $con);
//var_dump($md_id);
//save the new record with the correct information now
$new_md_id = saveAndReturnID("INSERT INTO md_name SET MedecineName='{$md_name}', MedecineCategorID='{$md_cate}', Emballage='{$md_embalage}', Status=1", $con);
//select all previous price to be delete
$price_ids = array();
$delete_query_1 = "DELETE FROM md_name WHERE";
for($i=0;$i<count($md_id);$i++){
	if($i > 0){
		$delete_query_1 .= " ||";
	}
	
	$delete_query_1 .= " `MedecineNameID`='{$md_id[$i]['MedecineNameID']}'";
	//select the price corresponding to this md_id
	$price_id = returnAllData("SELECT MedecinePriceID FROM  md_price WHERE MedecineNameID='{$md_id[$i]['MedecineNameID']}'",$con);
	//var_dump($price_id);echo "<br />";
	JoinArrays($price_ids, $price_id, $price_ids);

}
//echo "<pre>"; var_dump($price_ids);
//save new price id
$new_price_id = saveAndReturnID("INSERT INTO md_price SET MedecineNameID='{$new_md_id}', BuyingPrice='{$md_price}', Amount='{$amount}', Date='2015-12-01', Status=1", $con);

//update all md_records with the old data by new ones
$update_condition = "UPDATE md_records SET MedecinePriceID='{$new_price_id}' WHERE"; $cnd_count = 0;
$delete_condition = "DELETE FROM md_price WHERE";
foreach($price_ids as $price){
	if($cnd_count++ > 0){
		$update_condition .= " ||";
		$delete_condition .= " ||";
	}
	$update_condition .= " `MedecinePriceID`='{$price['MedecinePriceID']}'";
	$delete_condition .= " `MedecinePriceID`='{$price['MedecinePriceID']}'";
}
//echo $delete_query_1;
saveData($update_condition, $con);
saveData($delete_condition, $con);
saveData($delete_query_1, $con);

?>
<font color='green'>Thanks!</font>