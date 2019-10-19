
<form action="save_new_patient.php" method=post id=newpatient>
<label for="name">Name <span class=error-text>*</span></label>
<input type=text name=name id=name tabindex="1" placeholder='Patient Name' class="txtfield" />
Date of Birth<span class=error-text>*</span>
<input type=text placeholder="Birth Date of a Patient" id=dob tabindex="2"  onclick='ds_sh(this,"dob")' name=dob class="txtfield" />
Father
<input type=text name=father id=father tabindex="3" placeholder="patient's Father" class="txtfield" />
Mother
<input type=text name=mother id=mother tabindex="4" placeholder="patient's Mother" class="txtfield" />
Position in Family
<input type=text tabindex="5" id=position placeholder="Patient Position in Family" name=position class="txtfield" />
National ID
<input type=text name=n_id tabindex="6" id=n_id placeholder="Patient National Identity Number" class="txtfield" />
Family Chief ID No:
<input type=text name=fcid id=fcid placeholder='Family Chief Identity card Number' tabindex="7" class="txtfield" />
Address:
<input type=text name=address id=address tabindex="8" placeholder="Where Patient Reside" class="txtfield" id="address" />
<div class="center"><input type="submit" name="savebtn" id="loginbtn" class="flatbtn-blu" value="Save" tabindex="9"><span class=savedata></span></div>

</form>
	
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass"
	   style="display: none;">
	<tr>
		<td id="ds_calclass"></td>
	</tr></table>
	
<script>
$(document).ready(function(){
	$('#newpatient').submit(function(e){
		$(".savedata").html("saving!!!");
		$.ajax({
			type: "POST",
			url: "./save_new_patient.php",
			data: "name=" + $("#name").val() + "&dob=" + $("#dob").val() + "&father=" + $("#father").val() + "&mother=" + $("#mother").val() + "&position=" + $("#position").val() + "&nid=" + $("#n_id").val() + "&familychief=" + $("#fcid").val() + "&address=" + $("#address").val() + "&url=ajax",
			cache: false,
			success: function(result){
				$(".savedata").html(result);
			}
		});
		return e.preventDefault();
	});
});
</script>