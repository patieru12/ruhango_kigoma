<?php
	session_start();
	
	require_once "../lib/db_function.php";
	if("ph" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
		echo "<script>window.location='../logout.php';</script>";
		return;
	}
	$error = "";
	
$active = "request";
require_once "../lib2/cssmenu/ph_header.html";
?>
  <div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
		<h1 style='align: center; margin-top:-55px;'>Medicines Request Preparation</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<table cellpadding="0" cellspacing="0" style='font-size:14px;'>
    		<tr>
    			<!-- Left container -->
				<td>
					<input type=text name=search autocomplete=off style='width:100%' id=search placeholder="Filter Medicines" />
					<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
					<div class=stock_status style='height:420px; width:350px; overflow:auto;'>
						<script>
							//send the request to display lowest stock status
							$(".stock_status").load("./stock-level.php");
						</script>
					</div>
				</td>
				<td>
					<?= $error; ?>
					<?php
					//if the update command is available try to update some data
					$update = null;
					if(@$_GET['update'] && is_numeric($_GET['update'])){
						$update = returnAllData($sql="SELECT md_name.MedecineName, md_name.MedecineNameID, md_stock_records.* FROM md_stock_records, md_stock, md_name WHERE md_stock_records.MedicineStockID = md_stock.MedicineStockID && md_stock.MedicineNameID = md_name.MedecineNameID && StockRecordsID='".PDB($_GET['update'],true,$con)."' && Operation='REQUEST'",$con)[0];
						//echo $sql;
						//var_dump($update);
					}
					?>
					<form action='prepare-request.php' method='post'>
					<input type=hidden name=medicine id=medicine value='<?= @$update['MedecineNameID'] ?>' />
    				<table style='font-size:16px;' border=0>
			    			
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
					//check if there is a command not yet ends
					//$request_data = formatResultSet($rslt=returnResultSet($sql="SELECT DISTINCT OperationCode FROM md_stock_records WHERE Operation='REQUEST' && Status=0 ORDER BY Date ASC",$con),$multirows=false,$con);
					
					//check if the data is submitted
					if(@$_POST['save_date']){
						//save the new request
						//var_dump($_POST);
						if($_POST['medicine']){
							if($_POST['quantity']){
								//save the request now
								//if($request['OperationCode']){
								//every thing is fine then save
								if(!$stock_id = returnSingleField("SELECT MedicineStockID FROM md_stock WHERE MedicineNameID='".PDB($_POST['medicine'],true,$con)."'","MedicineStockID",true,$con)){
									$stock_id = saveAndReturnID("INSERT INTO md_stock SET MedicineNameID='".PDB($_POST['medicine'],true,$con)."', Quantity='0', Date='".time()."'",$con);
								}
								//check if the request is received before
								if(!$request_id = returnSingleField("SELECT StockRecordsID FROM md_stock_records WHERE MedicineStockID='{$stock_id}' && Operation='REQUEST' && Status=0","StockRecordsID",true,$con))
									saveData("INSERT INTO md_stock_records SET MedicineStockID='{$stock_id}', Operation='REQUEST', Quantity='".PDB($_POST['quantity'],true,$con)."', Date='".time()."', UserID='".$_SESSION['user']['UserID']."', Status=0",$con);
								else
									saveData("UPDATE md_stock_records SET Quantity='".PDB($_POST['quantity'],true,$con)."' WHERE StockRecordsID='{$request_id}'",$con);
								
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
					$request_data = formatResultSet($rslt=returnResultSet($sql="SELECT md_stock_records.*, md_name.MedecineName FROM md_name, md_stock, md_stock_records WHERE md_stock.MedicineStockID = md_stock_records.MedicineStockID && md_stock.MedicineNameID = md_name.MedecineNameID && md_stock_records.Status=0 && md_stock_records.Operation='REQUEST'",$con),$multirows=true,$con);
					//var_dump($request_data);
					?>
					<form action="./prepare-request.php" method=post>
						<table class=list style='width:100%;'>
							<tr><th>#</th><th>Medicine</th><th>Quantity</th><th colspan=2>Operation</th></tr>
							<?php
							if($request_data)
								foreach($request_data as $rqst){
									echo "<tr>
									<td><input class=all type=checkbox ".(@$_POST[$rqst['StockRecordsID']]?"checked":"")." name='{$rqst['StockRecordsID']}' /></td>
									<td>{$rqst['MedecineName']}</td>
									<td>{$rqst['Quantity']}</td>
									<td><a href='./prepare-request.php?update={$rqst['StockRecordsID']}' style='color:blue; text-decoration:none;' href=''>Change</a></td>
									<td><a href='./prepare-request.php?delete={$rqst['StockRecordsID']}' style='color:blue; text-decoration:none;' onclick='return confirm(\"Delete {$rqst['MedecineName']} From the Request\")'>Delete</a></td>
									</tr>";
								}
							?>
						</table>
						<label style='cursor:pointer; color:blue;'><input type='checkbox' id='control' onclick='if($("#control").prop("checked")){ $(".all").attr("checked",":true"); } else { $(".all").removeAttr("checked"); }' />check all</label><br />
						<input type=submit id='confirm' title='Approve That you received Requested Medicines' class=flatbtn-blu style='font-size:10px;' name='confirm' value='Request Received' />
						<input type=button name='print' onclick='click_now();' title='Print the list of requested medicines' class=flatbtn-blu style='font-size:10px;'  value='Print Request' />
					</form>
					<a href='./print-active-request.php' id='print_active' target='_blank'></a>
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
</body>
</html>

<script>
	function regulateStock(stock_id, position_){
		var new_value = prompt("Enter New Stock Value",0);
		//$("#nn" + position_).html(new_value);
		$.ajax({
			type: "POST",
			url: "./regulate.php",
			data: "stock_id=" + stock_id + "&old_value=" + $("#nn" + position_).html() + "&new_value=" + new_value + "&position=" + position_ + "&url=ajax",
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
	});
</script>