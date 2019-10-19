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
		<h1 style='align: center; margin-top:-55px;'>Medicine Main Stock Level</h1>
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
					$stock = formatResultSet($rslt=returnResultSet("SELECT 	a.MedecineName AS MedecineName,
																			b.MedecineCategoryName,
																			a.smallUnit,
																			a.form,
																			b.MedecineCategoryID,
																			c.batchNumber,
																			c.quantity,
																			c.price,
																			c.expiration,
																			a.MedecineNameID AS mdid
																			FROM md_name AS a
																			INNER JOIN md_category AS b
																			ON a.MedecineCategorID = b.MedecineCategoryID
																			LEFT JOIN md_main_stock AS c
																			ON a.MedecineNameID = c.mdNameId AND quantity > 0
																			ORDER BY MedecineCategoryName ASC, MedecineName ASC
																			", $con), true, $con);
					?>
					<div style="max-height: 665px; overflow: auto;">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Dosage</th>
									<th>Form</th>
									<th>Batch</th>
									<th>Quantity</th>
									<th>Price</th>
									<th>Exipration</th>
									<th colspan="2">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if(count($stock) > 0){
									// Here Loop all medicine in the stock to display their Status
									$lastCategory = "";
									foreach($stock AS $s){
										if($s["MedecineCategoryID"] != $lastCategory){
											echo "<tr>";
												echo "<td colspan='10' class='text-center'>".$s['MedecineCategoryName']."</td>";
											echo "</tr>";
											$lastCategory = $s["MedecineCategoryID"];
										}
										echo "<tr>";
											echo "<td></td>";
											echo "<td>". ucfirst(strtolower($s['MedecineName']))."</td>";
											echo "<td>{$s['smallUnit']}</td>";
											echo "<td>{$s['form']}</td>";
											echo "<td>{$s['batchNumber']}</td>";
											echo "<td>{$s['quantity']}</td>";
											echo "<td>{$s['price']}</td>";
											echo "<td>{$s['expiration']}</td>";
											echo "<td><a href='#'><i class='fa fa-pencil'></i></a></td>";
											echo "<td><a href='./cmd/prepare-command.php?mdid={$s['mdid']}' title='Add to Command List' rel='#overlay'><i class='fa fa-plus'></i></a></td>";
										echo "</tr>";
									}
								} else{
									?>
									<tr>
										<td colspan="10" class="text-center">No Medicine Found in the Stock</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
    			</td>
    		</tr>
    	</table>
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