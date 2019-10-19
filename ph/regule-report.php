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
		<h1 style='align: center; margin-top:-55px;'>Medicines Adjust Report</h1>
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
    	<div>
			<input type=text id=start_date value='<?= date("Y-m-d",time()) ?>' onclick='ds_sh(this,"start_date")' class=txtfield1 style='width:100px;' />
			<input type=text id=end_date value='<?= date("Y-m-d",time()) ?>' onclick='ds_sh(this,"end_date")' class=txtfield1 style='width:100px;' />
			<input type=button id=gen value='Generate' class='flatbtn-blu' style='font-size:10px;' />
			<div style='text-align:right;'><input type=text name=search autocomplete=off style='width:50%' id=search placeholder="Filter Medicines to simplify report" /></div>
			<span id=bar style='position:relative; top:-20px; left:93%; height:0px;'>&nbsp;</span>
			<div class=stock_status style='height:420px; width:100%; overflow:auto;'>
				<script>
					//send the request to display lowest stock status
					$(".stock_status").load("./stock-adjust.php?start_date=" + $("#start_date").val() + "&end_date=" + $("#end_date").val());
				</script>
			</div>
		</div>
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
		$("#gen").click(function(e){
			$(".stock_status").load("./stock-adjust.php?start_date=" + $("#start_date").val() + "&end_date=" + $("#end_date").val());
		});
	
		var query_sent = false;
		$("#search").focus();
		
		$("#search").keyup(function(){
			$("#bar").html("<img src='../images/ajax_clock_small.gif' />");
			//now try send the filter query
			if(query_sent == false){
				query_sent = true;
				setTimeout(function(){
					if($("#search").val().trim != ""){
						$(".stock_status").load("./stock-adjust.php?filter=true&keyword=" + $("#search").val().replace(/ /g,"%20"));
						$("#bar").html("");
					}
					query_sent = false;
				},1500);
			}
		});
	});
</script>