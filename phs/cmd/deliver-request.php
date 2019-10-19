<?php
session_start();
require_once "../../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_GET);
$currentDate = date("Y-m-d", time());
$stockId = PDB($_GET['stockId'], true, $con);
// Get the Medicine Identification now

$md = formatResultSet($rslt=returnResultSet("SELECT a.MedecineNameID AS mdNameId,
													a.MedecineName AS mdName,
													a.form AS mdForm,
													b.MedecineCategoryName AS mdCatName,
													b.MedecineCategoryID AS mdCatId,
													d.quantity,
													d.StockRecordsID AS stockId
													FROM md_name AS a
													INNER JOIN md_category AS b
													ON a.MedecineCategorID = b.MedecineCategoryID
													INNER JOIN md_stock AS c
													ON a.MedecineNameID = c.MedicineNameID
													INNER JOIN md_stock_records AS d
													ON c.MedicineStockID = d.MedicineStockID
													WHERE d.operation = 'REQUEST' AND d.status=0 AND d.StockRecordsID='{$stockId}'
													ORDER BY mdCatName ASC, mdName ASC
													", $con), false, $con);
?>
<div class="row">
	<div class="col-md-12">
		<div class="col-sm-6">
			Medicine Name: <strong><?= $md['mdName'] ?></strong>
		</div>
		<div class="col-sm-6">
			Medicine Category: <strong><?= $md['mdCatName'] ?></strong>
		</div>
		<div class="col-sm-6">
			Medicine Form: <strong><?= $md['mdForm'] ?></strong>
		</div>
		<div class="col-sm-6">
			Requested: <strong><?= number_format($md['quantity']) ?></strong>
		</div>
	</div>
</div>
<h5 class="alert alert-info text-center">Deliver Request for <strong><?= $md['mdName'] ?></strong></h5>
<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<span class=save_delivers></span>
		<form action="./cmd/save-delivered-command.php" method="POST" class="form-horizontal" id="commandListDeliveredeForm">
			<input type="hidden" name="stockId" value="<?= $md['stockId'] ?>">
			<input type="hidden" name="mdNameId" value="<?= $md['mdNameId'] ?>">
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Provided Quantity</label>
				<div class="col-sm-6">
					<input type="text" name="quantity" id="quantity" class="form-control" placeholder="Requested Quantity" value="<?= $md['quantity'] ?>" />
				</div>
				<div class="pull-right btn-group col-sm-3">
					<button type="button" id="deliverCommand" class="btn btn-success"><i class="fa fa-thumbs-o-up"></i> Deliver</button>
				</div>
			</div>
		</form>
	</div>
</div>
<h5 class="alert alert-success text-center">Current Stock Status</h5>
<?php
// Get the Main Stock Status
$stock = formatResultSet($rslt=returnResultSet("SELECT 	a.quantity,
														a.expiration,
														a.batchNumber,
														a.date
														FROM md_main_stock AS a
														WHERE a.mdNameId = '{$md['mdNameId']}' AND
															  a.expiration > '{$currentDate}' AND
															  a.quantity > 0
														ORDER BY expiration ASC
														", $con), true, $con);
// var_dump($stock);
if(count($stock) >0){
	$availableQty = 0;
	?>
	<div style="max-height: 130px;overflow: auto;">
		<table class="table table-bordered table-striped">
			<thead>
				<th>#</th>
				<th>Reg Date</th>
				<th>Batch Number</th>
				<th>Expiration</th>
				<th>Quantity</th>
			</thead>
			<tbody>
				<?php
				foreach($stock AS $s){
					$availableQty += $s['quantity'];
					?>
					<tr>
						<td></td>
						<td><?= $s['date'] ?></td>
						<td><?= $s['batchNumber'] ?></td>
						<td><?= $s['expiration'] ?></td>
						<td><?= number_format($s['quantity']) ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<input type="hidden" name="availableQty" id="availableQty" value="<?= $availableQty ?>" />
	</div>
	<?php
}
?>
<script type="text/javascript">
	$('#deliverCommand').click(function(e){ 
		e.preventDefault();
		// Check the Form Status and infor the user for Some Error
		var availableQty = $("#availableQty").val()*1;
		var requestedQty = $("#quantity").val()*1;
		if(requestedQty > availableQty){
			if(confirm("The Requested Quantity: " + requestedQty + "\nIs greater than Available: " + availableQty + "\nThe complete available Quantity will be distributed")){
				$("#quantity").val(availableQty);
			} else{
				return e.preventDefault();
			}
		}
		// return;
		//$('#save').attr("desabled",":true");
		$(".save_delivers").html('');
		$(".save_delivers").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#commandListDeliveredeForm").ajaxForm({ 
			target: '.save_delivers'
		}).submit();
			
	});
</script>