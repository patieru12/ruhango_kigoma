var base_url  = '';
var page = 1; //track user scroll as page number, right now page number is 1
var requestSent = 0;
//load_more(page); //initial content load
$(window).scroll(function() { //detect page scroll
    if($(window).scrollTop() + $(window).height() >= $(document).height()) { //if user scrolled from top to bottom of the page
        if($('.ajax-loading').html() != 'No more records!'){
        	page++; //page number increment
        }
        load_more(page); //load content   
    }
});

function load_more(page){
	console.log("Page Number " + page);
	if($('.ajax-loading').html() == 'No more records!'){
    	page--; //page number increment
    	return;
    }
	if(requestSent == 1)
		return;
	requestSent = 1;
	// console.log("Load More Triggered");
	$.ajax(
        {
            url: base_url + "ABParents/LoadMore?page=" + page,
            type: "GET",
            datatype: "html",
            beforeSend: function()
            {
                $('.ajax-loading').show();
            }
        })
        .done(function(data)
        {
        	requestSent = 0;
            if(data.length == 0){
            	console.log(data.length);
               
                //notify user if nothing to load
                $('.ajax-loading').html("No more records!");

                return;
            }
            $('.ajax-loading').hide(); //hide loading animation once data is received
            $("#DataResults").append(data); //append data into #results element          
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
        	$('.ajax-loading').hide();
        	requestSent = 0;
            alert('No response from server');
        });
}

function msparents_getDashboard() {

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

		var url 			 = base_url+"ABParents/Dashboard";

		 $.ajax({
	        url: url ,
	        type: "GET",
	        dataType: "html",
	        success: function (data) {
	            
	            $("#moduleContainer").html(data);
	            load_more(page);

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
									content +="<strong><a href='#' onclick='msparents_getDashboard()' ><i class='fa fa-refresh'></i>&nbsp;&nbsp;Try again</a></strong>";
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