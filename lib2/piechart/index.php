<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Pie Chart</title>
		<script type="text/javascript" src="<?= @$path_file ?>changes/jquery.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$("#generate").click(function(e){
			var options = {
				chart: {
	                renderTo: 'container',
	                plotBackgroundColor: null,
	                plotBorderWidth: null,
	                plotShadow: false
	            },
	            title: {
	                text: ' '
	            },
	            tooltip: {
	                formatter: function() {
	                    return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage,2) +' %';
	                }
	            },
	            plotOptions: {
	                pie: {
	                    allowPointSelect: true,
	                    cursor: 'pointer',
	                    dataLabels: {
	                        enabled: true,
	                        color: '#000000',
	                        connectorColor: '#000000',
	                        formatter: function() {
	                            return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
	                        }
	                    }
	                }
	            },
	            series: [{
	                type: 'pie',
	                name: 'PSC CASES',
	                data: []
	            }]
	        }
	        
	        $.getJSON("<?= @$path_file ?>data.php?exam=" + $("#exam").val() + "&date=" + $("#year").val()+'-'+$("#month").val(), function(json) {
				options.series[0].data = json;
	        	chart = new Highcharts.Chart(options);
	        });
	        
	        });
	        
      	});   
		</script>
		<script src="<?= @$path_file ?>changes/highcharts.js"></script>
        <script src="<?= @$path_file ?>changes/exporting.js"></script>
	</head>
	<body>
		<div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
	</body>
</html>
