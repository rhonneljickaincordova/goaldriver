/* 
this javascript file is used to generate highcharts in dashboard page -> kpi's tab and KPI page -> dashboard tab
*/
$(document).ready(function(){
	/* Filter gauge variables */
	$('#dp_graph_from').datetimepicker();
	$('#dp_graph_to').datetimepicker({
		useCurrent: false /* Important! See issue #1075 */
	});
	
	$("#dp_graph_from").on("dp.change", function (e) {
		$('#dp_graph_to').data("DateTimePicker").minDate(e.date);
	});
	$("#dp_graph_to").on("dp.change", function (e) {
		$('#dp_graph_from').data("DateTimePicker").maxDate(e.date);
	});
	$('.filter_bShowAverage_tooltip').tooltip({placement: "right"});
	$('.filter_bShowBreakdown_tooltip').tooltip({placement: "right"});
	
	/* Filter gauge variables */
	$('#dp_gauge_from').datetimepicker();
	$('#dp_gauge_to').datetimepicker({
		useCurrent: false /* Important! See issue #1075 */
	});
	
	$("#dp_gauge_from").on("dp.change", function (e) {
		$('#dp_gauge_to').data("DateTimePicker").minDate(e.date);
	});
	$("#dp_gauge_to").on("dp.change", function (e) {
		$('#dp_gauge_from').data("DateTimePicker").maxDate(e.date);
	});
});

var app = angular.module('moreApps');
app.controller('kpi_highcharts', kpi_highcharts);

function kpi_highcharts($scope, $http, $filter, $httpParamSerializerJQLike){
	$scope.gauge_graph = [];
	$scope.gauge_graph_options = [];
	
	$scope.filter_graph_modal = "#filter_highchartModal";
	$scope.filter_gauge_modal = "#filter_gaugeModal";
	$scope._error_filter_date =  "";
	$scope.isopen_date_error = false;
	$scope.ajax_graph_users_url = 'kpi_dashboard/ajax_graph_users_dash';
	
	/* filter kpi users dropdown */
	$scope.filter_kpi_users = []; 
	$scope.filter_kpi_usersModel = []; 
	$scope.filter_kpi_usersOptions = [];
	$scope.filter_kpi_usersSettings = {showCheckAll : false, showUncheckAll: false, dynamicTitle: true};
	$scope.filter_kpi_usersTranslation = {buttonDefaultText: "Filter Users", dynamicButtonTextSuffix	 : "Filtered user(s)"};
	
	/* On click kpi tab on Dashboard Page */ 
	$( "#dashboard_main_tablist a[href='#graphs_main']" ).on('show.bs.tab', function (e) {
		$scope.get_highcharts();
	}); 
	
	
	/* on load generate highcharts  */
	$scope.get_highcharts = function (){
		if($('#kpi_permission_name').val() == "hidden")
		{
			return false;
		}
		
		$("#container-highchart").html('');
		var data = {csrf_gd : Cookies.get('csrf_gd')};
		
		$.ajax({
			type : "POST",
			dataType : "json",
			url : base_url + "index.php/kpi_dashboard/ajax_highchart_dash",
			data : data,
			success: function(response)
			{		
				var result = response.result;
				var gauge_count = response.gauge_count;
				var graph_count = response.graph_count;
				
				if(gauge_count > 0){
					var highcharts_gauges = response.highcharts_gauges;
					var highcharts = highcharts_gauges.highcharts;
					var highchart_count = highcharts_gauges.highchart_gauge_count;
					
					if(highchart_count > 0){
						var x = 0;
						$scope.gauge_graph = [];
						$scope.gauge_graph_options = [];
						while(x < highchart_count){
							var highchart = highcharts[x]; 
							
							loadGauge(highchart);
							x++;
						}
					}
				}
				
				if(graph_count > 0){
					var highcharts_graphs = response.highcharts_graphs;
					var highcharts = highcharts_graphs.highcharts;
					var highchart_count = highcharts_graphs.highchart_graph_count;
					
					if(highchart_count > 0){
						var x = 0;
						while(x < highchart_count){
							var highchart = highcharts[x]; 
							loadGraph(highchart);
							x++;
						}
					}
				}
				
				$("#container-highchart").append("<div style='clear:both'></div>");
			}
		});
	} 
	
	
	
	
	function loadGraph(highchart){
		var charts = [];
		var html = highchart.html;
		var options = highchart.options;
		var highchart_id = "#filter_highchart-" + highchart.id;
		var graph_type = highchart.type.toLowerCase();
		
		switch(graph_type){
			case "line" : 
				$scope.graph_options = window.line_default_options;
			break;
			case "bar" :
				$scope.graph_options = window.bar_default_options;
			break;
			case "pie" :
				$scope.graph_options = window.pie_default_options;
			break;
		}
		var new_tooltip_opt = angular.extend($scope.graph_options.tooltip, options.tooltip);
		options.tooltip = new_tooltip_opt;
		
		var merged_options = angular.extend($scope.graph_options, options);
		
		$("#container-highchart").append(html);
		charts[highchart_id] = new Highcharts.Chart(merged_options);
	}
	
	
	function loadGauge(highchart){
		var html = highchart.html;
		var options = highchart.options;
		var max = highchart.max;
		var total_actuals = highchart.percent;
		var formula_string = highchart.formula_string;
		var highchart_id = "#gauge_highchart-" + highchart.id;
		var bg_color = highchart.bg_color; 
		var valuePrefix = highchart.valuePrefix; 
		var valueSuffix = highchart.valueSuffix; 
		
		$scope.gauges_options = window.new_gauge_default_options;
		
		gauge_color = getGaugeColor(bg_color);
	
		$scope.gauges_options.series[0]['data'][0]['formula_string'] = formula_string;
		$scope.gauges_options.series[0]['data'][0]['y'] = total_actuals;
		$scope.gauges_options.yAxis['max'] = max;
		$scope.gauges_options.series[0]['borderColor'] = gauge_color.borderColor;
		$scope.gauges_options.tooltip['borderColor'] = gauge_color.pane_bg_color;
		$scope.gauges_options.pane['background'][0]['backgroundColor'] = gauge_color.pane_bg_color;
		
		var merged_options = angular.extend($scope.gauges_options, options);
		
		$("#container-highchart").append(html);
		$scope.gauge_graph_options[highchart.id] = options;
		$scope.gauge_graph[highchart.id] = new Highcharts.Chart(merged_options);
		
	}
	
	/* 
	*
	FILTER GRAPH CODE START HERE 
	*
	*/

	/* STEP 1 : Show Filter Graph Modal */
	angular.element($scope.filter_graph_modal).on('show.bs.modal', function(e) {
		var highchart_id = $(e.relatedTarget).data('highchart_id');
		var frequency = $(e.relatedTarget).data('frequency');
		var kpi_id = $(e.relatedTarget).data('kpi_id');
		var show_average = $(e.relatedTarget).data('show_average');
		var show_break_down = $(e.relatedTarget).data('show_break_down');
		
		
		$scope.filter_highchart_id = highchart_id;
		$scope.filter_date_type = frequency;
		$scope.filter_bShowAverage = false;	
		$scope.filter_bShowBreakdown = false;
		
		if(show_average == 1){
			$scope.filter_bShowAverage = true;	
		}
		if(show_break_down == 1){
			$scope.filter_bShowBreakdown = true;
		}
		
		filter_getGraphUsers(highchart_id);
			
		if(frequency == "daily"){
			$('#dp_graph_from').data("DateTimePicker").format("YYYY-MM-DD");
			$('#dp_graph_from').data("DateTimePicker").viewMode("days");
			$('#dp_graph_to').data("DateTimePicker").format("YYYY-MM-DD");
			$('#dp_graph_to').data("DateTimePicker").viewMode("days");
		}else if(frequency == "weekly"){
			$('#dp_graph_from').data("DateTimePicker").format("YYYY-MM-DD");
			$('#dp_graph_from').data("DateTimePicker").viewMode("days");
			$('#dp_graph_to').data("DateTimePicker").format("YYYY-MM-DD");
			$('#dp_graph_to').data("DateTimePicker").viewMode("days");
		}else if(frequency == "monthly"){
			$('#dp_graph_from').data("DateTimePicker").format("YYYY-MM");
			$('#dp_graph_from').data("DateTimePicker").viewMode("months");
			$('#dp_graph_to').data("DateTimePicker").format("YYYY-MM");
			$('#dp_graph_to').data("DateTimePicker").viewMode("months");
		}else if(frequency == "quarterly"){
			$('#dp_graph_from').data("DateTimePicker").format("YYYY-MM");
			$('#dp_graph_from').data("DateTimePicker").viewMode("months");
			$('#dp_graph_to').data("DateTimePicker").format("YYYY-MM");
			$('#dp_graph_to').data("DateTimePicker").viewMode("months");
		}else if(frequency == "yearly"){
			$('#dp_graph_from').data("DateTimePicker").format("YYYY");
			$('#dp_graph_from').data("DateTimePicker").viewMode("years");
			$('#dp_graph_to').data("DateTimePicker").format("YYYY");
			$('#dp_graph_to').data("DateTimePicker").viewMode("years");
		}
		$('#dp_graph_from').data("DateTimePicker").clear();
		$('#dp_graph_to').data("DateTimePicker").clear();
	});
	
	/* STEP 2 : Filter graph before ajax */
	$scope.filter_graph = function(){
		$scope.filter_date_from = $('#filter_date_text-from').val();
		$scope.filter_date_to = $('#filter_date_text-to').val();
		var filter_date = false;
		var date_type = "";
		var	highchart_id = $scope.filter_highchart_id;
		var from_date = $scope.filter_date_from; 
		var	to_date = $scope.filter_date_to; 
		var	show_average = $scope.filter_bShowAverage; 
		var	show_break_down = $scope.filter_bShowBreakdown; 
			
		if($scope.filter_date_from != "" && $scope.filter_date_to != ""){
			filter_date = true;
			$scope._error_filter_date =  "";
			$scope.isopen_date_error = false;
			
			switch($scope.filter_date_type){
				case "daily" : 
						date_type = "day";
					break;
				case "weekly" : 
						date_type = "week";
					break;
				case "monthly" : 
						date_type = "month"; 
						from_date = $scope.filter_date_from + '-01'; 
						to_date = $scope.filter_date_to + '-01'; 
					break;					
				case "quarterly" : 
						date_type = "quarter";
						from_date = $scope.filter_date_from +'-01';
						to_date = $scope.filter_date_to +'-01'; 
					break;
				case "yearly" : 
						date_type = "year"; 
						from_date = $scope.filter_date_from +'-02-01'; 
						to_date = $scope.filter_date_to +'-02-01'; 
					break;		
			}					
		}
		
		/* error capture */
		if($scope.filter_date_from == "" && $scope.filter_date_to == ""){
			$scope._error_filter_date =  "Dates are required.";
				$scope.isopen_date_error = true;
		}else if($scope.filter_date_from == "" || $scope.filter_date_to == ""){
			if($scope.filter_date_from == ""){
				$scope._error_filter_date =  "From date is required.";
				$scope.isopen_date_error = true;
			}else if($scope.filter_date_to == ""){
				$scope._error_filter_date =  "To date is required.";
				$scope.isopen_date_error = true;
			}
		}
		
		if($scope.isopen_date_error == false){
			var users = [];
			
			users = $scope.filter_kpi_usersModel;
			var data = {
					users : users, 
					date_type : date_type, 
					from_date : from_date, 
					to_date : to_date,
					highchart_id : highchart_id, 
					show_break_down : show_break_down, 
					show_average : show_average
				};
				
			$scope.process_filter_graph(data);		
		}
	}
	
	/* STEP 3 : load filtered graph */
	$scope.process_filter_graph = function(unformatted_data){
		var date_type = unformatted_data.date_type;
		var highchart_id = unformatted_data.highchart_id;
		var from_date = unformatted_data.from_date;
		var to_date = unformatted_data.to_date;
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var data = angular.extend(unformatted_data, csrf_object);
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi_dashboard/ajax_filter_graph_dash',
		        data    :  $httpParamSerializerJQLike(data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			var result = response.result;
			var response_series = response.options.series;
			var series_count = response_series.length;
			var graph_type = response.type;
			
			if(result == "success"){
				var chart = $('#highchart-'+highchart_id).highcharts();
				var seriesLength = chart.series.length;
				
				for(var i = seriesLength -1; i > -1; i--) {
					chart.series[i].remove();
				}
				
				if($filter('lowercase')(graph_type)  == "line" || $filter('lowercase')(graph_type) == "bar"){
					var categories = response.options.xAxis.categories;
					chart.xAxis[0].setCategories(categories);					
				}else if($filter('lowercase')(graph_type)  == "pie - single kpi"){
					var new_title = response.options.title;
					chart.setTitle(new_title);					
				}
				
				var x = 0;
				while(x < series_count){
					chart.addSeries(response_series[x]);	
					x++;
				}
				
				$($scope.filter_graph_modal).modal('hide');
			}
		});
	}
	
	/* 
	*
	FILTER GAUGE CODE START HERE 
	*
	*/

	/* STEP 1 : Show Filter GAUGE Modal */
	angular.element($scope.filter_gauge_modal).on('show.bs.modal', function(e) {
		var gauge_id = $(e.relatedTarget).data('gauge_id');
		var date_type = $(e.relatedTarget).data('date_type');
		var kpi_id = $(e.relatedTarget).data('kpi_id');
		var show_average = $(e.relatedTarget).data('show_average');
		var show_break_down = $(e.relatedTarget).data('show_break_down');
		
		$scope.filter_gauge_id = gauge_id;
		$scope.filter_gauge_date_type = date_type;
		
		if(date_type == "daily"){
			$('#dp_gauge_from').data("DateTimePicker").format("YYYY-MM-DD");
			$('#dp_gauge_from').data("DateTimePicker").viewMode("days");
			$('#dp_gauge_to').data("DateTimePicker").format("YYYY-MM-DD");
			$('#dp_gauge_to').data("DateTimePicker").viewMode("days");
		}else if(date_type == "weekly"){
			$('#dp_gauge_from').data("DateTimePicker").format("YYYY-MM-DD");
			$('#dp_gauge_from').data("DateTimePicker").viewMode("days");
			$('#dp_gauge_to').data("DateTimePicker").format("YYYY-MM-DD");
			$('#dp_gauge_to').data("DateTimePicker").viewMode("days");
		}else if(date_type == "monthly"){
			$('#dp_gauge_from').data("DateTimePicker").format("YYYY-MM");
			$('#dp_gauge_from').data("DateTimePicker").viewMode("months");
			$('#dp_gauge_to').data("DateTimePicker").format("YYYY-MM");
			$('#dp_gauge_to').data("DateTimePicker").viewMode("months");
		}else if(date_type == "quarterly"){
			$('#dp_gauge_from').data("DateTimePicker").format("YYYY-MM");
			$('#dp_gauge_from').data("DateTimePicker").viewMode("months");
			$('#dp_gauge_to').data("DateTimePicker").format("YYYY-MM");
			$('#dp_gauge_to').data("DateTimePicker").viewMode("months");
		}else if(date_type == "yearly"){
			$('#dp_gauge_from').data("DateTimePicker").format("YYYY");
			$('#dp_gauge_from').data("DateTimePicker").viewMode("years");
			$('#dp_gauge_to').data("DateTimePicker").format("YYYY");
			$('#dp_gauge_to').data("DateTimePicker").viewMode("years");
		}
		$('#dp_gauge_from').data("DateTimePicker").clear();
		$('#dp_gauge_to').data("DateTimePicker").clear();
	});
	
	/* STEP 2 : Filter GAUGE before ajax */
	$scope.filter_guage = function(){
		$scope.filter_gauge_date_from = $('#dp_gauge_text-from').val();
		$scope.filter_gauge_date_to = $('#dp_gauge_text-to').val();
		
		var filter_date = false;
		var date_type = "";
		var	gauge_id = $scope.filter_gauge_id;
		var from_date = $scope.filter_gauge_date_from; 
		var	to_date = $scope.filter_gauge_date_to; 
			
		if($scope.filter_gauge_date_from != "" && $scope.filter_gauge_date_to != ""){
			filter_date = true;
			$scope._error_filter_gauge_date =  "";
			$scope.isopen_gauge_date_error = false;
			
			switch($scope.filter_gauge_date_type){
				case "daily" : 
						date_type = "day";
					break;
				case "weekly" : 
						date_type = "week";
					break;
				case "monthly" : 
						date_type = "month"; 
						from_date = $scope.filter_gauge_date_from + '-01'; 
						to_date = $scope.filter_gauge_date_to + '-01'; 
					break;					
				case "quarterly" : 
						date_type = "quarter";
						from_date = $scope.filter_gauge_date_from +'-01';
						to_date = $scope.filter_gauge_date_to +'-01'; 
					break;
				case "yearly" : 
						date_type = "year"; 
						from_date = $scope.filter_gauge_date_from +'-02-01'; 
						to_date = $scope.filter_gauge_date_to +'-02-01'; 
					break;		
			}					
		}
		
		/* error capture */
		if($scope.filter_gauge_date_from == ""){
			$scope._error_filter_gauge_date =  "From date is required.";
			$scope.isopen_gauge_date_error = true;
		}else if($scope.filter_gauge_date_to == ""){
			$scope._error_filter_gauge_date =  "To date is required.";
			$scope.isopen_gauge_date_error = true;
		}
		
		
		if($scope.isopen_gauge_date_error == false){
			var data = {
				date_type : date_type, 
				from_date : from_date, 
				to_date : to_date,
				gauge_id : gauge_id 
			};
				
			$scope.process_filter_gauge(data);		
		}
	}
	
	/* STEP 3 : load filtered GAUGE */
	$scope.process_filter_gauge = function(unformatted_data){
		var gauge_id = unformatted_data.gauge_id;
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var data = angular.extend(unformatted_data, csrf_object);
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi_dashboard/ajax_filter_gauge_dash',
		        data    :  $httpParamSerializerJQLike(data), /* forms user object */
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			var result = response.result;
			
			if(result == "success"){
				reloadGauge(gauge_id, response.gauge);
			}
			$($scope.filter_gauge_modal).modal('hide');
		});
	}
	
	/* STEP 4 : RELOAD GAUGE WITH NEW DATA */
	function reloadGauge(gauge_id, gauge){
		var options = $scope.gauge_graph_options[gauge_id];
		var bg_color = gauge.highcharts[0].bg_color;
		var gauge_color = getGaugeColor(bg_color); 
		var total_actuals = gauge.highcharts[0].percent;
		var formula_string = gauge.highcharts[0].formula_string;
		var max = gauge.highcharts[0].max;		
		var gauges_options = window.new_gauge_default_options;
		
		$("#gauge_date_prev-"+gauge_id).val(gauge.highcharts[0].rf_from_date);
		$("#gauge_date_next-"+gauge_id).val(gauge.highcharts[0].rf_to_date);
		$("#gauge_date_string-"+gauge_id).text(gauge.highcharts[0].date_string);
		
		
		gauges_options.title['text'] = options.title['text'];
		gauges_options.series[0]['data'][0]['y'] = total_actuals;
		gauges_options.series[0]['data'][0]['formula_string'] = formula_string;
		gauges_options.yAxis['max'] = max;
		gauges_options.series[0]['borderColor'] = gauge_color.borderColor;
		gauges_options.pane['background'][0]['backgroundColor'] = gauge_color.pane_bg_color;
		gauges_options.tooltip['borderColor'] = gauge_color.pane_bg_color;
		
		var merged_options = angular.extend(gauges_options, options);
		new Highcharts.Chart(merged_options);
	}
	
	/* STEP 5 : load PREV GAUGE DATA !!!! */
	$("body").on("click", ".hg_gauge_prev_btn", function(){
		var gauge_id = $(this).attr('data-gauge_id');
		var prev_date = $("#gauge_date_prev-"+gauge_id).val();
		var next_date = $("#gauge_date_next-"+gauge_id).val();
		var frequency = $("#gauge_date_frequency-"+gauge_id).val();
		var data = {
				action : 'get_gauge_filter_data', 
				gauge_id : gauge_id, 
				prev_date : prev_date, 
				next_date : next_date, 
				frequency : frequency, 
				direction : 'prev', 
				csrf_gd : Cookies.get('csrf_gd')
		};
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi_dashboard/ajax_filter_gauge_v3_dash',
		        data    :  $httpParamSerializerJQLike(data),
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			var result = response.result;
			
			if(result == "success"){
				reloadGauge(gauge_id, response.gauge);
			}
			$($scope.filter_gauge_modal).modal('hide');
		});
	});
	/* STEP 5 : load NEXT GAUGE DATA !!!! */
	$("body").on("click", ".hg_gauge_next_btn", function(){
		var gauge_id = $(this).attr('data-gauge_id');
		var prev_date = $("#gauge_date_prev-"+gauge_id).val();
		var next_date = $("#gauge_date_next-"+gauge_id).val();
		var frequency = $("#gauge_date_frequency-"+gauge_id).val();
		var data = {
				action : 'get_gauge_filter_data', 
				gauge_id : gauge_id, 
				prev_date : prev_date, 
				next_date : next_date, 
				frequency : frequency, 
				direction : 'next', 
				csrf_gd : Cookies.get('csrf_gd')
		};
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi_dashboard/ajax_filter_gauge_v3_dash',
		        data    :  $httpParamSerializerJQLike(data),
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			var result = response.result;
			
			if(result == "success"){
				reloadGauge(gauge_id, response.gauge);
			}
			$($scope.filter_gauge_modal).modal('hide');
		});
	});
	
	/* 
	*
	COMPLEMENTING CODE HERE
	*
	*/
	function getGaugeColor(bg_color){
		var color = { 
			borderColor : "",
			pane_bg_color : ""
		};
		
		switch(bg_color){
			case "green" : 
				color.borderColor = window.gauge_default_colors['green']['borderColor'];
				color.pane_bg_color = window.gauge_default_colors['green']['pane_bg_color'];
				break;
			case "amber" : 
				color.borderColor = window.gauge_default_colors['amber']['borderColor'];
				color.pane_bg_color = window.gauge_default_colors['amber']['pane_bg_color'];
				break;
			case "red" : 
				color.borderColor = window.gauge_default_colors['red']['borderColor'];
				color.pane_bg_color = window.gauge_default_colors['red']['pane_bg_color'];
				break;
			default :
				color.borderColor = window.gauge_default_colors['grey']['borderColor'];
				color.pane_bg_color = window.gauge_default_colors['grey']['pane_bg_color'];
			break;
		}
		
		return color;
	}
	
	function filter_getGraphUsers(graph_id){
		var data = {	
				action : "get_graph_users",
				graph_id: graph_id,
				csrf_gd : Cookies.get('csrf_gd')
			};	
		$scope.file =  $http({
			method  : 'POST',
			url     : $scope.ajax_graph_users_url,
			data    :  $httpParamSerializerJQLike(data), //forms user object
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				var settings = response.settings;
				var data_users = response['users'];
				var users_count = response.users_count;
				if(users_count > 0){
					var x = 0;
					var users = [];
					var assigned_users = [];
					while(x < users_count){	
						
						var name = data_users[x]['first_name']  + " "+ data_users[x]['last_name'];
						users.push({id: data_users[x]['user_id'], label :  name});
						if(data_users[x]['is_graph_user'] == true){
							assigned_users.push({id:data_users[x]['user_id']});	
						}
						x++;
						
					}
					$scope.filter_kpi_usersTitle = "Users"; 
					$scope.filter_kpi_usersOptions = users;
					$scope.filter_kpi_usersModel = assigned_users;
					$scope.filter_statusFilterDisabled = false;
					
					
				}else{
					$scope.filter_kpi_usersTitle = "No assigned users";
					$scope.filter_kpi_usersOptions = [];
					$scope.filter_kpi_usersModel = [];
					$scope.filter_statusFilterDisabled = true;
					
				}
			}
		});	
	}
	
	
	$scope.onFilterUserChange = function(user){
		var users_model_count = $scope.filter_kpi_usersModel.length;
		var users_options_count = $scope.filter_kpi_usersOptions.length;
		if(users_model_count  == 0){
			alert("Minimum 1 user to filter.");
			$scope.filter_kpi_usersModel.push({id:user.id});
		}
	}	
	
	
	
}

