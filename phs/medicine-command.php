<?php
session_start();
require_once "../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$error = "";
	
$active = "medicines";
require_once "../lib2/cssmenu/phs_header.html";
?>
<div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
		<h1 style='align: center; margin-top:-55px;'>Medicine Command List</h1>
			<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<span class=save_adds></span>
		<form action="./cmd/save-command.php" method="POST" class="form-horizontal" id="commandListPreparationForm">
			<!-- <input type="hidden" name="mdid" value="<?= $md['mdNameId'] ?>">
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Quantity</label>
				<div class="col-sm-8">
					<input type="text" name="quantity" class="form-control" placeholder="Requested Quantity" />
				</div>
			</div> -->
			<!-- <div class="pull-right btn-group">
				<button type="button" id="receiveMedicines" class="btn btn-primary"><i class="fa fa-arrow-down"></i> Print</button>
				<button type="button" id="addToCommandList" class="btn btn-success"><i class="fa fa-print"></i> Print</button>
			</div> -->
		</form>
	</div>
</div>

<?php
$cmd= formatResultSet($rslt=returnResultSet("SELECT a.MedecineNameID AS mdNameId,
													a.MedecineName AS mdName,
													a.form AS mdForm,
													b.MedecineCategoryName AS mdCatName,
													b.MedecineCategoryID AS mdCatId,
													d.quantity,
													d.id AS stockId
													FROM md_name AS a
													INNER JOIN md_category AS b
													ON a.MedecineCategorID = b.MedecineCategoryID
													INNER JOIN md_main_stock AS c
													ON a.MedecineNameID = c.mdNameId
													INNER JOIN md_main_stock_records AS d
													ON c.id = d.stockId
													WHERE d.operation = 'REQUEST' AND d.status=0
													ORDER BY mdCatName ASC, mdName ASC
													", $con), true, $con);
if(count($cmd) > 0){
	?>
	<div class="row">
		<div class="col-md-12">
			<div style="max-height: 570px; overflow: auto;">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Form</th>
							<th>Quantity</th>
							<th>Operation</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$lastCategory = "";
						$counter = 1;
						foreach($cmd AS $c){
							if($lastCategory != $c['mdCatId']){
								echo "<tr>";
									echo "<td colspan='5' class='text-center'>".$c['mdCatName']."</td>";
								echo "</tr>";
								$lastCategory = $c['mdCatId'];
							}
							echo "<tr>";
								echo "<td>".($counter++)."</td>";
								echo "<td>".$c['mdName']."</td>";
								echo "<td>".$c['mdForm']."</td>";
								echo "<td class='text-right'>".$c['quantity']."</td>";
								echo "<td><a title='Receive {$c['mdName']}' href='./cmd/receive-command.php?stockId={$c['stockId']}' rel='#overlay' ><i class='fa fa-arrow-down'></i></a></td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php
} else{
	echo "No Medicine on the Command List";
}
?>

    	<div style='height: 10px;'>&nbsp;</div>
	</div>
</div>
  <?php
  include_once "../footer.html";
  ?>
  	<div class="apple_overlay" id="overlay">
		<!-- the external content is loaded inside this tag -->
		<div class="contentWrap"></div>
	</div>
</body>
</html>
<script type="text/javascript">
	$('#addToCommandList').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_adds").html('');
		$(".save_adds").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#commandListPreparationForm").ajaxForm({ 
			target: '.save_adds'
		}).submit();
			
	});

	$(document).ready(function(){

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
	});
</script>