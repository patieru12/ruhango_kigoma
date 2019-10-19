<?php
//var_dump($_GET);
require_once "../../lib/db_function.php";
//check the price for medicine
$records = formatResultSet($rslt=returnResultSet($sql="SELECT cn_price.Amount FROM cn_price, cn_name WHERE cn_name.MedecineNameID = cn_price.MedecineNameID && cn_name.MedecineName = '{$_GET['medecinename']}' && cn_price.Date <= '{$_GET['medecinedate']}' ORDER BY cn_price.Date DESC LIMIT 0,1",$con),$multirows=false,$con);
//echo $sql;
//var_dump($records)

if($records){
	$o = false;
	$md = ommitStringPart($str=$_GET['medecinename'],7,$o);
	if(preg_match("/coartem/",strtolower($_GET['medecinename'])) || preg_match("/quinine/",strtolower($_GET['medecinename'])) || preg_match("/artesunate/",strtolower($_GET['medecinename']))){
		?>
		<script>
			$("#lock_malaria_anti").val("1");
		</script>
		<?php
	}
	echo $md.":<span class=success>".($records['Amount']*$_GET['medecinequantity'])."</span>";
}
?>
<br />