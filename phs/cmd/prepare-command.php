<?php
session_start();
require_once "../../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_GET);
$mdid = PDB($_GET['mdid'], true, $con);
// Get the Medicine Identification now
$md = formatResultSet($rslt=returnResultSet("SELECT a.MedecineNameID AS mdNameId,
													a.MedecineName AS mdName,
													a.form AS mdForm,
													b.MedecineCategoryName AS mdCatName,
													b.MedecineCategoryID AS mdCatId
													FROM md_name AS a
													INNER JOIN md_category AS b
													ON a.MedecineCategorID = b.MedecineCategoryID
													WHERE a.MedecineNameID = '{$mdid}'
													", $con), false, $con);
?>
<div class="row">
	<div class="col-md-12">
		<div class="col-sm-6">
			Medicine Name: <strong><?= $md['mdName'] ?></strong>
		</div>
		<div class="col-sm-6">
			Medicine Form: <strong><?= $md['mdForm'] ?></strong>
		</div>
		<div class="col-sm-6">
			Medicine Category: <strong><?= $md['mdCatName'] ?></strong>
		</div>
	</div>
</div>
<h2>Add Medicine to Command List</h2>
<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<span class=save_adds></span>
		<form action="./cmd/save-command.php" method="POST" class="form-horizontal" id="commandListPreparationForm">
			<input type="hidden" name="mdid" value="<?= $md['mdNameId'] ?>">
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Quantity</label>
				<div class="col-sm-8">
					<input type="text" name="quantity" class="form-control" placeholder="Requested Quantity" />
				</div>
			</div>
			<div class="pull-right btn-group">
				<button type="button" id="addToCommandList" class="btn btn-primary"><i class="fa fa-check"></i> Add</button>
			</div>
		</form>
	</div>
</div>

<?php
$cmd= formatResultSet($rslt=returnResultSet("SELECT a.MedecineNameID AS mdNameId,
													a.MedecineName AS mdName,
													a.form AS mdForm,
													b.MedecineCategoryName AS mdCatName,
													b.MedecineCategoryID AS mdCatId,
													d.quantity
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
			<div style="max-height: 170px; overflow: auto;">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Form</th>
							<th>Quantity</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$lastCategory = "";
						$counter = 1;
						foreach($cmd AS $c){
							if($lastCategory != $c['mdCatId']){
								echo "<tr>";
									echo "<td colspan='4' class='text-center'>".$c['mdCatName']."</td>";
								echo "</tr>";
								$lastCategory = $c['mdCatId'];
							}
							echo "<tr>";
								echo "<td>".($counter++)."</td>";
								echo "<td>".$c['mdName']."</td>";
								echo "<td>".$c['mdForm']."</td>";
								echo "<td>".$c['quantity']."</td>";
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
</script>