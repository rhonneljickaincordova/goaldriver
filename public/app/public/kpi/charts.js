$(document).ready(function(){
	window.Kpi_data_days_list = $("#kpi_calendar-days").DataTable({
		"bSort" : false,	
		"aoColumnDefs":[
			{	
				"aTargets": [ 0 ],
				className: "kpi_id", 

			},
			{	"aTargets": [ 1 ]	},
			{ 	"aTargets": [ 2 ]	},
			{ 	
				"aTargets": [ 3, 4, 5, 6, 7 ],	
				"mRender": function(data, type, row){
					var kpi_id = row[0];
					angular.element(this).parents('td').addClass(kpi_id+"_"+data.date);
					
					var actual_val = (data.actual != null ? data.actual : '');
					var target_val = (data.target != null ? data.target : '');
					var show_target = (data.show_target === true ? '' : 'hidden')
					var actual_text = "<input type='text' class='form-control actual' value='"+actual_val+"' data-val='"+actual_val+"' autocomplete='off'>";
					var target_text = "<input type='text' style='margin-top:3px;' class='form-control target "+show_target+"' value='"+target_val+"' autocomplete='off'>";
					
					return actual_text + target_text;
				},
				"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
					var kpi_id = oData[0];
					$(nTd).attr('id',kpi_id+"_"+sData.date);
				}
			},
			{ 	"aTargets": [ 8 ]	}
		],
		"paginate" :false,
		"filter" :false,
		"dom" : "t",
	});
	
});


var app = angular.module('moreApps');

app.controller('kpi_data', kpi_data);
function kpi_data($scope, $http){
	$scope.changed_data_days = {};
	
	/*
	**
	**KPI Data-Days--------------------------------------------------------
	**
	*/
	$( "#kpis_tablist" ).on('show.bs.tab', function (e) {
		$scope.current_kpi_data_tab = angular.element(e.target).attr('id');
		
		if($scope.current_kpi_data_tab == "kpi_data_tab-li"){
			$('#kpidata_tablist a[href="#kpi_data-daily"]').tab('show');
		}
		$scope.reload_day_data();
	});
	$( '#kpidata_tablist a[href="#kpi_data-daily"]' ).on('show.bs.tab', function (e) {
		console.log('d');
		$scope.reload_day_data();
	});
		
	$scope.reload_day_data = function(){
		var last_day = angular.element('#last_day').val();
		var data = angular.toJson({
			last_day: last_day,
			date_type : "day",
			process_type : "current"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				Kpi_data_days_list.clear().draw();
				$scope.entries = response.entries;
				angular.forEach($scope.entries, function(entry){
					var x = 0;
					var actual_target = [];
					while(x < 7){
						actual_target.push({actual:entry.actuals[x], target:entry.targets[x], show_target:entry.show_target, date:response.actual_dates[x]});
						x++;
					}
					var rowNode = Kpi_data_days_list
						.row.add( 
							[ entry.kpi_id, entry.name, '', actual_target[0], actual_target[1], actual_target[2], actual_target[3], actual_target[4], ''] );
				});
				$scope.changed_data_days = {};
				Kpi_data_days_list.draw();
			}
			
		});	
	}
	/* Get Previos Day KPI Data */
	$scope.prev_day_data = function(){
		var start_day = angular.element('#start_day').val();
		
		var data = angular.toJson({
			start_day: start_day,
			date_type : "day",
			process_type : "prev"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				Kpi_data_days_list.clear().draw();
				angular.element('#start_day').val(response.kpi_dates['start_day']['date']);
				angular.element('.kpi_days_date').last().remove();
				var last_day = angular.element('.kpi_days_date').last().find('.day_val').val();
				angular.element('#last_day').val(last_day);
				var text_day = response.kpi_dates['start_day']['formatted_date'];
				if(response.kpi_dates['start_day']['is_today'] == true){
					text_day = "Today";
				}else if(response.kpi_dates['start_day']['is_yesterday'] == true){
					text_day = "Yesterday";
				}
				var text = "<th class='kpi_days_date'>"+text_day+'<input type="hidden" value="'+response.kpi_dates['start_day']['date']+'" class="day_val">'+"</th>";
				angular.element('.kpi_days_date').first().before(text);
				
				$scope.entries = response.entries;
				angular.forEach($scope.entries, function(entry){
					var x = 0;
					var actual_target = [];
					while(x < 7){
						actual_target.push({actual:entry.actuals[x], target:entry.targets[x], show_target:entry.show_target, date:response.actual_dates[x]});
						x++;
					}
					var rowNode = Kpi_data_days_list
						.row.add( 
							[ entry.kpi_id, entry.name, '', actual_target[0], actual_target[1], actual_target[2], actual_target[3], actual_target[4], ''] );
				});
				$scope.changed_data_days = {};
				Kpi_data_days_list.draw();
			}
			
		});	
		
	}
	
	/* Get Next Day KPI Data */
	$scope.next_day_data = function(){
		var last_day = angular.element('#last_day').val();
		
		var data = angular.toJson({
			last_day: last_day,
			date_type : "day",
			process_type : "next"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				
				Kpi_data_days_list.clear().draw();
				angular.element('#last_day').val(response.kpi_dates['last_day']['date']);
				angular.element('.kpi_days_date').first().remove();
				var start_day = angular.element('.kpi_days_date').first().find('.day_val').val();
				angular.element('#start_day').val(start_day);
				
				var text_day = response.kpi_dates['last_day']['formatted_date'];
				if(response.kpi_dates['last_day']['is_today'] == true){
					text_day = "Today";
				}else if(response.kpi_dates['last_day']['is_yesterday'] == true){
					text_day = "Yesterday";
				}
				var text = "<th class='kpi_days_date'>"+text_day+'<input type="hidden" value="'+response.kpi_dates['last_day']['date']+'" class="day_val">'+"</th>";
				angular.element('.kpi_days_date').last().after(text);
				
				$scope.entries = response.entries;
				angular.forEach($scope.entries, function(entry){
					var x = 0;
					var actual_target = [];
					while(x < 7){
						actual_target.push({actual:entry.actuals[x], target:entry.targets[x], show_target:entry.show_target, date:response.actual_dates[x]});
						x++;
					}
					var rowNode = Kpi_data_days_list
						.row.add( 
							[ entry.kpi_id, entry.name, '', actual_target[0], actual_target[1], actual_target[2], actual_target[3], actual_target[4], ''] );
				});
				$scope.changed_data_days = {};
				Kpi_data_days_list.draw();
			}
		});	
	}
	/*
	*
	Process kpi data - Days
	*
	*/
	$("body").on('change','.actual', function(){
		var column_id = angular.element(this).parents('td').attr('id');
		
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		if($scope.changed_data_days[column_id] != undefined){
			console.log('existing');
			var data_value = $scope.changed_data_days[column_id];
			
			if(data_value.old_val == new_val){
				delete $scope.changed_data_days[column_id];
			}else{
				$scope.changed_data_days[column_id] = {old_val:old_val, new_val:new_val};
			}
			
		}else{
			$scope.changed_data_days[column_id] = {old_val:old_val, new_val:new_val};
		}
	});
	
	$scope.KpiData_SaveChanges_days = function(){
		if(!$.isEmptyObject($scope.changed_data_days)){
			console.log('has changes');
			
			var data = angular.toJson({
				action : 'kpi_data_save_changes',
				date_type : 'day',
				data : $scope.changed_data_days
			});
			
			$scope.file =  $http({
					method  : 'POST',
					url     : 'kpi/ajax_save_kpi_data',
					data    :  data, //forms user object
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				console.log(response);
			});	
		
		}else{
			console.log('no changes');
		}
		
	}
									
	/*
	**
	**KPI Data-Weeks--------------------------------------------------------
	**
	*/
	/* Get Next Week KPI Data */
	/* $scope.next_week_data = function(){
		var last_week = angular.element('#last_week').val();
		
		var data = angular.toJson({
			last_week: last_week,
			type : "get_next_week"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			console.log(response);
			if(response.result == "success"){
				angular.element('#last_day').val(response.kpi_data['last_day']['date']);
				angular.element('.kpi_days_date').first().remove();
				var start_day = angular.element('.kpi_days_date').first().find('.day_val').val();
				angular.element('#start_day').val(start_day);
				
				var text_day = response.kpi_data['last_day']['formatted_date'];
				if(response.kpi_data['last_day']['is_today'] == true){
					text_day = "Today";
				}else if(response.kpi_data['last_day']['is_yesterday'] == true){
					text_day = "Yesterday";
				}
				var text = "<th class='kpi_days_date'>"+text_day+'<input type="hidden" value="'+response.kpi_data['last_day']['date']+'" class="day_val">'+"</th>";
				angular.element('.kpi_days_date').last().after(text);
			}
			
		});	
	} */
	
	
	/*
	**
	**KPI Data-Years--------------------------------------------------------
	**
	*/
	/* Get Previos Day KPI Data */
	/* $scope.prev_year_data = function(){
		var start_year = angular.element('#start_year').val();
		
		var data = angular.toJson({
			start_year: start_year,
			date_type : "year",
			process_type : "prev"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				angular.element('#start_year').val(response.kpi_data['start_year']['formatted_year']);
				angular.element('.kpi_years_date').last().remove();
				var last_year = angular.element('.kpi_years_date').last().find('.year_val').val();
				angular.element('#last_year').val(last_year);
				var text_year = response.kpi_data['start_year']['year_num'];
				var text = "<th class='kpi_years_date'>"+text_year+'<input type="hidden" value="'+response.kpi_data['start_year']['formatted_year']+'" class="year_val">'+"</th>";
				angular.element('.kpi_years_date').first().before(text);
			}
			
		});	
		
	} */
	/* Get Next Year KPI Data */
	/* $scope.next_year_data = function(){
		console.log('d');
		var last_year = angular.element('#last_year').val();
		
		var data = angular.toJson({
			last_year: last_year,
			date_type : "year",
			process_type : "next"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			console.log(response);
			if(response.result == "success"){
				angular.element('#last_year').val(response.kpi_data['last_year']['formatted_year']);
				angular.element('.kpi_years_date').first().remove();
				var start_year = angular.element('.kpi_years_date').first().find('.year_val').val();
				angular.element('#start_year').val(start_year);
				
				var text_year = response.kpi_data['last_year']['year_num'];
				
				var text = "<th class='kpi_years_date'>"+text_year+'<input type="hidden" value="'+response.kpi_data['last_year']['formatted_year']+'" class="year_val">'+"</th>";
				angular.element('.kpi_years_date').last().after(text);
			}
			
		});	
	} */
	
	/*
	**
	**KPI Data-Quarters--------------------------------------------------------
	**
	*/
	/* Get Previos Quarter KPI Data */
	/* $scope.prev_quarter_data = function(){
		var start_quarter = angular.element('#start_quarter').val();
		var start_quarter_year = angular.element('#start_quarter_year').val();
		
		var data = angular.toJson({
			start_quarter: start_quarter,
			start_quarter_year: start_quarter_year,
			date_type : "quarter",
			process_type : "prev"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				angular.element('#start_quarter').val(response.kpi_data['start_quarter']['quarter_num']);
				angular.element('#start_quarter_year').val(response.kpi_data['start_quarter']['year_num']);
				angular.element('.kpi_quarters_date').last().remove();
				var last_quarter = angular.element('.kpi_quarters_date').last().find('.quarter_val').val();
				var last_quarter_year = angular.element('.kpi_quarters_date').last().find('.quarter_year_val').val();
				angular.element('#last_quarter').val(last_quarter);
				angular.element('#last_quarter_year').val(last_quarter_year);
				var text_quarter = response.kpi_data['start_quarter']['quarter_string'];
				
				var text = "<th class='kpi_quarters_date'>" +
						text_quarter +
						'<input type="hidden" value="'+response.kpi_data['start_quarter']['quarter_num'] +'" class="quarter_val">'+
						'<input type="hidden" value="'+response.kpi_data['start_quarter']['year_num'] +'" class="quarter_year_val">'+
						"</th>";
				angular.element('.kpi_quarters_date').first().before(text);
			}
			
		});	
		
	} */
	/* Get Next Quarter KPI Data */
	/* $scope.next_quarter_data = function(){
		var last_quarter = angular.element('#last_quarter').val();
		var last_quarter_year = angular.element('#last_quarter_year').val();
		
		var data = angular.toJson({
			last_quarter: last_quarter,
			last_quarter_year: last_quarter_year,
			date_type : "quarter",
			process_type : "next"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			console.log(response);
			if(response.result == "success"){
				angular.element('#last_quarter').val(response.kpi_data['last_quarter']['quarter_num']);
				angular.element('#last_quarter_year').val(response.kpi_data['last_quarter']['year_num']);
				angular.element('.kpi_quarters_date').first().remove();
				var start_quarter = angular.element('.kpi_quarters_date').first().find('.quarter_val').val();
				var start_quarter_year = angular.element('.kpi_quarters_date').first().find('.quarter_year_val').val();
				angular.element('#start_quarter').val(start_quarter);
				angular.element('#start_quarter_year').val(start_quarter_year);
				
				var text_quarter = response.kpi_data['last_quarter']['quarter_string'];
				
				var text = "<th class='kpi_quarters_date'>"+
							text_quarter +
							'<input type="hidden" value="'+response.kpi_data['last_quarter']['quarter_num']+'" class="quarter_val">'+
							'<input type="hidden" value="'+response.kpi_data['last_quarter']['year_num']+'" class="quarter_year_val">'+
							"</th>";
				angular.element('.kpi_quarters_date').last().after(text);
			}
			
		});	
	}
	 */
	/*
	**
	**KPI Data-Months--------------------------------------------------------
	**
	*/
	/* Get Previos Month KPI Data */
	/* $scope.prev_month_data = function(){
		var start_month = angular.element('#start_month').val();
		
		var data = angular.toJson({
			start_month: start_month,
			date_type : "month",
			process_type : "prev"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				angular.element('#start_month').val(response.kpi_data['start_month']['formatted_month']);
				angular.element('.kpi_months_date').last().remove();
				var last_month = angular.element('.kpi_months_date').last().find('.month_val').val();
				angular.element('#last_month').val(last_month);
				var text_month = response.kpi_data['start_month']['month_string'];
				var text = "<th class='kpi_months_date'>"+text_month+'<input type="hidden" value="'+response.kpi_data['start_month']['formatted_month']+'" class="month_val">'+"</th>";
				angular.element('.kpi_months_date').first().before(text);
			}
			
		});	
		
	} */
	/* Get Next Year KPI Data */
	/* $scope.next_month_data = function(){
		console.log('d');
		var last_month = angular.element('#last_month').val();
		
		var data = angular.toJson({
			last_month: last_month,
			date_type : "month",
			process_type : "next"
		});
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi/ajax_get_kpi_data',
		        data    :  data, //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			console.log(response);
			if(response.result == "success"){
				angular.element('#last_month').val(response.kpi_data['last_month']['formatted_month']);
				angular.element('.kpi_months_date').first().remove();
				var start_month = angular.element('.kpi_months_date').first().find('.month_val').val();
				angular.element('#start_month').val(start_month);
				
				var text_month = response.kpi_data['last_month']['month_string'];
				
				var text = "<th class='kpi_months_date'>"+text_month+'<input type="hidden" value="'+response.kpi_data['last_month']['formatted_month']+'" class="month_val">'+"</th>";
				angular.element('.kpi_months_date').last().after(text);
			}
			
		});	
	} */
	
}




