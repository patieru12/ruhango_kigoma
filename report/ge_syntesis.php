<?php
session_start();
//var_dump($_SESSION);
require_once "../lib/db_function.php";
if("rcp" !== returnSingleField($sql="SELECT PostCode from sy_post WHERE PostID='{$_SESSION['user']['PostID']}'",$field="PostCode",$data=true, $con)){
	echo "<script>window.location='../logout.php';</script>";
	return;
}
require_once "../lib2/cssmenu/rcp_header.html";

?>
  <script type="text/javascript">
  	$(function(){
  		$('#print').hide();
  	});
  </script>
  <style>
	.fld_txt{
		width:80%;
		border:1px solid #00a;
	}
  </style>
  <div id="w">
    <div id="content">
      <h1 style='margin-top:-55px'>Laboratory Monthly Summary</h1>
      <b>
	  		<input type=hidden value='0' id='edit_mode' />
	<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	  <input type=hidden name=post_ value='_1' id='post' />
	  <table class=list-1><tr><td>Post</td><td>Year</td><td>Month</td><td><label for='exam'>Exam</label></td></tr>
	  <tr>
	  <td>
	  <label><input type=checkbox checked onclick='if($("#post_sovu").prop("checked")){$("#post").val($("#post").val() + "_1")} else{ $("#post").val($("#post").val().replace("_1","")) }' id='post_sovu' name=post value='1'>Busoro</label> 
	  <label><input type=checkbox onclick='if($("#post_rukira").prop("checked")){$("#post").val($("#post").val() + "_2")} else{ $("#post").val($("#post").val().replace("_2","")) }' name='post' id='post_rukira' value='2'>Post</label></td>
	  <td>
	  <select name=year class=txtfield1 style='width:70px;' id='year'>
		<?php
		for($y = date("Y",time()); $y>="2015";$y--){
			echo "<option>{$y}</option>";
		}
		?>
	  </select>
	  </td><td>
	  <select name=month class=txtfield1 style='width:140px;' id='month'>
		<?php
		$month = array(1=>"January","Febuary","March","April","May","June","July","August","September","October","November","December");
		for($m = 1; $m <= 12;$m++){
			$n = $m;
			if($n<10)
				$n = "0".$n;
			$ommitted = false;
			echo "<option value='{$n}' ".(date("m",time()) == $m?"selected":"").">".$month[$m]."</option>";
		}
		?>
	  </select>
	  </td>
	  <td>
	  	<select id='exam' name='exam' required class='txtfield1' style='width:190px;' >
	  		<option value='' selected ><i>Select Exam....</i></option>
	  		<?php
	  			$sql = "SELECT * FROM `la_exam` ORDER BY `ExamName` ASC";
	  			$query = mysql_query($sql);
	  			while ($get = mysql_fetch_assoc($query)) {
					$ommitted = false;
					$display = ommitStringPart($get['ExamName'],14,$ommitted);
	  				echo "<option value='".$get['ExamID']."' title='".($ommitted?$get['ExamName']:"")."'  >".$display."</option>";
	  			}
	  		?>
	  	</select>
	  </td>
	  <td>
	  <input type=button class=flatbtn-blu style='padding:0 10px;' id='generate' value='Generate' />
	  </td></tr>
	  </table>
	  <table class=list-1><tr><td>
	 <div id="ds" style='min-width:100px'></div></td></tr></table>
	  <span class='update_result'></span>
	  <input type=hidden id='filter_' />
	  <div class="patient_found" style='max-height:430px;'>
		<span class=error-text>Select Date to see Exam Result of Patients</span>
	  </div>
	  <div class=chart_e style='height:425px; width:540px; overflow:auto; float:right;'>
		<?php 
		$path_file = "../lib2/piechart/";
		require_once "../lib2/piechart/index.php"; ?>
	  </div>
	  <div class="doc_selected" style='height:425px; width:400px; overflow:auto;'>
	  
	  </div>
	  <div>
	  	<button id='print' class=flatbtn-blu style='padding:4 8px;margin-top: 10px; cursor: pointer;'>Print</button>
	  </div>
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
  <!-- make all links with the 'rel' attribute open overlays -->
<script>

$(document).ready(function(){
	//$(".chart_e").load("../lib2/piechart/index.php");
	$("#generate").click(function(e){
		var year = $("#year");
		var month = $("#month");
		var day = $("#day");
		var exam = $("#exam");
		$('#print').fadeIn();
		if (exam.val() != "") {
			var dt = year.val()+'-'+month.val();
			exam = exam.val();
			//$(".doc_selected").html(exam +"<br/>"+dt);

			$.post(
				"./la-record-loader.php",
				{
					ExamID : exam,
					ResultDate : dt 
				},
				function(data){
					$(".error-text").fadeOut(000);
					$(".doc_selected").html(data);
				}
			);
		}
		else $(".error-text").text("Please Select Exam.");

	});
	$("#print").click(function(){
		//window.html($(".table")).print();
		var divContent = $(".doc_selected").html();
		var printWindow = window.open('','','');
		printWindow.document.write("<html><body>");
		printWindow.document.write(divContent);
		printWindow.document.write("</body></html>");
		
		printWindow.print();
		
		printWindow.document.close();
	});	
});

</script>
</body>
</html>
