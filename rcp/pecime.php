
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


Princ. <input type=text placeholder="Enter Principal Diagnostic" id=pecime name=principaldiagnostic class='txtfield1' style='width:150px; font-size:12px;' />
<script>
	var query_sent = false;
	$("#pecime").keyup(function(e){
		
		var pecime_pattern = /psc/;
		if(pecime_pattern.test($("#pecime").val().toLowerCase() ) && !query_sent ){
			query_sent = true
			if($("#olddata").val() == 1){
				$(".medecine1").load("./adds-on/medecine.php?number=1&code=2&wght=<?= @$_GET['wght'] ?>");
			} else{
				$(".medecine" + $("#olddata").val()).load("./adds-on/medecine.php?number=" + $("#olddata").val() + "&code=2&wght=<?= @$_GET['wght'] ?>");
			}
		}
	});
</script>