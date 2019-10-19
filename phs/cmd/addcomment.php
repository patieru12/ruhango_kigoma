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
													d.StockRecordsID AS stockId,
													d.Comment
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
<h5 class="alert alert-warning text-center">Add Comment to the Request</h5>
<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<span class=save_comments></span>
		<form action="./cmd/save-comment.php" method="POST" class="form-horizontal" id="commandListCommentForm">
			<input type="hidden" name="stockId" value="<?= $md['stockId'] ?>">
			
			<div class="form-group">
				<label class="col-sm-2 control-label text-center">Comment</label>
				<div class="col-sm-8">
					<textarea name="comment" rows="3" class="form-control" placeholder="Comment"><?= $md['Comment'] ?></textarea>
				</div>
				<div class="pull-right btn-group col-sm-2">
					<button type="button" id="commandComment" class="btn btn-warning"><i class="fa fa-comment-o"></i> Send</button>
					<!-- <button type="button" id="deliverCommand" class="btn btn-success"><i class="fa fa-thumbs-o-up"></i> Deliver</button> -->
				</div>
			</div>
			<!-- <div class="pull-right btn-group">
				
			</div> -->
		</form>
	</div>
</div>
<h5 class="alert alert-warning text-center">Current Stock Status</h5>
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
	$('#commandComment').click(function(e){ 
		e.preventDefault();
		//$('#save').attr("desabled",":true");
		$(".save_comments").html('');
		$(".save_comments").html('<img src="../images/loading.gif" alt="Saving"/>'); 
		$("#commandListCommentForm").ajaxForm({ 
			target: '.save_comments'
		}).submit();
			
	});
</script>