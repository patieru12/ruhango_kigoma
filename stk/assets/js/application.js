//var base_url 	= 'http://academicbridge.rw';

var base_url  = '';

$(document).ajaxError(function(event, jqxhr, settings, exception) {

    if (exception == 'Unauthorized') {

        $('#Model_LoginAgain').modal({
            keyboard: false,
            backdrop: 'static'
        });

        $('#Model_LoginAgain').on('shown.bs.modal', function () {
         
          $('#LoginAgainPassword').focus();

        });
        
    }
});

function ab_page_necessity(){
	
	  $('[data-toggle="tooltip"]').tooltip({
        placement : 'top'
    });

    $('.datepicker').datepicker();

    $('.easyTicker-new-payment').easyTicker({
        visible: 1,
        interval: 4000
    });

}

function formatNumber (num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function INPUTStudent_formatSelection(state) { 

      if (state.id > 0)
      {
        return "<img class='img-circle' style='max-width:50px; max-height:50px;' src='images/student/50/"+state.id+"/0' /> " + state.text;
      }
      else{

        return state.text;
      }
      
  } 

function INPUTStudent_formatResult(state) { 

      if (state.id > 0)
      {
        return "<img class='img-circle' style='max-width:50px; max-height:50px;' src='images/student/50/"+state.id+"/0' /> " + state.text;
      }
      else{
        
        return state.text;
      }
      
}

function ab_moment(specialDate)
{

        specialDate =  moment.unix(specialDate).zone('+0200').format('YYYY-MM-DD');

        var today         = moment();
        today             = today.format('YYYY-MM-DD');

        var moment_date;

        if ( specialDate > today )
        {
          moment_date =   "<span class='text-green'>"+moment(specialDate).countdown(today).toString()+" left </span>";

        }else if( specialDate < today ){

              moment_date = "<span class='text-red'>"+moment(specialDate).fromNow()+"</span>";

        }else{

              moment_date = "<span class='text-blue'>today</span>";

        }

        return moment_date;
}

function ab_moment_plus(specialDate)
{
        specialDate =  moment.unix(specialDate).zone('+0300').format('YYYY-MM-DD');

        var today         = moment();
        today             = today.format('YYYY-MM-DD');

        var moment_date;

        if ( specialDate > today )
        {
          moment_date =   "<span >"+moment(specialDate).countdown(today).toString()+"</span> from today";

        }else if( specialDate < today ){

              moment_date = "<span >"+moment(specialDate).from(today)+"</span>";

        }else{

              moment_date = "<span >today</span>";

        }

        return moment_date;
}

function date_moment(unixtime)
{
  return moment.unix(unixtime).format("DD-MM-YYYY");
}

function date_HM_moment(unixtime)
{
  return moment.unix(unixtime).format("hh:mm - DD/MM/YYYY ");
}

//
  function Select2ClassroomformatSelection(state)
  {
    console.log("Excuted");

      if (state.id > 0)
      {
          var originalOption = $(state.element);
          
          return originalOption.data('yearname') + " " + state.text;

      }
      else{

          return state.text;
      }

  }

  function Select2ClassroomformatResult(state)
  {
      return state.text;
  }

  function ClassroomCongestion(enrolled,capacity)
  {

    var difference = capacity - enrolled;

    if (difference > 0 )
     {
        return  '<span class="text-green">'+difference+' available</span>';

     }else if(difference < 0) 
     {

        return '<span class="text-red">'+Math.abs(difference)+' exceeded</span>';

     }else if(difference == 0 )
     {

      return '<span class="text-blue"><i class="fa"></i>Full</span>';

     }else
     {

     }

  }

  function select2GenerateNewOption(selectID,data)
  {
    $("#"+selectID).select2({
      createSearchChoice:function(term, data) { if ($(data).filter(function() { return this.text.localeCompare(term)===0; }).length===0) {return {id:term, text:term};} },
      multiple: false,
      data: data
    });

  }

  function select2GenerateNewMupliple(selectID,data)
  {
    $("#"+selectID).select2({
      createSearchChoice:function(term, data) { if ($(data).filter(function() { return this.text.localeCompare(term)===0; }).length===0) {return {id:term, text:term};} },
      multiple: true,
      data: data
    });

  }
  
