
<script>
	/* 
	$("#principaldiagnostic").autocomplete("./auto/diagnostic.php", {
		selectFirst: true
	});  */
	$("#nopecime").autocomplete("./auto/diagnostic_nopecime.php", {
		selectFirst: true
	}); 
	$("#pecime").autocomplete("./auto/diagnostic_pecime.php", {
		selectFirst: true
	}); 
	$("#secondarydiagnostic").autocomplete("./auto/diagnostic.php", {
		selectFirst: true
	}); 
</script>

Princ. <input type=text placeholder="Enter Principal Diagnostic" id=nopecime name=principaldiagnostic class='txtfield1' style='width:300px; font-size:12px;' value='<?= @$_GET['diag'] ?>' />
<?= @$_GET['diag_id']?"<input type=hidden name='principal_existbefore' value='{$_GET['diag_id']}' />":"" ?>
<script>
	var query_sent = false;
	$("#nopecime").keyup(function(e){
		/* return e.preventDefault();
		var nopecime_pattern = /psc/;
		var nopecime_pattern2 = /glossesse/;
		if(nopecime_pattern.test($("#nopecime").val().toLowerCase() ) && !query_sent ){
			query_sent = true
			if($("#olddata").val() == 1){
				$(".medecine1").load("./adds-on/medecine.php?number=1&code=2&wght=<?= @$_GET['wght'] ?>");
			} else{
				$(".medecine" + $("#olddata").val()).load("./adds-on/medecine.php?number=" + $("#olddata").val() + "&code=2&wght=<?= @$_GET['wght'] ?>");
			}
		}
		if(nopecime_pattern2.test($("#nopecime").val().toLowerCase())){
			$(".medecine1").load("./adds-on/medecine.php?number=1&code=3&wght=<?= @$_GET['wght'] ?>");
		} */
	});
</script>