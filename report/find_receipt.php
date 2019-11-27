<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
//var_dump($_GET);
if(!@$_GET['key']){
	
	echo "<span class=error-text>Select Insurance</span>";
	return;
}
$select = "";
$post = "";
$posts = explode("_", $_GET['post']);
//var_dump($posts);
$count = count($post);
$current = 1;
$sys = "("; $sys_s = 0;
$ok = false;
foreach($posts as $pst){
	$ps = returnSingleField($sql="SELECT CenterName FROM sy_center WHERE CenterID='{$pst}'",$field="CenterName",$data=true, $con);
	//var_dump($ps);
	if($ps != null){
		$ok = true;
		if($post && $current++ == $count)
			$post .= " And ";
		else
			$post .= " ";
		$post .= $ps;
		if($sys_s++ > 0)
			$sys .= " || ";
		$sys .= "sy_center.CenterName = '{$ps}'";
	}
}
$sys .= ")";
if(!$ok){
	echo "<span class=error>No Post Selected</span>";
	return;
}
//var_dump($_GET);
$sp_condition = "";
if(@$_GET['filter']){
	$sp_condition .= " AND a.pdfData LIKE('%{$_GET['filter']}%')";
}
//echo $sys;
if(strlen($_GET['key'])){
	//select all possible information on the comming id
	$startTime = strtotime("{$_GET['year']}-{$_GET['month']}-{$_GET['day']} 00:00:00");
	$endTime = strtotime("{$_GET['year']}-{$_GET['month']}-{$_GET['day']} 23:59:59");

	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.pdfData,
																	a.id
																	FROM sy_print_command AS a
																	WHERE a.submittedOn >= {$startTime}
																	AND a.submittedOn <= '{$endTime}'
																	{$sp_condition}
																	",$con),$multirows=true,$con);
// echo $sql; die();
if($patients){
	?>
	<script>
		$("#filter").click(function(e){
			$(".patient_found").load("find_receipt.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + prompt("Enter Filter Key",'<?= @$_GET['filter'] ?>').replace(/ /g,"%20"));
			return e.preventDefault();
		});
		$("#filter_remove").click(function(e){
			$("#filter_").val("");
			$(".patient_found").load("find_receipt.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
			return e.preventDefault();
		});
		//$("#filter").focus();
		function deleteProfileNow(record_id){
			$.ajax({
				type: "POST",
				url: "./delete_patient_file.php",
				data: "record_id=" + record_id,
				cache: false,
				success: function(result){
					$(".doc_selected").html(result);
					$(".patient_found").load("document_list_cbhi.php?key=" + $("#insurance").val() + "&day=" + $("#day").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
				}
			});
		}
	</script>
	<b class=visibl>
	<span class=success style='font-weight:bold; font-size:20px;'>
	Printed Receipt<br />
	Post: <?= $post ?><br />
	Date: <?= $_GET['day']."/".$_GET['month']."/".$_GET['year'] ?><br />
	Number: <?= count($patients) ?> Receipt<?= count($patients)>1?"s":"" ?> 
	</span>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
		.number_right{ text-align:right; }
	</style>
	<span class=styling></span>
	<?= @$_GET['filter']?"<script>$('#filter_').val('{$_GET['filter']}');</script><br /><span class=error-text>".count($patients)." Result".(count($patients)>1?"s":"")." found for ".$_GET['filter']."</span>":"" ?> <span style='float:right;'><?= @$_GET['filter']?"<a href='#' id=filter_remove style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' ><img src='../images/filter_remove.png' /> Remove Filter</a>":"" ?><a href='#' id=filter style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' > <img src='../images/filter.png' /> Filter </a></span>
	<div style='max-height:350px; margin-top:2px; width:100%; border:0px solid #000; overflow:auto;'>
		<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
			
			<?php
			$columns = 6;
			$isOpened = false;
			for($i=0; $i < count($patients); $i++){
				$receipt = $patients[$i];
				if($i % $columns == 0){
					if($i > 0){
						$isOpened = false;
						?>
						</tr>
						<?php
					}
					$isOpened = true;
					?>
					</tr>
					<?php
				}
				?>
				<td>
					<?= $receipt['pdfData'] ?>
					<br /><a href="#" style="color: blue; text-decoration: none;" onclick="window.open('../app/print_cmgd.php?process_id=2018200001&commandId=<?= $receipt['id'] ?>', '_blank', 'location=yes,height=360,width=500,scrollbars=yes,status=yes');">Reprint</a>
				</td>
				<?php

			}
			if($isOpened){
				?>
				</tr>
				<?php
			}
			?>
		</table>
	</div>
	</b>
	<?php
	
} else{
	echo "<span class=error-text>No Patient in the selected month {$_GET['day']}/{$_GET['month']}/{$_GET['year']} at selected station {$post}</span>";
}
}
?>
<script>
	$(document).ready(function() { 
		$('#excel_file').live('change', function(){ 
			
			$("#upload_out").html('');
			$("#upload_out").html('<img src="../images/loading.gif" alt="Uploadding"/>'); 
			$("#upload_patient").ajaxForm({ 
				target: '#upload_out'
			}).submit(); 
		}); 
	});
</script>
<div id=upload_out></div>