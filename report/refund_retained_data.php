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
// var_dump($_GET);
$date = $_GET['year']."-".$_GET['month'];
$sp_condition = "";
if(@$_GET['filter']){
	$sp_condition .= " && (
		pa_records.InsuranceCardID LIKE('%{$_GET['filter']}%') ||
		pa_info.Name LIKE('%{$_GET['filter']}%') ||
		pa_info.FamilyCode LIKE('%{$_GET['filter']}%')
	)";
}
//echo $sys;
if(strlen($_GET['key'])){
	$patients = formatResultSet($rslt=returnResultSet($sql="SELECT 	a.DocID AS DocID,
																	b.Name AS Name,
																	a.DateIn AS Date,
																	a.PatientRecordID,
																	c.Amount,
																	c.id,
																	c.tbName
																	FROM pa_records AS a
																	INNER JOIN pa_info AS b
																	ON a.PatientID = b.PatientID
																	INNER JOIN (
																		SELECT 	a.PatientRecordID,
																				a.Amount AS Amount,
																				a.id,
																				'rpt_cbhi' AS tbName
																				FROM rpt_cbhi AS a
																				WHERE a.Date LIKE ('{$date}%')
																				AND a.itemName='Retained'
																				GROUP BY a.PatientRecordID
																		UNION
																			SELECT 	a.PatientRecordID,
																					a.Amount AS Amount,
																					a.id,
																					'rpt_mmi' AS tbName
																					FROM rpt_mmi AS a
																					WHERE a.Date LIKE ('{$date}%')
																					AND a.itemName='Retained'
																					GROUP BY a.PatientRecordID
																		UNION
																			SELECT 	a.PatientRecordID,
																					a.Amount AS Amount,
																					a.id,
																					'rpt_rssb_rama' AS tbName
																					FROM rpt_rssb_rama AS a
																					WHERE a.Date LIKE ('{$date}%')
																					AND a.itemName='Retained'
																					GROUP BY a.PatientRecordID
																		UNION
																			SELECT 	a.PatientRecordID,
																					a.Amount AS Amount,
																					a.id,
																					'rpt_private' AS tbName
																					FROM rpt_private AS a
																					WHERE a.Date LIKE ('{$date}%')
																					AND a.itemName='Retained'
																					GROUP BY a.PatientRecordID
																	) AS c
																	ON a.PatientRecordID = c.PatientRecordID
																	WHERE a.DateIn LIKE ('{$date}%')
																	", $con),true, $con);
//echo $sql;
if($patients){
	?>
	<script>
		$("#filter").click(function(e){
			$(".patient_found").load("document_list_private.php?key=" + $("#insurance").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + prompt("Enter Filter Key",'<?= @$_GET['filter'] ?>').replace(/ /g,"%20"));
			return e.preventDefault();
		});
		$("#filter_remove").click(function(e){
			$("#filter_").val("");
			$(".patient_found").load("document_list_private.php?key=" + $("#insurance").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val());
			return e.preventDefault();
		});
		//$("#filter").focus();
		function payDebt(record_id){
			$.ajax({
				type: "POST",
				url: "./pay_debt.php",
				data: "record_id=" + record_id,
				cache: false,
				success: function(result){
					$(".doc_selected").html(result);
					$(".patient_found").load("debt_data.php?key=" + $("#insurance").val() + "&month=" + $("#month").val() + "&year=" + $("#year").val() + "&post=" + $("#post").val() + "&filter=" + $("#filter_").val().replace(/ /g,"%20"));
				}
			});
		}
	</script>
	<b class=visibl>
	<span class=success style='font-weight:bold; font-size:20px;'>
	Monthly Retained Summary<br />
	Post: <?= $post ?><br />
	Date: <?= $_GET['year']."/".$_GET['month'] ?><br />
	Number: <?= count($patients) ?> Records 
	</span>
	<style>
		table#vsbl td, table#vsbl th{font-size:11px; font-family:arial; font-weight:bold; border:1px solid #000;}
		.number_right{ text-align:right; }
	</style>
	<span class=styling></span>
	<?= @$_GET['filter']?"<script>$('#filter_').val('{$_GET['filter']}');</script><br /><span class=error-text>".count($patients)." Result".(count($patients)>1?"s":"")." found for ".$_GET['filter']."</span>":"" ?> <span style='float:right;'><?= @$_GET['filter']?"<a href='#' id=filter_remove style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' ><img src='../images/filter_remove.png' /> Remove Filter</a>":"" ?><a href='#' id=filter style=' color:blue; border:0px solid #eee; font-size:12px; text-decoration:none;' > <img src='../images/filter.png' /> Filter </a></span>
	<div style='height:73%; margin-top:2px; width:100%; border:0px solid #000; overflow:auto;'>
	<style type="text/css">
		.overdue{
			background: #f5e5e3
		}
	</style>
	<table class=list id=vsbl border="1" style='width:100%; font-size:30px;'>
		<tr><th>ID</th><th>Date</th><th>Reference</th><th>Name</th><th>Amount</th><th>Status</th></tr>
		<tbody>
		<?php
		$t = array();
		$kk_ = "";
		$data = array();
		// var_dump($patients); //die;
		$row_count = 0;
		for($i=0;$i<count($patients);$i++){
			$r = $patients[$i];
			
			$row_count++;
			$className = "not_paid";
			$kk_ .=  "<tr onclick='$(\".styling\").html(\"<style>#id{$i}{background-color:#e5e5e3;}</style>\");' id='id{$i}' class='{$className}'>
				<td>".($i+1)."</td>
				<td>".($r['Date'])."</td>
				<td>{$r["DocID"]}</td>
				<td>{$r["Name"]}</td>
				<td>{$r["Amount"]}</td>
				<td><a style='color:blue;' href='./pay_retained.php?record_id={$r['PatientRecordID']}&refund={$r['id']}&tbName={$r['tbName']}' rel='#overlay'>Refund</a></td>
				";
		}
		echo $kk_;
		//var_dump($data);
		?>
		</tbody>
	</table>
	</div>
	</b>
	<?php
	if(count($data)>0){
		$_SESSION['report'] = $data;
		$_SESSION['header'] = array(
									array("PROVINCE / MVK"=>"SUD"),
									array("ADMINISTRATIVE DISTRICT"=>"HUYE", "Period"=>$_GET['year']."/".$_GET['month']),
									array("ADMINISTRATIVE SECTION"=>"HUYE"),
									array("HEALTH FACILITY"=>"BUSORO HEALTH CENTER"),
									array("CODE / HEALTH FACILITY"=>"40211013"),
								);
		$_SESSION['report_title'] = array("title"=>"S U M M A R Y  OF CORAR  P R E S T A T I O N");
		?>
		<style>
			.img_links{
				height:40px; 
				width:40px; 
				cursor:pointer;
			}
			.img_links:hover{
				/* height:37px;  */
				border-bottom:3px solid red;
			}
		</style>
		<a href='./print_report.php?format=pdf' onclick='alert("Sorry Not Availabel"); return false;' target="_blank"><img title='View in PDF' src="../images/b_pdfdoc.png" class=img_links width=30px /></a>
		<a href='./print_report_pres.php?format=excel&in=cbhi' target="_blank"><img title="View in EXCEL" src="../images/excel.png" class=img_links width=40px /></a>
		<?php
	}
	
} else{
	echo "<span class=error-text>No Patient in the selected month {$_GET['year']}-{$_GET['month']} at selected station {$post}</span>";
}
}
?>
<script>
	$(document).ready(function() { 
		$("a[rel]").overlay({
	        mask: '#206095',
	        effect: 'apple',
	        onBeforeLoad: function() {

	            // grab wrapper element inside content
	            var wrap = this.getOverlay().find(".contentWrap");
				
	            // load the page specified in the trigger
	            wrap.load(this.getTrigger().attr("href"));
	        }

	    });
	    
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