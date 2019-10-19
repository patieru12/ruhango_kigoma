
<script>
	
	$("#pecime_sec").autocomplete("./auto/diagnostic_pecime.php", {
		selectFirst: true
	});
</script>

Sec. <input type=text placeholder="Enter Secondary Diagnostic" id=pecime_sec name=secondarydiagnostic class='txtfield1' style='width:150px; font-size:12px;' />
<script>
	var query_sent = false;
	$("#pecime_sec").keyup(function(e){
		
		var pecime_pattern = /psc/;
		if(pecime_pattern.test($("#pecime_sec").val().toLowerCase() ) && !query_sent ){
			query_sent = true
			if($("#olddata").val() == 1){
				$(".medecine1").load("./adds-on/medecine.php?number=1&code=2&wght=<?= @$_GET['wght'] ?>");
			} else{
				$(".medecine" + $("#olddata").val()).load("./adds-on/medecine.php?number=" + $("#olddata").val() + "&code=2&wght=<?= @$_GET['wght'] ?>");
			}
		}
	});
</script>