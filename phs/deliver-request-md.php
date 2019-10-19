<?php
session_start();	
require_once "../lib/db_function.php";
if("phs" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
$error = "";

$active = "request";
require_once "../lib2/cssmenu/phs_header.html";
?>
<div id="w" style='height: auto;'>
    <div id="content" style='height: auto;'>
    	<b>
    		<h1 style='align: center; margin-top:-55px;'>Send Medicines Details Pharmacy</h1>
    		<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
				<tr>
					<td id="ds_calclass"></td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" style='font-size:14px; width: 100%;'>
    			<tr>
					<td>
						<table class='list' id='copy' style='width:100%;'>
							<thead>
								<tr id='th'>
									<th style='width:5%;'>#</th>
									<th style='width:20%;'>Medicine Name</th>
									<th style='width:10%;'>Requested</th>
									<th style='width:10%;'>Provided</th>
									<th style='width:20%;'>Batch</th>
									<th style='width:10%;'>Buying Price</th>
									<th style='width:20%;'>Expiration</th>
									<th style='width:10%;'>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if(@$_POST['confirm']){
									$i=1;
									//process every entry in the post array
									foreach($_POST as $index=>$value){
										$indexData = PDB($index,true,$con);

										$sql = "SELECT 	a.*,
														'' AS AvailableQuantity,
														'' AS MedicineStockID,
														'' AS expirationDate,
														'' AS batchNumber,
														'' AS price
														FROM md_name AS a
														";
										if($st_id = returnAllData($sql,$con)[0]){
											echo "<tr id='row{$i}'>";
												echo "<td style='text-align:right;'>{$i}<input type=hidden name='recId' id='rec{$i}' value='{$st_id['StockRecordsID']}' /></td>";
												echo "<td>{$st_id['MedecineName']}</td>";
												echo "<td style='text-align:right;'>".number_format($st_id['Quantity'])."</td>";
												echo "<td><input type=text name='qty{$i}' id='qty{$i}' value='".($st_id['Quantity'] > $st_id['AvailableQuantity']?$st_id['AvailableQuantity']:$st_id['Quantity'])."' /></td>";
												echo "<td>{$st_id['batchNumber']}</td>";
												echo "<td>{$st_id['price']}</td>";
												echo "<td>{$st_id['expirationDate']}</td>";
												echo "<td style='text-align:center;' title='Save changes?'><label><input type=checkbox name='save{$i}' onclick='saveData({$i});' id='save{$i}' value='{$indexData}'>Save</label></td>";
											echo "</tr>";
											$i++;
										}
									}
								}
								?>
							</tbody>
						</table>
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

<script type="text/javascript">
	function saveData(rowId){

		// check if the box is checked
		if($("#save" + rowId).prop("checked")){
			//alert($("#save" + rowId).prop("checked"));
			// $("#save" + rowId).prop("checked", ":true");
			// return false;

			var recordId 	= $("#rec" + rowId).val();
			var quantity 	= $("#qty" + rowId).val()*1;

			if(quantity <= 0){
				$("#row" + rowId).css("background", "#b77");
				$("#save" + rowId).removeProp("checked");
				alert("Row number " + rowId + " does not contain valid quantity!");
				return;
			}

			$.ajax({
				type: "POST",
				url: "./send-md.php",
				data: "recordId=" + recordId + "&quantity=" + quantity + "&url=ajax",
				cache: false,
				success: function(result){
					// $("#nn" + position_).html(result);
					if(result == "OK"){
						$("#row" + rowId).css("background", "#7b7");
					} else{
						$("#row" + rowId).css("background", "#b77");
						$("#save" + rowId).removeProp("checked");
						alert("Row " + rowId + " result is: " + result);
					}
					//console.log(result);
				},
				error: function(err){
					$("#row" + rowId).css("background", "#b77");
					$("#save" + rowId).removeProp("checked");
				}
			});
		} else{
			alert("There is no change on row number " + rowId + "\nPlease Save Other rows");
			$("#save" + rowId).prop("checked", ":true");
		}
	}
</script>