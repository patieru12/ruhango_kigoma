var base_url  = '';

function msab_getDashboard() {

	var moduleContainer = $("#moduleContainer");

	var content = '';
		content +='<aside class="right-side">';
			content +='<section class="content-header">';
				content +='<div id="dashSubmenu">';
				content +='</div>';
			content +='</section>';
			content +='<section class="content">';
				content +='<div class="row">';
					content +='<div class="grid text-center">';
						content +='<img src="../packages/assets/plugins/img/loading.gif" alt=""> Loading, Please wait ...';
							content +='';
						content +='</div>';
					content +='</div>';
				content +='</div>';
			content +='</section>';
		content +='</aside>';
		
		moduleContainer.html(content);

		var url 			 = base_url+"ABAdmin/Dashboard";

		 $.ajax({
	        url: url ,
	        type: "GET",
	        dataType: "html",
	        success: function (data) {
	            
	            $("#moduleContainer").html(data);

	        },
	        error: function (xhr, status) {

	        var	content = '';
	        	content +='<aside class="right-side">';
					content +='<section class="content-header">';
						content +='<div id="dashSubmenu">';
						content +='</div>';
					content +='</section>';
					content +='<section class="content">';
						content +='<div class="grid text-center">';
							content +='<div class="alert alert-warning">';
								content +='<div class="row">';
									content +="Something went wrong! Check your internet and try again";
								content +='</div>';
								content +='<div class="row top-buffer">';
									content +="<strong><a href='#' onclick='msab_getDashboard()' ><i class='fa fa-refresh'></i>&nbsp;&nbsp;Try again</a></strong>";
								content +='</div>';
							content +='</div>';							
						content +='</div>';
					content +='</section>';
				content +='</aside>';

	        	$("#moduleContainer").html(content);
	        	
	        },
	        complete: function (xhr, status) {
	           
	        }
	    });

}