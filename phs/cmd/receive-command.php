<?php
session_start();
require_once "../../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
// var_dump($_GET);
$stockId = PDB($_GET['stockId'], true, $con);
// Get the Medicine Identification now
/*$md = formatResultSet($rslt=returnResultSet("SELECT a.MedecineNameID AS mdNameId,
													a.MedecineName AS mdName,
													a.form AS mdForm,
													b.MedecineCategoryName AS mdCatName,
													b.MedecineCategoryID AS mdCatId
													FROM md_name AS a
													INNER JOIN md_category AS b
													ON a.MedecineCategorID = b.MedecineCategoryID
													INNER JOIN md_main_stock AS c
													ON a.
													WHERE a.MedecineNameID = '{$mdid}'
													", $con), false, $con);*/

$md = formatResultSet($rslt=returnResultSet("SELECT a.MedecineNameID AS mdNameId,
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
													WHERE d.operation = 'REQUEST' AND d.status=0 AND d.id='{$stockId}'
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
<h5 class="alert alert-info text-center">Receive Medicines in Main Stock</h5>
<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<span class=save_adds_save></span>
		<form action="./cmd/save-received-command.php" method="POST" class="form-horizontal" id="commandListReceiveForm">
			<input type="hidden" name="stockId" value="<?= $md['stockId'] ?>">
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Batch</label>
				<div class="col-sm-8">
					<input type="text" name="batch" class="form-control" placeholder="Batch Number" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Unit Price</label>
				<div class="col-sm-8">
					<input type="text" name="price" class="form-control" placeholder="Unit Price for small Unit" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Expiration</label>
				<div class="col-sm-8">
					<input type="text" name="expiration" class="form-control" placeholder="Expiration Date" id="expirationDate" onclick="ds_sh(this, 'expirationDate');" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label text-center">Received</label>
				<div class="col-sm-8">
					<input type="text" name="quantity" class="form-control" placeholder="Requested Quantity" value="<?= $md['quantity'] ?>" />
				</div>
			</div>
			<div class="pull-right btn-group">
				<button type="button" id="receiveCommand" class="btn btn-primary"><i class="fa fa-check"></i> Receive</button>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$('#receiveCommand').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_adds_save").html('');
		$(".save_adds_save").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#commandListReceiveForm").ajaxForm({ 
			target: '.save_adds_save'
		}).submit();
			
	});
</script>