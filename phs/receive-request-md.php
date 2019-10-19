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
    		<h1 style='align: center; margin-top:-55px;'>Medicines Received From outside</h1>
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
									<th style='width:50px;'>#</th>
									<th style='width:253px;'>Medicine Name</th>
									<th style='width:10%;'>Requested</th>
									<th style='width:10%;'>Received</th>
									<th style='width:10%;'>Batch</th>
									<th style='width:80px;'>Buying Price</th>
									<th style='width:80px;'>Expiration</th>
									<th style='width:90px;'>Emballage</th>
									<th style='width:90px;'>Comment</th>
									<th style='width:90px;'>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if(@$_POST['confirm']){
									$i=1;
									//process every entry in the post array
									foreach($_POST as $index=>$value){
										$indexData = PDB($index,true,$con);
										$sql = "SELECT 	a.Quantity AS Quantity,
														d.MedecineName AS MedecineName,
														a.StockRecordsID AS StockRecordsID
														FROM md_main_stock_records AS a 
														INNER JOIN md_main_stock AS b
														ON a.MedicineStockID = b.id
														INNER JOIN md_stock_batch AS c
														ON b.batchId = c.id
														INNER JOIN md_name AS d
														ON c.medicineNameId = d.MedecineNameID
														WHERE a.StockRecordsID='{$indexData}' && 
															  a.Status=0";
										if($st_id = returnAllData($sql,$con)[0]){
											echo "<tr id='row{$i}'>";
												echo "<td style='text-align:right;'>{$i}<input type=hidden name='recId' id='rec{$i}' value='{$st_id['StockRecordsID']}' /></td>";
												echo "<td>{$st_id['MedecineName']}</td>";
												echo "<td style='text-align:right;'>".number_format($st_id['Quantity'])."</td>";
												echo "<td><input type=text name='qty{$i}' id='qty{$i}' value='{$st_id['Quantity']}' /></td>";
												echo "<td><input type=text name='batch{$i}' id='batch{$i}' value='' /></td>";
												echo "<td><input type=text name='buying{$i}' id='buying{$i}' value='' /></td>";
												echo "<td><input type=text readonly name='expiration{$i}' id='expiration{$i}' onclick='ds_sh(this, \"expiration{$i}\")' value='' /></td>";
												echo "<td style='text-align:center;' title='Did Medicine require packaging?'><input type=checkbox name='emballage{$i}' checked value='1'></td>";
												echo "<td><input type=text name='comment{$i}' id='comment{$i}' value='From District Pharmacy' /></td>";
												echo "<td style='text-align:center;' title='Save changes?'><label><input type=checkbox name='save{$i}' onclick='saveData({$i});' id='save{$i}'>Save</label></td>";
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
			var batchNumber = $("#batch" + rowId).val();
			var price 		= $("#buying" + rowId).val()*1;
			var expiration 	= $("#expiration" + rowId).val();
			var emballage 	= $("#emballage" + rowId).prop("checked")?1:0;
			var comment 	= $("#comment" + rowId).val();

			if(quantity <= 0){
				$("#row" + rowId).css("background", "#b77");
				$("#save" + rowId).removeProp("checked");
				alert("Row number " + rowId + " does not contain valid quantity!");
				return;
			}
			if(price <= 0){
				$("#row" + rowId).css("background", "#b77");
				$("#save" + rowId).removeProp("checked");
				alert("Row number " + rowId + " does not contain valid buying price!");
				return;
			}
			if(!batchNumber){
				$("#row" + rowId).css("background", "#b77");
				$("#save" + rowId).removeProp("checked");
				alert("Row number " + rowId + " does not contain valid Batch number!");
				return;
			}
			if(!expiration){
				$("#row" + rowId).css("background", "#b77");
				$("#save" + rowId).removeProp("checked");
				alert("Row number " + rowId + " does not contain valid Expiration date!");
				return;
			}
			if(!comment){
				$("#row" + rowId).css("background", "#b77");
				$("#save" + rowId).removeProp("checked");
				alert("Row number " + rowId + " does not contain valid comment info!");
				return;
			}

			$.ajax({
				type: "POST",
				url: "./receive-md.php",
				data: "recordId=" + recordId + "&quantity=" + quantity + "&batch_number=" + batchNumber + "&price=" + price + "&expiration=" + expiration + "&emballage=" + emballage + "&comment=" + comment + "&url=ajax",
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