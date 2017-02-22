var app = angular.module('moreApps');
app.controller('dashboard_milestone_highcharts', dashboard_milestone_highcharts);

function dashboard_milestone_highcharts($scope, $http, $filter){
	$scope.filter_modal = "#filter_highchartModal";
	$scope._error_filter_date =  "";
	$scope.isopen_date_error = false;
	$scope.isopen_display_option = false;
	$scope.ajax_settings_url = 'graph/ajax_get_graph_settings';
	$scope.colors = {
		green :{
			borderColor : Highcharts.getOptions().colors[2],
			dataColor : Highcharts.getOptions().colors[1],
			pane_bg_color : Highcharts.Color(Highcharts.getOptions().colors[2]).setOpacity(0.3).get(),			
		},
		amber : {
			borderColor : Highcharts.getOptions().colors[6],
			dataColor : Highcharts.getOptions().colors[1],
			pane_bg_color : Highcharts.Color(Highcharts.getOptions().colors[6]).setOpacity(0.3).get(),
		},
		red : {
			borderColor : Highcharts.getOptions().colors[8],
			dataColor : Highcharts.getOptions().colors[1],
			pane_bg_color : Highcharts.Color(Highcharts.getOptions().colors[8]).setOpacity(0.3).get(),
		},
		grey : {
			borderColor : Highcharts.Color(Highcharts.getOptions().colors[1]).setOpacity(0.5).get(),
			dataColor : Highcharts.getOptions().colors[1],
			pane_bg_color : Highcharts.Color(Highcharts.getOptions().colors[1]).setOpacity(0.3).get(),
		}
	}
	$scope.title_ext_option = {
		style: {
			fontSize: '16px'
		}
    };
	$scope.chart_ext_option = {
            events: {
				load: function(event) {
					 // Move icon
					this.renderer.path(['M', -6, 0, 'L', 6, 0, 'M', 0, -6, 'L', 6, 0, 0, 6])
						.attr({
							'stroke': '#303030',
							'stroke-linecap': 'round',
							'stroke-linejoin': 'round',
							'stroke-width': 2,
							'zIndex': 10
						})
						.translate(140, 16)
						.add(this.series[0].group);

				}
			} 
        };
		
	$scope.gauges_options = {
		tooltip: {
            borderWidth: 0,
            backgroundColor: 'none',
            shadow: false,
            style: {
                fontSize: '12px'
            },
            pointFormat: '{series.name}<br><span style="font-size:2em; color: {point.color}; font-weight: bold">{point.y}%</span>',
            positioner: function (labelWidth, labelHeight) {
                return {
                    x: 150 - labelWidth / 2,
                    y: 125
                };
            }
        },

        pane: {
            startAngle: 0,
            endAngle: 360,
            background: [
			{ 
				outerRadius: '112%',
                innerRadius: '88%',
               /*  outerRadius: '87%',
                innerRadius: '63%', */
                backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[2]).setOpacity(0.3).get(),
                borderWidth: 0
            }
			]
        },
		exporting: {
			enabled: false
		},
        yAxis: {
            min: 0,
            max: 100,
            lineWidth: 0,
            tickPositions: []
        },

        plotOptions: {
            solidgauge: {
                borderWidth: '26px',
                dataLabels: {
                    enabled: false
                },
                linecap: 'round',
                stickyTracking: false
            }
        },
		credits: {
			enabled: false
		},
        series: [
		{
            name: " ",
            borderColor: Highcharts.getOptions().colors[2],
           data: [{
                color: Highcharts.getOptions().colors[1],
                radius: '100%',
                innerRadius: '100%',
                y: 0
				
            }]
        }
		]
    };
	
	$scope.get_milestones_as_onload = function()
	{
		var kpi_permission_name = $('#kpi_permission_name').val();
		var milestone_permission_name = $('#milestone_permission_name').val();
		if( kpi_permission_name == "hidden" &&  milestone_permission_name != "hidden")
		{
			$scope.get_milestones_highcharts();	
		}
	}
	
	$scope.get_milestones_highcharts = function (){
		var data = {csrf_gd : Cookies.get('csrf_gd')};
		$("#milestone-highchart").html('');
		$.ajax({
			type : "POST",
			dataType : "json",
			data : data,
			url : base_url + "index.php/milestone/ajax_milestone_highchart",
			error: function(xhr, status, error) {
				console.log("error");
			},
			success: function(response)
			{		
				var charts = [];
				var result = response.result;
				var count = response.count;
				var milestones = response.milestones;
				
				
				if(count > 0 ){
					var x = 0;
					while(x < count){
						var highchart = milestones[x];
						var html = highchart.html;
						var options = highchart.options;
						var duedate_name = highchart.duedate_name;
						var milestone_status = highchart.milestone_status;
						var highchart_id = "#milestone_-" + highchart.id;
						
						options.title['text'] = options.title['text'] + duedate_name;
						
						/* var new_chart_opt = angular.extend($scope.chart_ext_option, options.chart); */
						var new_title_opt = angular.extend($scope.title_ext_option, options.title);
						var bg_color = highchart.bg_color; 
						
						switch(bg_color){
							case "green" : 
								var borderColor = $scope.colors['green']['borderColor'];
								var pane_bg_color = $scope.colors['green']['pane_bg_color'];
								break;
							case "amber" : 
								var borderColor = $scope.colors['amber']['borderColor'];
								var pane_bg_color = $scope.colors['amber']['pane_bg_color'];
								break;
							case "red" : 
								var borderColor = $scope.colors['red']['borderColor'];
								var pane_bg_color = $scope.colors['red']['pane_bg_color'];
								break;
							default :
								var borderColor = $scope.colors['grey']['borderColor'];
								var pane_bg_color = $scope.colors['grey']['pane_bg_color'];
							break;
						}
						
						/* options.chart = new_chart_opt; */
						options.title = new_title_opt;
						
						
						/* $scope.gauges_options.series[0]['name'] = duedate_name; */
						$scope.gauges_options.series[0]['data'][0]['y'] = milestone_status;
						$scope.gauges_options.series[0]['borderColor'] = borderColor;
						$scope.gauges_options.pane['background'][0]['backgroundColor'] = pane_bg_color;
						
						var merged_options = angular.extend($scope.gauges_options, options);
						
						$("#milestone-highchart").append(html);
						charts[highchart_id] = new Highcharts.Chart(merged_options);
						
						var override_chart = $("#"+options.chart['renderTo']).highcharts();
						point = override_chart.series[0].points[0];
						point.onMouseOver(); // Show the hover marker
						override_chart.tooltip.refresh(point); // Show the tooltip
						override_chart.tooltip.hide = function () {};
						
						x++;
					}
				}
				$("#milestone-highchart").append("<div style='clear:both'></div>");
			}
		});
	} 
	
	$( "#dashboard_main_tablist a[href='#milestone']" ).on('show.bs.tab', function (e) {
		$scope.get_milestones_highcharts();
	}); 
	
}

