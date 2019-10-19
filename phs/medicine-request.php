<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$error = "";
	
$active = "request";
$currentDate = date("Y-m-d", time());
require_once "../lib2/cssmenu/phs_header.html";
?>
<div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Confirm Requested Medicines</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="0" cellspacing="0" style='font-size:14px;'>
    		<tr>
    			<td>
					<?= $error; ?>
					<?php
					//if the update command is available try to update some data
					$update = null;
					if(@$_GET['update'] && is_numeric($_GET['update'])){
						$update = returnAllData($sql="SELECT md_name.MedecineName, md_name.MedecineNameID, md_main_stock_records.* FROM md_main_stock_records, md_main_stock, md_stock_batch, md_name WHERE md_main_stock_records.MedicineStockID = md_main_stock.id && md_main_stock.batchId = md_stock_batch.id && md_stock_batch.medicineNameId = md_name.MedecineNameID && md_main_stock_records.StockRecordsID='".PDB($_GET['update'],true,$con)."' && Operation='REQUEST'",$con)[0];
						//echo $sql;
						//var_dump($update);
					}
					/*
					?>
					<form action='prepare-request-md.php' method='post'>
						<input type=hidden name=medicine id=medicine value='<?= @$update['MedecineNameID'] ?>' />
    					<table style='font-size:16px; width: 100%;' border=0>
							<tr>
			    				<td><label>Medicines:</label></td>
								<td class=medicine_field><?= @$update['MedecineName'] ?></td>
							</tr>
							<tr>
			    				<td><label>Quantity:</label></td>
								<td><input required type="text" name='quantity' class='txtfield1' style='width:100%; ' value = '<?= @$update['Quantity'] ?>' onclick='' /></td>
							</tr>
							<tr>
								<td colspan='2' align=center><input type="submit" class="flatbtn" style='font-size:12px;' name="save_date" value="Add" /></td>
							</tr>
    					</table>
			    	</form>
					<?php
					*/
					//check if there is a command not yet ends
					//$request_data = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT OperationCode FROM md_stock_records WHERE Operation='REQUEST' && Status=0 ORDER BY Date ASC",$con),$multirows=false,$con);
					
					//check if the data is submitted
					if(@$_POST['save_date']){
						//save the new request
						//var_dump($_POST);
						if($_POST['medicine']){
							if($_POST['quantity']){
								$date = date("Y-m-d", time());
								$medicine = PDB($_POST['medicine'],true,$con);
								// Check the batch information now
								if(!$batchId = returnSingleField("SELECT id FROM md_stock_batch WHERE medicineNameId='{$medicine}' && Date='{$date}' && batchNumber IS NULL", "id", true, $con)){
									$batchId = saveAndReturnID("INSERT INTO md_stock_batch SET medicineNameId='{$medicine}', Date='{$date}'", $con);
								}
								//save the request now
								//if($request['OperationCode']){
								//every thing is fine then save
								if(!$stock_id = returnSingleField("SELECT id FROM md_main_stock WHERE batchId='{$batchId}'","id",true,$con)){
									$stock_id = saveAndReturnID("INSERT INTO md_main_stock SET batchId='{$batchId}', quantity='0', Date='".time()."'",$con);
								}
								//check if the request is received before
								if(!$request_id = returnSingleField("SELECT StockRecordsID FROM md_main_stock_records WHERE MedicineStockID='{$stock_id}' && Operation='REQUEST' && Status=0","StockRecordsID",true,$con)){
									saveData("INSERT INTO md_main_stock_records SET MedicineStockID='{$stock_id}', Operation='REQUEST', quantity='".PDB($_POST['quantity'],true,$con)."', Date='".time()."', UserID='".$_SESSION['user']['UserID']."', Status=0",$con);
								} else{
									saveData("UPDATE md_main_stock_records SET quantity='".PDB($_POST['quantity'],true,$con)."' WHERE StockRecordsID='{$request_id}'",$con);
								}
							} else{
								echo "<span class=error-text>No Quantity Entered</span>";
							}
						} else{
							echo "<span class=error-text>No Medicine Selected</span>";
						}
					}
					//delete the requested medicine now
					if(@$_GET['delete'] && is_numeric($_GET['delete'])){
						saveData("DELETE FROM md_stock_records WHERE StockRecordsID='".PDB($_GET['delete'],true,$con)."'",$con);
					}
					//var_dump($_POST);
					if(@$_POST['confirm']){
						//process every entry in the post array
						foreach($_POST as $index=>$value){
							//if the index exist in our database create a request to
							if($st_id = returnAllData("SELECT * FROM md_stock_records WHERE StockRecordsID='".PDB($index,true,$con)."' && Status=0",$con)[0]){
								//create the IN Query to update the stock
								$sql = "INSERT INTO md_stock_records SET MedicineStockID='{$st_id['MedicineStockID']}', Operation='IN', Quantity='{$st_id['Quantity']}', Date='".time()."', UserID='".$_SESSION['user']['UserID']."', Status='1'";
								//echo $sql."<br />";
								saveData($sql, $con);
								//create a query to update the request
								$sql = "UPDATE md_stock_records SET Status=1 WHERE StockRecordsID='".PDB($index,true,$con)."'";
								//echo $sql."<br />";
								saveData($sql, $con);
								//create a query to increase the stock
								$sql = "UPDATE md_stock SET Quantity= Quantity + {$st_id['Quantity']} WHERE MedicineStockID='".PDB($st_id['MedicineStockID'],true,$con)."'";
								//echo $sql."<br />";
								saveData($sql, $con);
								//then unset the post array
								unset($_POST);
							}
						}
					}
					//now select all non-approved request with the current operation code
					$sql = "SELECT 	a.StockLevel,
									a.Quantity,
									a.Comment,
									c.MedecineName,
									a.StockRecordsID AS StockRecordsID,
									COALESCE(d.quantity, 0) AS AvailableQuantity,
									COALESCE(d.expiration, 'Not available') AS expirationDate,
									c.MedecineNameID AS mdid
									FROM md_stock_records AS a
									INNER JOIN md_stock AS b
									ON a.MedicineStockID = b.MedicineStockID
									INNER JOIN md_name AS c
									ON b.MedicineNameID = c.MedecineNameID
									LEFT JOIN(
										SELECT 	SUM(b.quantity) AS quantity,
												a.MedecineNameID,
												MIN(b.expiration) AS expiration
												FROM md_name AS a
												INNER JOIN md_main_stock AS b
												ON a.MedecineNameID = b.mdNameId
												WHERE b.quantity > 0 AND 
										  			  b.expiration > '$currentDate'
										  		GROUP BY a.MedecineNameID
									) AS d
									ON c.MedecineNameID = d.MedecineNameID
									WHERE a.Status = 0 AND 
										  a.Operation='REQUEST'";
					//now select all non-approved request with the current operation code
					$request_data = formatResultSet($rslt=returnResultSet($sql,$con),$multirows=true,$con);
					// $request_data = formatResultSet($rslt=returnResultSet($sql="SELECT md_stock_records.*, md_name.MedecineName, md_stock.Quantity as Stock FROM md_name, md_stock, md_stock_records WHERE md_stock.MedicineStockID = md_stock_records.MedicineStockID && md_stock.MedicineNameID = md_name.MedecineNameID && md_stock_records.Status=0 && md_stock_records.Operation='REQUEST' ORDER BY md_stock.Quantity ASC;",$con),$multirows=true,$con);
					//var_dump($request_data);
					?>
					<form action="./deliver-request-md.php" method=post>
						<table class="list table table-bordered table-striped">
							<tr><th>#</th><th>Medicine</th><th>Requested Quantity</th><th>Available Quantity</th><th>Next Expiration</th><!-- <th>Provided Quantity</th> --><th colspan="2">Operation</th></tr>
							<?php
							if($request_data){
								$counter = 1;
								foreach($request_data as $rqst){
									echo "<tr>
									<td>";
									if($rqst['AvailableQuantity'] < $rqst['Quantity']){
										echo "<a href='./cmd/prepare-command.php?mdid={$rqst['mdid']}' rel='#overlay' title='add to command list'><i class='fa fa-plus'></i></a>";
									} else {
										echo $counter;
									}
									$counter++;
									echo "</td>
									<td>{$rqst['MedecineName']}".(!is_null($rqst['Comment'])?" <i class='fa fa-comment-o text-success' title='{$rqst['Comment']}' ></i>":"")."</td>
									<td class='text-right'>".number_format($rqst['Quantity'])."</td>
									<td class='text-right'>".number_format($rqst['AvailableQuantity'])."</td>
									<td>{$rqst['expirationDate']}</td>
									<td class='text-center'><a href='./cmd/addcomment.php?stockId={$rqst['StockRecordsID']}' style='' rel='#overlay' class='text-success' title='Add Comment'><i class='fa fa-comment'></i></a></td>
									<td class='text-center'>".($rqst['AvailableQuantity'] > 0?"<a href='./cmd/deliver-request.php?stockId={$rqst['StockRecordsID']}' style='' rel='#overlay' class='text-primary' title='Deliver Medicine'><i class='fa fa-thumbs-o-up'></i></a>":"<a href='#' class='text-danger' title='No Stock is Available'><i class='fa fa-thumbs-o-down'></i></a>")."</td>
									</tr>";
								}
							}
							?>
						</table>
						<!-- <label style='cursor:pointer; color:blue;'><input type='checkbox' id='control' checked="" onclick='if($("#control").prop("checked")){ $(".all").attr("checked",":true"); } else { $(".all").removeAttr("checked"); }' />check all</label><br /> -->
						<!-- <input type=submit id='confirm' title='Approve That you received Requested Medicines' class='btn btn-primary' style='font-size:12px;' name='confirm' value='Save and Deliver' /> -->
						<!--<input type=button name='print' onclick='click_now();' title='Print the list of requested medicines' class=flatbtn-blu style='font-size:12px; padding: 5 10px'  value='Save & Print Delivered' />-->
					</form>
					<a href='./print-active-request-md.php' id='print_active' target='_blank'></a>
					<script>
						function click_now(){
							try{
								$('#print_active')[0].click();
							} catch(e){
								console.log("Error!");
							}
						}
					</script>
    			</td>
    		</tr>
    	</table>
    	<div style='height: 10px;'></div>
    	</b>
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

<script>
	function regulateStock(stock_id, position_, batchNumber){
		var new_value = prompt("Enter New Stock Value for batch number: " + batchNumber,0);
		//$("#nn" + position_).html(new_value);
		$.ajax({
			type: "POST",
			url: "./regulate.php",
			data: "stock_id=" + stock_id + "&batch_number=" + batchNumber + "&old_value=" + $("#nn" + position_).html() + "&new_value=" + new_value + "&position=" + position_ + "&url=ajax",
			cache: false,
			success: function(result){
				$("#nn" + position_).html(result)
				//console.log(result);
			},
			error: function(err){
				console.log(err.responseText());
			}
		});
	}
	$(document).ready(function(){
		var query_sent = false;
		$("#search").focus();
		
		$("#search").keyup(function(){
			$("#bar").html("<img src='../images/ajax_clock_small.gif' />");
			//now try send the filter query
			if(query_sent == false){
				query_sent = true;
				setTimeout(function(){
					if($("#search").val().trim != ""){
						$(".stock_status").load("./stock-level.php?filter=true&keyword=" + $("#search").val().replace(/ /g,"%20"));
						$("#bar").html("");
					}
					query_sent = false;
				},1000);
			}
		});

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