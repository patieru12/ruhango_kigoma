$(document).ready(function(){
	$("#class_search").autocomplete("./lib2/autocomplete/book/title.php", {
		selectFirst: true
	}); 
	$("#authors").autocomplete("./lib2/autocomplete/book/authors.php", {
		selectFirst: true
	});
	$("#collection").autocomplete("./lib2/autocomplete/book/collection.php", {
		selectFirst: true
	});
	$("#call_number").autocomplete("./lib2/autocomplete/book/call_n.php", {
		selectFirst: true
	}); 
	/* $("#pub_year").autocomplete("./lib2/autocomplete/autocomplete.php", {
		selectFirst: true
	});  */
	$("#publisher").autocomplete("./lib2/autocomplete/book/publisher.php", {
		selectFirst: true
	}); 
	$("#city").autocomplete("./lib2/autocomplete/book/city.php", {
		selectFirst: true
	}); 
	$("#language").autocomplete("./lib2/autocomplete/book/lng.php", {
		selectFirst: true
	}); 
	$("#mode").autocomplete("./lib2/autocomplete/autocomplete.php", {
		selectFirst: true
	}); 
	$("#tag2").autocomplete("./lib2/autocomplete/autocomplete.php", {
		selectFirst: true
	});
});