var app = angular.module('moreApps');

app.controller('kpi_data', kpi_data);
function kpi_data($scope, $http, $httpParamSerializerJQLike){
	$scope.changed_data = {
		'daily' : {},	
		'weekly' : {},	
		'monthly' : {},	
		'quarterly' : {},	
		'yearly' : {}
	};
	
	$scope.day_calendar = '';
	$scope.week_calendar = '';
	$scope.month_calendar = '';
	$scope.quarter_calendar = '';
	$scope.year_calendar = '';
	
	angular.element('#kpi_calendar-daily #mydatepicker-days').on('dp.change', function(d){
		var next_date = $("#mydatepicker_text-days").val();
		if($scope.day_calendar != next_date){
			$scope.process_get_kpi_data({ next_date: next_date, frequency : "daily", process_type : "current", calendar : true});
			$scope.day_calendar = next_date;
		}
	});
	
	angular.element('#kpi_calendar-weekly #mydatepicker-weeks').on('dp.change', function(d){
		var next_date = $("#mydatepicker_text-weeks").val();
		if($scope.week_calendar != next_date){
			$scope.process_get_kpi_data({ next_date: next_date, frequency : "weekly", process_type : "current", calendar : true});
			$scope.week_calendar = next_date;
		}
	});
	angular.element('#kpi_calendar-monthly #mydatepicker-months').on('dp.change', function(d){
		var next_date = $("#mydatepicker_text-months").val();
		if($scope.month_calendar != next_date){
			var next_date = next_date + '-01';
			$scope.process_get_kpi_data({ next_date: next_date, frequency : "monthly", process_type : "current", calendar : true});
			$scope.month_calendar = next_date;
		}
	});
	angular.element('#kpi_calendar-quarterly #mydatepicker-quarters').on('dp.change', function(d){
		next_date = $("#mydatepicker_text-quarters").val();
		if($scope.quarter_calendar != next_date){
			var next_date = next_date + '-01';
			$scope.process_get_kpi_data({ next_date: next_date, frequency : "quarterly", process_type : "current", calendar : true});
			$scope.quarter_calendar = next_date;
		}
	});
	angular.element('#kpi_calendar-yearly #mydatepicker-years').on('dp.change', function(d){
		var next_date = $("#mydatepicker_text-years").val();
		if($scope.year_calendar != next_date){
			next_date = next_date +'-01-01';
			$scope.process_get_kpi_data({ next_date: next_date, frequency : "yearly", process_type : "current", calendar : true});
			$scope.year_calendar = next_date;
		}
	});
	/*
	**
	**KPI Data-Days--------------------------------------------------------
	**
	*/
	
	$( "#kpis_tablist a[href='#kpi_data_tab']" ).on('show.bs.tab', function (e) {
		$scope.current_kpi_data_tab = angular.element(e.target).attr('id');
		
		if($scope.current_kpi_data_tab == "kpi_data_tab-li"){
			$('#kpidata_tablist a[href="#kpi_data-daily"]').tab('show');
			$scope.reload_day_data();
		}
	});
	
	$( '#kpidata_tablist a[href="#kpi_data-daily"]' ).on('show.bs.tab', function (e) {
		$scope.reload_day_data();
	});
	
	/* Reload Day KPI Data */	
	$scope.reload_day_data = function(){
		var prev_date = angular.element('#start_day').val();
		var next_date = angular.element('#last_day').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date: next_date, frequency : "daily", process_type : "current", 'calendar' : false });
	}
	
	/* Get Previos Day KPI Data */
	$scope.prev_day_data = function(){
		var prev_date = angular.element('#start_day').val();
		var next_date = angular.element('#last_day').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date: next_date, frequency : "daily", process_type : "prev", 'calendar' : false });
	}
	
	/* Get Next Day KPI Data */
	$scope.next_day_data = function(){
		var prev_date = angular.element('#start_day').val();
		var next_date = angular.element('#last_day').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date: next_date, frequency : "daily", process_type : "next", 'calendar' : false });
	}
	
	/*
	**
	**KPI Data-Weeks--------------------------------------------------------
	**
	*/
	
	$( '#kpidata_tablist a[href="#kpi_data-weekly"]' ).on('show.bs.tab', function (e) {
		$scope.reload_week_data();
	});
	
	/* Reload Week KPI Data */	
	$scope.reload_week_data = function(){
		var prev_date = angular.element('#start_week').val();
		var next_date = angular.element('#last_week').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date:next_date, frequency : "weekly", process_type : "current", 'calendar' : false });
	}
	
	/* Get Previos Week KPI Data */
	$scope.prev_week_data = function(){
		var prev_date = angular.element('#start_week').val();
		var next_date = angular.element('#last_week').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date:next_date, frequency : "weekly", process_type : "prev", 'calendar' : false });
	}
	
	/* Get Next Week KPI Data */
	$scope.next_week_data = function(){
		var prev_date = angular.element('#start_week').val();
		var next_date = angular.element('#last_week').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date:next_date, frequency : "weekly", process_type : "next", 'calendar' : false });
	}
	
	/*
	**
	**KPI Data-Months--------------------------------------------------------
	**
	*/
	$( '#kpidata_tablist a[href="#kpi_data-monthly"]' ).on('show.bs.tab', function (e) {
		$scope.reload_month_data();
	});
	
	/* Reload Month KPI Data */	
	$scope.reload_month_data = function(){
		var prev_date = angular.element('#start_month').val();
		var next_date = angular.element('#last_month').val();
		var data = { prev_date: prev_date, next_date:next_date, frequency : "monthly", process_type : "current", 'calendar' : false };
		$scope.process_get_kpi_data(data);
	}
	
	/* Get Previos Month KPI Data */	
	$scope.prev_month_data = function(){
		var prev_date = angular.element('#start_month').val();
		var next_date = angular.element('#last_month').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date:next_date, frequency : "monthly", process_type : "prev", 'calendar' : false });
	}
	
	/* Get Next Month KPI Data */	
	$scope.next_month_data = function(){
		var prev_date = angular.element('#start_month').val();
		var next_date = angular.element('#last_month').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date:next_date, frequency : "monthly", process_type : "next", 'calendar' : false });
	}
	/*
	**
	**KPI Data-Quarters--------------------------------------------------------
	**
	*/
	$( '#kpidata_tablist a[href="#kpi_data-quarterly"]' ).on('show.bs.tab', function (e) {
		$scope.reload_quarter_data();
	});
	
	/* Reload Quarter KPI Data */	
	$scope.reload_quarter_data = function(){
		var prev_date = angular.element('#start_quarter').val();
		var next_date = angular.element('#last_quarter').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date: next_date, frequency : "quarterly", process_type : "current", 'calendar' : false });
	}
	
	/* Get Previos Quarter KPI Data */	
	$scope.prev_quarter_data = function(){
		var prev_date = angular.element('#start_quarter').val();
		var next_date = angular.element('#last_quarter').val();
		
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date: next_date, frequency : "quarterly", process_type : "prev", 'calendar' : false });
	}
	
	/* Get Next Quarter KPI Data */	
	$scope.next_quarter_data = function(){
		var prev_date = angular.element('#start_quarter').val();
		var next_date = angular.element('#last_quarter').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date: next_date, frequency : "quarterly", process_type : "next", 'calendar' : false });
	}
	/*
	**
	**KPI Data-Years--------------------------------------------------------
	**
	*/
	$( '#kpidata_tablist a[href="#kpi_data-yearly"]' ).on('show.bs.tab', function (e) {
		$scope.reload_year_data();
	});
	
	/* Reload Year KPI Data */	
	$scope.reload_year_data = function(){
		var prev_date = angular.element('#start_year').val();
		var next_date = angular.element('#last_year').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date:next_date, frequency : "yearly", process_type : "current", 'calendar' : false });
	}
	
	/* Get Previos Year KPI Data */	
	$scope.prev_year_data = function(){
		var prev_date = angular.element('#start_year').val();
		var next_date = angular.element('#last_year').val();
		$scope.process_get_kpi_data({ prev_date: prev_date, next_date:next_date,  frequency : "yearly", process_type : "prev", 'calendar' : false });
	}
	
	/* Get Next Year KPI Data */	
	$scope.next_year_data = function(){
		var prev_date = angular.element('#start_year').val();
		var next_date = angular.element('#last_year').val();
		$scope.process_get_kpi_data({  prev_date: prev_date, next_date:next_date,  frequency : "yearly", process_type : "next", 'calendar' : false });
	}
	
	/*
	*
	PROCESS GET Kpi Data - ALL
	*
	*/
	$scope.process_get_kpi_data = function(tmp_unformatted_data){
		$scope.KpiData_SaveChanges(tmp_unformatted_data.frequency, 'get_new_data', tmp_unformatted_data);
	}
	
	$scope.get_kpi_data = function(tmp_unformatted_data){
		var frequency = tmp_unformatted_data['frequency'];
		var process_type = tmp_unformatted_data['process_type'];
		var csrf_object = { action: "get_kpi_data", csrf_gd : Cookies.get('csrf_gd')};
		var data = angular.extend(tmp_unformatted_data, csrf_object);
		var unformatted_data = tmp_unformatted_data;
		
		$(".dataTables_processing label").text("Loading...");
		$("#kpi_calendar-"+frequency+"_processing").show();
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : 'kpi_data/ajax_get_kpi_data',
		        data    :  $httpParamSerializerJQLike(data), 
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				
				switch(frequency){
					case "daily" : 
						Kpi_data_days_list.clear().draw();
						$scope.entries = response.entries;
						$scope.actual_dates = response.actual_dates;
						
						if(unformatted_data.calendar == true || (unformatted_data.calendar == false && process_type != 'current'))
						{
							
								var kpi_dates = response.dates;
								var kpi_days_dates = angular.element('.kpi_days_date');
							
								angular.element('#start_day').val($scope.actual_dates[0]);
								angular.element('#last_day').val($scope.actual_dates[4]);
								
								var x = 0;
								angular.forEach(kpi_dates.dates, function(kpi_date){
									var text = "<p>"+kpi_date['formatted_date']+"</p>"+'<input type="hidden" value="'+kpi_date['selected_date']+'" class="day_val">';
									angular.element(kpi_days_dates[x]).html(text);
									x++;
								});
						}
						
						angular.forEach($scope.entries, function(kpi){
							var x = 0;
							var actual_target = [];
							angular.forEach($scope.actual_dates, function(date){
								if(kpi.dates[date] != undefined){
									var kpi_data = kpi.dates[date];
									actual_target.push({
												actual:kpi_data.actual, 
												target:kpi_data.target, 
												display : kpi_data.display_inputData, 
												frequency : 'daily', 
												show_target:kpi.show_target, 
												date: date
											});	
								}else{
									actual_target.push({
												actual:"", 
												target:kpi.default_target, 
												show_target: kpi.show_target, 
												date: date,
												display : 0, 
												frequency : 'daily'
									});	
								}
							});
							
							var rowNode = Kpi_data_days_list
								.row.add( 
									[ kpi.kpi_id, kpi.name, '', actual_target[0], actual_target[1], actual_target[2], actual_target[3], actual_target[4], ''] );
						});
						$scope.changed_data['daily'] = {};
						Kpi_data_days_list.draw();
						
						break;
					case "weekly" : 
						Kpi_data_weeks_list.clear().draw();
						$scope.entries = response.entries;
						$scope.actual_dates = response.actual_dates;
						
						if(unformatted_data.calendar == true || (unformatted_data.calendar == false && process_type != 'current'))
						{
							var kpi_dates = response.dates;
							var kpi_weeks_date = angular.element('.kpi_weeks_date');
							
							angular.element('#start_week').val($scope.actual_dates[0]);
							angular.element('#last_week').val($scope.actual_dates[4]);
							
							var x = 0;
							angular.forEach(kpi_dates.dates, function(kpi_date){
								var text = "<p>"+kpi_date['formatted_date']+"</p>"+'<input type="hidden" value="'+kpi_date['selected_date']+'" class="week_val">';
								angular.element(kpi_weeks_date[x]).html(text);
								x++;
							});
						}
						
						angular.forEach($scope.entries, function(kpi){
							var x = 0;
							var actual_target = [];
							angular.forEach($scope.actual_dates, function(date){
								if(kpi.dates[date] != undefined){
									var kpi_data = kpi.dates[date];
									actual_target.push({
												actual:kpi_data.actual, 
												target:kpi_data.target, 
												frequency : 'weekly', 
												show_target:kpi.show_target, 
												date: date
											});	
								}else{
									actual_target.push({
												actual:"", 
												target:kpi.default_target, 
												show_target: kpi.show_target, 
												date: date,
												frequency : 'weekly'
									});	
								}
							});
							var rowNode = Kpi_data_weeks_list
								.row.add( 
									[ kpi.kpi_id, kpi.name, '', actual_target[0], actual_target[1], actual_target[2], actual_target[3], actual_target[4], ''] );
						});
						$scope.changed_data['weekly'] = {};
						Kpi_data_weeks_list.draw();
						break;
					case "monthly" : 
						Kpi_data_months_list.clear().draw();
						$scope.entries = response.entries;
						$scope.actual_dates = response.actual_dates;
						
						if(unformatted_data.calendar == true || (unformatted_data.calendar == false && process_type != 'current'))
						{	
								var kpi_dates = response.dates;
								var kpi_months_date = angular.element('.kpi_months_date');
							
								angular.element('#start_month').val($scope.actual_dates[0]);
								angular.element('#last_month').val($scope.actual_dates[4]);
								
								var x = 0;
								angular.forEach(kpi_dates.dates, function(kpi_date){
									var text = "<p>"+kpi_date['formatted_date']+"</p>"+'<input type="hidden" value="'+kpi_date['selected_date']+'" class="month_val">';
									angular.element(kpi_months_date[x]).html(text);
									x++;
								});
						}
						
						angular.forEach($scope.entries, function(kpi){
							var x = 0;
							var actual_target = [];
							angular.forEach($scope.actual_dates, function(date){
								if(kpi.dates[date] != undefined){
									var kpi_data = kpi.dates[date];
									actual_target.push({
												actual:kpi_data.actual, 
												target:kpi_data.target, 
												frequency : 'monthly', 
												show_target:kpi.show_target, 
												date: date
											});	
								}else{
									actual_target.push({
												actual:"", 
												target:kpi.default_target, 
												show_target: kpi.show_target, 
												date: date,
												frequency : 'monthly'
									});	
								}
							});
							var rowNode = Kpi_data_months_list
								.row.add( 
									[ kpi.kpi_id, kpi.name, '', actual_target[0], actual_target[1], actual_target[2], actual_target[3], actual_target[4], ''] );
						});
						$scope.changed_data['monthly'] = {};
						Kpi_data_months_list.draw();
					
						break;
					case "quarterly" : 
						Kpi_data_quarters_list.clear().draw();
						$scope.entries = response.entries;
						$scope.actual_dates = response.actual_dates;
						
						if(unformatted_data.calendar == true || (unformatted_data.calendar == false && process_type != 'current'))
						{
								var kpi_dates = response.dates;
								var kpi_quarters_date = angular.element('.kpi_quarters_date');
							
								angular.element('#start_quarter').val($scope.actual_dates[0]);
								angular.element('#last_quarter').val($scope.actual_dates[4]);
								
								var x = 0;
								angular.forEach(kpi_dates.dates, function(kpi_date){
									var text = "<p>"+kpi_date['formatted_date']+"</p>"+'<input type="hidden" value="'+kpi_date['selected_date']+'" class="quarter_val">';
									angular.element(kpi_quarters_date[x]).html(text);
									x++;
								});
						}
						
						angular.forEach($scope.entries, function(kpi){
							var x = 0;
							var actual_target = [];
							angular.forEach($scope.actual_dates, function(date){
								if(kpi.dates[date] != undefined){
									var kpi_data = kpi.dates[date];
									actual_target.push({
												actual:kpi_data.actual, 
												target:kpi_data.target, 
												frequency : 'quarterly', 
												show_target:kpi.show_target, 
												date: date
											});	
								}else{
									actual_target.push({
												actual:"", 
												target:kpi.default_target, 
												show_target: kpi.show_target, 
												date: date,
												frequency : 'quarterly'
									});	
								}
							});
							var rowNode = Kpi_data_quarters_list
								.row.add( 
									[ kpi.kpi_id, kpi.name, '', actual_target[0], actual_target[1], actual_target[2], actual_target[3], actual_target[4], ''] );
						});
						$scope.changed_data['quarterly'] = {};
						Kpi_data_quarters_list.draw();
						break;
					case "yearly" : 
						Kpi_data_years_list.clear().draw();
						$scope.entries = response.entries;
						$scope.actual_dates = response.actual_dates;
						
						if(unformatted_data.calendar == true || (unformatted_data.calendar == false && process_type != 'current'))
						{
								var kpi_dates = response.dates;
								var kpi_years_date = angular.element('.kpi_years_date');
							
								angular.element('#start_year').val(response.actual_dates[0]);
								angular.element('#last_year').val(response.actual_dates[4]);
								
								var x = 0;
								angular.forEach(kpi_dates.dates, function(kpi_date){
									var text = "<p>"+kpi_date['formatted_date']+"</p>"+'<input type="hidden" value="'+kpi_date['selected_date']+'" class="year_val">';
									angular.element(kpi_years_date[x]).html(text);
									x++;
								});
						}
						
						angular.forEach($scope.entries, function(kpi){
							var x = 0;
							var actual_target = [];
							angular.forEach($scope.actual_dates, function(date){
								if(kpi.dates[date] != undefined){
									var kpi_data = kpi.dates[date];
									actual_target.push({
												actual:kpi_data.actual, 
												target:kpi_data.target, 
												frequency : 'quarterly', 
												show_target:kpi.show_target, 
												date: date
											});	
								}else{
									actual_target.push({
												actual:"", 
												target:kpi.default_target, 
												show_target: kpi.show_target, 
												date: date,
												frequency : 'quarterly'
									});	
								}
							});
							var rowNode = Kpi_data_years_list
								.row.add( 
									[ kpi.kpi_id, kpi.name, '', actual_target[0], actual_target[1], actual_target[2], actual_target[3], actual_target[4], ''] );
						});
						$scope.changed_data['yearly'] = {};
						Kpi_data_years_list.draw();
						break;
					default : return false; break;
				}
				$(".dataTables_processing").hide();
				
			}
		});	
		get_kpis_count();
	}
	
	
	/************************************************************************************************
	*
	CHANGES KPI DATA ACTUAL AND TARGET
	*
	************************************************************************************************/
	/*
	Changes kpi data : DAILY
	*/
	$("#kpi_calendar-daily").on('change','.actual', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_actual('daily', column_id, old_val, new_val);
	});
	
	$("#kpi_calendar-daily").on('change','.target', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_target('daily', column_id, old_val, new_val);
	});
	/*
	Changes kpi data : WEEKLY
	*/
	$("#kpi_calendar-weekly").on('change','.actual', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_actual('weekly', column_id, old_val, new_val);
	});
	
	$("#kpi_calendar-weekly").on('change','.target', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_target('weekly', column_id, old_val, new_val);
	});
	/*
	Changes kpi data : MONTHLY
	*/
	$("#kpi_calendar-monthly").on('change','.actual', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_actual('monthly', column_id, old_val, new_val);
	});
	
	$("#kpi_calendar-monthly").on('change','.target', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_target('monthly', column_id, old_val, new_val);
	});
	/*
	Changes kpi data : QUARTERLY
	*/
	$("#kpi_calendar-quarterly").on('change','.actual', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_actual('quarterly', column_id, old_val, new_val);
	});
	
	$("#kpi_calendar-quarterly").on('change','.target', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_target('quarterly', column_id, old_val, new_val);
	});
	/*
	Changes kpi data : YEARLY
	*/
	$("#kpi_calendar-yearly").on('change','.actual', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_actual('yearly', column_id, old_val, new_val);
	});
	
	$("#kpi_calendar-yearly").on('change','.target', function()
	{
		var column_id = angular.element(this).parents('td').attr('id');
		var old_val = angular.element(this).data('val');
		var new_val = angular.element(this).val();
		
		$scope.change_data_target('yearly', column_id, old_val, new_val);
	});
	
	
	
	/************************************************************************************************
	*
	SAVE CHANGES KPI DATA ACTUAL AND TARGET
	*
	************************************************************************************************/
	$scope.KpiData_SaveChanges = function(frequency, get_new_data, tmp_unformatted_data){
		if(!$.isEmptyObject($scope.changed_data[frequency])){
			$("#kpi_calendar-"+frequency+"_processing label").text("Saving changes first...");
			var save_data = { 
					action : 'kpi_data_save_changes', 
					frequency : frequency, 
					data : $scope.changed_data[frequency]
				};
			
			saveKpiData('save_only', save_data, get_new_data, tmp_unformatted_data);	
		}else if(get_new_data == 'get_new_data'){
			$scope.get_kpi_data(tmp_unformatted_data);
		}
	}
	
	/*Process kpi Data - ALL*/
	function saveKpiData(save_type, initial_data, get_new_data, tmp_unformatted_data){
		var csrf_object = {action: "save_kpi_data", csrf_gd : Cookies.get('csrf_gd')};
		var data = angular.extend(initial_data, csrf_object);
		
		$scope.file =  $http({
				method  : 'POST',
				url     : 'kpi_data/ajax_save_kpi_data',
				data    :  $httpParamSerializerJQLike(data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			var tmp_data = data;
			if(response.result == "success"){
				var reponse_data = response.islocked_kpis;
				var kpi_count = Object.keys(reponse_data).length;
				var kpi_keys = Object.keys(reponse_data);

				if(kpi_count  > 0 )
				{
					var x = 0;
					while(x < kpi_count)
					{
						var kpi_id = reponse_data[kpi_keys[x]].kpi_id;
						var islocked = reponse_data[kpi_keys[x]].islocked;
						
						if(islocked == true)
						{
							var current_row = angular.element("#kpi_id-"+kpi_id).parents('tr');
							var row_kpi_id = "#kpi_id-"+kpi_id;
							var className = ".islocked";
							var kpi_column = angular.element(row_kpi_id).find(className);
							
							if(kpi_column.length > 0)
							{
								update_column = Kpi_list.cell(kpi_column).data(1); 
							}
						}
						x++;
					}	
				}
				
				$scope.popup_alert_message('btn-success', "Success", response.message, "");
				$scope.changed_data[tmp_data.frequency] = {};
			}else{
				$scope.popup_alert_message('btn-danger', "Error", response.message, "");
			}
			
			if(get_new_data == 'get_new_data'){
				$scope.get_kpi_data(tmp_unformatted_data);
			}
		
		}).error(function(){
			$scope.popup_alert_message('btn-danger', "Error", "Failed to save changes.", "");
			if(get_new_data == 'get_new_data'){
				$scope.get_kpi_data(tmp_unformatted_data);
			}
		});
	}
	
	
	/***************************************************************************************
	*
	CHANGES KPI DATA ACTUAL AND TARGET : FUNCTION PROCESSOR
	*
	****************************************************************************************/
	$scope.change_data_actual = function(frequency, column_id, old_val, new_val){
		if($scope.changed_data[frequency][column_id] != undefined){
			var actual = $scope.changed_data[frequency][column_id]['actual'];
			if(actual != undefined){
				if(actual.old_val == new_val){
					if($scope.changed_data[frequency][column_id]['target'] != undefined){
						delete $scope.changed_data[frequency][column_id]['actual'];
					}else{
						delete $scope.changed_data[frequency][column_id];
					}	
				}else{
					$scope.changed_data[frequency][column_id]['actual'] = {old_val:old_val, new_val:new_val};
				}
			}else{
				$scope.changed_data[frequency][column_id]['actual'] = {old_val:old_val, new_val:new_val};
			}
		}else{
			$scope.changed_data[frequency][column_id] ={actual: {old_val:old_val, new_val:new_val}};
		}
	};	
	$scope.change_data_target = function(frequency, column_id, old_val, new_val){
		if($scope.changed_data[frequency][column_id] != undefined)
		{
			var target = $scope.changed_data[frequency][column_id]['target'];
			if(target != undefined){
				if(target.old_val == new_val){
					if($scope.changed_data[frequency][column_id]['actual'] != undefined){
						delete $scope.changed_data[frequency][column_id]['target'];	
					}else{
						delete $scope.changed_data[frequency][column_id];
					}
				}else{
					$scope.changed_data[frequency][column_id]['target'] = {old_val:old_val, new_val:new_val};
				}
			}else{
				$scope.changed_data[frequency][column_id]['target'] = {old_val:old_val, new_val:new_val};
			}
		}else{
			$scope.changed_data[frequency][column_id] = {target:{old_val:old_val, new_val:new_val}};
		}
	};
	

	/* GET KPI COUNT - ALL */
	function get_kpis_count(){
		var data = {action: "get_kpis_count", csrf_gd : Cookies.get('csrf_gd')};
		$scope.file =  $http({
				method  : 'POST',
				data : $httpParamSerializerJQLike(data),
				url     : 'kpi/ajax_get_kpis_count',
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			var frequency_count = response;
			
			angular.forEach(frequency_count, function(count, key){
				angular.element("#"+key+"-li .badge").text( count );
				if(count > 0){
					angular.element("#"+key+"-li .badge").removeClass("badge_zero");
				}else{
					angular.element("#"+key+"-li .badge").addClass("badge_zero");
				}
			});
		});
		
	}		
	
	$scope.organisation_user_type = angular.element("#organisation_user_type").val();
	
	$scope.popup_alert_message = function(btnClass, title, message, modal_id){
		if(modal_id != ""){
			$.alert({
				title: title,
				content: message,
				confirmButtonClass: btnClass,
				confirm: function(){
					$(modal_id).modal('hide');
				}
			});
		}else{
			$.alert({
				title: title,
				content: message,
				confirmButtonClass: btnClass
			});
		}
	}
	
}




