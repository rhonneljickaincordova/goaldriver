var app = angular.module('moreApps');
app.controller('kpi', kpi);




function kpi($scope, $http, $filter, $httpParamSerializerJQLike){
	/* add kpi users dropdown */
	 $scope.kpi_users = []; 
	 $scope.kpi_usersModel = []; 
	 $scope.kpi_usersOptions = [];
	 $scope.kpi_usersSettings = {showCheckAll : false, showUncheckAll: false, dynamicTitle: true};
	 $scope.kpi_usersTranslation = {buttonDefaultText: "Assign Users", dynamicButtonTextSuffix	 : "Assigned user(s)"};
	 
	 /* edit kpi users dropdown */
	 $scope.edit_kpi_users = []; 
	 $scope.edit_kpi_usersModel = []; 
	 $scope.edit_kpi_usersOptions = [];
	 $scope.edit_kpi_usersSettings = {showCheckAll : false, showUncheckAll: false, dynamicTitle: true};
	 $scope.edit_kpi_usersTranslation = {buttonDefaultText: "Assign Users", dynamicButtonTextSuffix	 : "Assigned user(s)"};
	 
	$scope.add_modal_id = "#add_kpiModal";
	$scope.edit_modal_id = "#edit_kpiModal";
	$scope.delete_modal_id = "#delete_kpiModal";
	
	$scope.ajax_add_url = 'kpi/ajax_add_kpi';
	$scope.ajax_edit_url = 'kpi/ajax_edit_kpi';
	$scope.ajax_delete_url = 'kpi/ajax_delete_kpi';
	$scope.ajax_get_users = 'kpi/ajax_get_users';
	
	$scope.kpi_frequency_disable = false;
	
	$scope.frequencyList = [
		{	value: 'daily', label: 'Daily'	}, 	
		{	value: 'weekly', label: 'Weekly'},
		{	value: 'monthly', label: 'Monthly'	},
		{	value: 'quarterly', label: 'Quarterly'	},
		{	value: 'yearly', label: 'Yearly'	}
	];
	
	$scope.formatList = [
		{	value: '1', label: '1,234'	}, 	
		{	value: '2', label: '1,234.56'	}, 	
		{	value: '3', label: '12%'	}, 	
		{	value: '4', label: '12.34%'	}, 	
		{	value: '5', label: '₱1,234.56'	}, 	
		{	value: '6', label: '$1,234.56'	}, 	
		{	value: '7', label: '£1,234.56'	}, 	
		{	value: '8', label: '¥1,234.56'	}, 	
		{	value: '9', label: '12 secs'	}, 	
		{	value: '10', label: '12 mins'	}, 	
		{	value: '11', label: '12 hrs'	}, 	
		{	value: '12', label: '12 days'	}, 	
		{	value: '13', label: '12 wks'	}, 	
		{	value: '14', label: '12 mths'	}, 	
		{	value: '15', label: '12 qtrs'	}, 	
		{	value: '16', label: '12 yrs'	}
	];
	
	$scope.directionList = [
		{	value: 'up', label: 'Up'	}, 	
		{	value: 'down', label: 'Down'},
		{	value: 'none', label: 'None'	}
	];
	
	$scope.aggregateList = [
		{	value: 'sum_total', label: 'Sum Total'	}, 	
		{	value: 'average', label: 'Average'}
	];
	
	
	
	/*Setting Selected item in dropdown select */
	function getSelectedItem(array, object){
		return array[$filter('GetIndex')(array,object )]; 
	}
	
	/* Add KPI */
	jQuery("#new_goal_btn").click(function(){
		getUsers("add");
		
		$scope.kpi_name = "";
		$scope.kpi_desc = "";
		$scope.kpi_frequency = $scope.frequencyList[0];
		$scope.kpi_format = $scope.formatList[0];
		$scope.kpi_best_direction = $scope.directionList[0];
		$scope.kpi_target = "";
		$scope.kpi_rag_1 = "";
		$scope.kpi_rag_2 = "";
		$scope.kpi_rag_3 = "";
		$scope.kpi_rag_4 = "";
		$scope.kpi_agg_type = $scope.aggregateList[0];
		$scope.isopen_users = false;
		
		$($scope.add_modal_id).modal('show');
	});
	
	
	$scope.addKPI = function(){
		$scope.validated = validate_form("add");
		if($scope.validated == true){
			var data = {
				action : "add_kpi",
				name: $scope.kpi_name,
				description: $scope.kpi_desc,
				frequency: $scope.kpi_frequency.value,
				format: $scope.kpi_format.value,
				best_direction: $scope.kpi_best_direction.value,
				target: $scope.kpi_target,
				agg_type: $scope.kpi_agg_type.value,
				rag_1: $scope.kpi_rag_1,
				rag_2: $scope.kpi_rag_2,
				rag_3: $scope.kpi_rag_3,
				rag_4: $scope.kpi_rag_4,
				users: $scope.kpi_users
			};
			
			processKpi(data, "add", $scope.add_modal_id, $scope.ajax_add_url);
		}
	}

	/* edit Organisation */
	
	angular.element('#edit_kpiModal').on('show.bs.modal', function(e) {
		var current_row = angular.element(e.relatedTarget).parents('tr');
		var row_data = Kpi_list.row( current_row ).data();
		var kpi_id = row_data[1];
		getUsers("edit", kpi_id);
		
		$(".edit_kpi_days_list small").removeClass("selected");
		
		$scope.$apply(function(){
			$scope.edit_kpi_id = kpi_id;
			$scope.edit_kpi_name = row_data[0];
			$scope.edit_kpi_desc = row_data[3];
			$scope.edit_kpi_frequency =  getSelectedItem($scope.frequencyList, {value:row_data[4]});
			$scope.edit_kpi_best_direction = getSelectedItem($scope.directionList, {value:row_data[6]});
			$scope.edit_kpi_target = row_data[7];
			$scope.edit_kpi_rag_1 = row_data[8];
			$scope.edit_kpi_rag_2 = row_data[9];
			$scope.edit_kpi_rag_3 = row_data[10];
			$scope.edit_kpi_rag_4 = row_data[11];
			$scope.edit_kpi_agg_type = getSelectedItem($scope.aggregateList, {value:row_data[12]}); 
			$scope.edit_kpi_format = getSelectedItem($scope.formatList, {value:row_data[13]});
			$scope.islocked = row_data[15]; 
			$scope.edit_kpi_days = row_data[18]; 
			
			var kpi_days = $scope.edit_kpi_days.split(",");
			$scope.temp_edit_kpi_days = kpi_days;
			
			if($scope.edit_kpi_frequency.value == "daily"){
				$scope.isopen_edit_kpi_days = true;
			}else{
				$scope.isopen_edit_kpi_days = false;
			}
			x = 0;
			while(x < kpi_days.length){
				$(".edit_kpi_days_list small#kd-"+kpi_days[x]).addClass("selected");
				x++;
			}
			if($scope.islocked == 1){
				$scope.kpi_frequency_disable = true;
			}else{
				$scope.kpi_frequency_disable = false;
			}
			$scope.isopen_edit_kpi_users = false;
			$scope._error_edit_kpi_users = "";
			
		});
	});
	
	$scope.change_kpi_days = function(day_num){
		var day_index = day_num - 1;
		if($(".edit_kpi_days_list small#kd-"+day_num).hasClass("selected")){
			$(".edit_kpi_days_list small#kd-"+day_num).removeClass("selected");
			$scope.temp_edit_kpi_days[day_index] = 0;
		}else{
			$(".edit_kpi_days_list small#kd-"+day_num).addClass("selected");
			$scope.temp_edit_kpi_days[day_index] = day_num;
		}
		console.log($scope.temp_edit_kpi_days);
	}
	
	$scope.updateKPI = function(){
		$scope.validated = validate_form("edit");
		
		if($scope.validated == true){
			var data = {	
				action : "edit_kpi",
				kpi_id : $scope.edit_kpi_id,
				name: $scope.edit_kpi_name,
				description: $scope.edit_kpi_desc,
				frequency: $scope.edit_kpi_frequency.value,
				kpi_format_id: $scope.edit_kpi_format.value,
				best_direction: $scope.edit_kpi_best_direction.value,
				target: $scope.edit_kpi_target,
				agg_type: $scope.edit_kpi_agg_type.value,
				rag_1: $scope.edit_kpi_rag_1,
				rag_2: $scope.edit_kpi_rag_2,
				rag_3: $scope.edit_kpi_rag_3,
				rag_4: $scope.edit_kpi_rag_4,
				users: $scope.edit_kpi_users,
				kpi_days : $scope.temp_edit_kpi_days
			};
			
			processKpi(data, "edit", $scope.edit_modal_id, $scope.ajax_edit_url);
		}	
	}
	
	
	
	/*delete KPI*/
	angular.element('#delete_kpiModal').on('show.bs.modal', function(e) {
		var current_row = angular.element(e.relatedTarget).parents('tr');
		var row_data = Kpi_list.row( current_row ).data();
		var kpi_id = row_data[1];
		
		$scope.$apply(function(){
			$scope.delete_kpi_id = kpi_id;
		});
	});
	
	
	$scope.deleteKPI = function(){
		var data = {	
			action : "delete_kpi",
			kpi_id : $scope.delete_kpi_id
		};
		
		processKpi(data, "delete", $scope.delete_modal_id, $scope.ajax_delete_url);
	}
	
	
	function objToArray(obj){
		return $.map(obj, function(value, index) {
			return [value];
		});
	}

	/*process kpi*/
	function processKpi(initial_data, function_type, modal_id, url){
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var data = angular.extend(initial_data, csrf_object);
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : url,
		        data    :  $httpParamSerializerJQLike(data), 
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				var data = response.data;
				if(response.result == "success"){
					$scope.popup_alert_message('btn-success', "Success", response.message, modal_id);
					if(function_type == "add"){
						var rowNode = Kpi_list
							.row.add( 
								[data.name, response.kpi_id, data.icon, data.description, data.frequency, data.format, 
								data.best_direction, data.target, data.rag_1, data.rag_2, data.rag_3, data.rag_4, data.agg_type, data.kpi_format_id, "", 0, "",data.name, data.kpi_days  ] 
							)
							.draw()
							.node();
						
						angular.element( rowNode ).attr("id","kpi_id-"+response.kpi_id);
						
						
					}else if(function_type == "edit"){
						var data_count = Object.keys(data).length;
						var data_keys = Object.keys(data);
						var current_row = angular.element("#kpi_id-"+response.kpi_id).parents('tr');
						var row_kpi_id = "#kpi_id-"+response.kpi_id;
						if( data_count > 0){
							var x = 0;
							
							while(x < data_count){
								var className = ".kpi_"+data_keys[x];
								//if(data_keys[x] == "kpi_fo")
								var new_column_data = data[data_keys[x]];
								var kpi_column = angular.element(row_kpi_id).find(className);
								if(kpi_column.length > 0){
									update_column = Kpi_list.cell(kpi_column).data(new_column_data); 
								}
								
								x++
							}
								
						}
						
						$scope.isopen_edit_kpi_users = false;
						$scope._error_edit_kpi_users = "";
					}else if(function_type == "delete"){
						var current_row = angular.element("#kpi_id-"+response.kpi_id);
						var rowNode = Kpi_list.rows(current_row).remove().draw();
					}
				
				}else{
					if(function_type == "edit" && response.result == "error" && response.error_type == "users"){
						/* add modal here */
						$scope.isopen_edit_kpi_users = true;
						$scope._error_edit_kpi_users = response.message;
					}else{
						$scope.popup_alert_message('btn-danger', "Error", response.message, modal_id);
					}
					
				}
				
			});	
		
	}
	
	
	
	function validate_form(type){
		$scope.error = false;
		var rag_has_error = false;
		if(type == "add"){
			var kpi_name = $filter('myTrim')($scope.kpi_name);
			var target = $filter('myTrim')($scope.kpi_target);
			var rag_1 = $filter('myTrim')($scope.kpi_rag_1);
			var rag_2 = $filter('myTrim')($scope.kpi_rag_2);
			var rag_3 = $filter('myTrim')($scope.kpi_rag_3);
			var rag_4 = $filter('myTrim')($scope.kpi_rag_4);
			
			
			if(kpi_name == '' || kpi_name == undefined)
			{
				$scope._error_kpi =  "KPI Name is required";
				$scope.isopen_kpi_name = true;
				$scope.error = true;
			}else{
				$scope._error_kpi =  "";
				$scope.isopen_kpi_name = false;
			}
			
			if(target != '' && target != undefined){
				if(!$.isNumeric($scope.kpi_target)){
					$scope._error_target =  "Target should be numeric.";
					$scope.isopen_target = true;
					$scope.error = true;	
				}else{
					$scope._error_target =  "";
					$scope.isopen_target = false;
				}
				
			}else{
				$scope._error_target =  "";
				$scope.isopen_target = false;
			}
			
			if(rag_1 == "" && rag_2 == "" && rag_3 == "" && rag_4 == "" )
			{
				$scope.kpi_rag_1 =  null;	
				$scope.kpi_rag_2 =  null;	
				$scope.kpi_rag_3 =  null;	
				$scope.kpi_rag_4 =  null;	
				rag_has_error = false;
			}else if( (rag_1 != "" && $.isNumeric(rag_1))
				&& (rag_2 != "" && $.isNumeric(rag_2)) 
				&& (rag_3 != "" && $.isNumeric(rag_3))
				&& (rag_4 != "" && $.isNumeric(rag_4))
				)
			{
				rag_has_error = false;
			}else if( rag_1 == null && rag_2 == null && rag_3 == null && rag_4 == null )
			{
				rag_has_error = false;
			}
			else
			{
				rag_has_error = true;
			}
			
			var users_count = $scope.kpi_usersModel.length;
			var tmp_users = $scope.kpi_usersModel;
			$scope.kpi_users = [];
			if(users_count > 0){
				var x = 0;
				var users = [];
				while(x < users_count){
					users.push(tmp_users[x].id);
					x++;
				}
				$scope.kpi_users = users;
				$scope.isopen_users = false;
			}else{
				$scope._error_kpi_users =  "Minimum assigned user is 1.";
				$scope.isopen_users = true;
				$scope.error = true;	
			}
			
			if(rag_has_error == true){
				$scope._error_rag =  "RAG Threshold should be all numeric or leave all blank.";
				$scope.isopen_rag = true;
				$scope.error = true;	
			}else{
				$scope._error_rag =  "";
				$scope.isopen_rag = false;
			}
		}else if(type == "edit"){
			var kpi_name = $filter('myTrim')($scope.edit_kpi_name);
			var target = $filter('myTrim')($scope.edit_kpi_target);
			var rag_1 = $filter('myTrim')($scope.edit_kpi_rag_1);
			var rag_2 = $filter('myTrim')($scope.edit_kpi_rag_2);
			var rag_3 = $filter('myTrim')($scope.edit_kpi_rag_3);
			var rag_4 = $filter('myTrim')($scope.edit_kpi_rag_4);
			if(kpi_name == '' || kpi_name == undefined)
			{
				$scope._error_edit_kpi =  "KPI Name is required";
				$scope.isopen_edit_kpi_name = true;
				$scope.error = true;
			}else{
				$scope._error_edit_kpi =  "";
				$scope.isopen_edit_kpi_name = false;
			}
			
			if(target != '' && target != undefined){
				if(!$.isNumeric($scope.edit_kpi_target)){
					$scope._error_edit_target =  "Target should be numeric.";
					$scope.isopen_edit_target = true;
					$scope.error = true;	
				}else{
					$scope._error_edit_target =  "";
					$scope.isopen_edit_target = false;
				}
				
			}else{
				$scope._error_edit_target =  "";
				$scope.isopen_edit_target = false;
			}
			
			if(rag_1 == "" && rag_2 == "" && rag_3 == "" && rag_4 == "" )
			{
				$scope.edit_kpi_rag_1 =  null;	
				$scope.edit_kpi_rag_2 =  null;	
				$scope.edit_kpi_rag_3 =  null;	
				$scope.edit_kpi_rag_4 =  null;	
				rag_has_error = false;
			}else if( (rag_1 != "" && $.isNumeric(rag_1))
				&& (rag_2 != "" && $.isNumeric(rag_2)) 
				&& (rag_3 != "" && $.isNumeric(rag_3))
				&& (rag_4 != "" && $.isNumeric(rag_4))
				)
			{
				rag_has_error = false;
			}else if( rag_1 == null && rag_2 == null && rag_3 == null && rag_4 == null )
			{
				rag_has_error = false;
			}else
			{
				rag_has_error = true;
			}
		
			
			var users_count = $scope.edit_kpi_usersModel.length;
			var tmp_users = $scope.edit_kpi_usersModel;
			$scope.edit_kpi_users = [];
			if(users_count > 0){
				var x = 0;
				var users = [];
				while(x < users_count){
					users.push(tmp_users[x].id);
					x++;
				}
				$scope.edit_kpi_users = users;
			}
			
			if(rag_has_error == true){
				$scope._error_edit_rag =  "RAG Threshold should be all numeric or leave all blank.";
				$scope.isopen_edit_rag = true;
				$scope.error = true;	
			}else{
				$scope._error_edit_rag =  "";
				$scope.isopen_edit_rag = false;
			}
		}	
		
		
		if($scope.error == true){
			return false;
		}else{
			return true;	
		}
	}
	
	function getUsers(type, kpi_id){
		var get_kpi_users = false;
		if(type == "edit"){
			get_kpi_users = true;
			kpi_id = kpi_id;
		}
		
		var data = {	
				get_users: true,
				get_kpi_users : get_kpi_users,
				kpi_id : kpi_id,
				csrf_gd : Cookies.get('csrf_gd')
			};
			
		$scope.file =  $http({
		        method  : 'POST',
		        url     : $scope.ajax_get_users,
				data : $httpParamSerializerJQLike(data),
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				var data_users = response.users;
				var count = response.count;
				$scope.kpi_usersModel = [];
				
				if(type == "add"){
					if(count > 0){
						var x = 0;
						var users = [];
						var assigned_users = [];
						while(x < count){	
							users.push({id: data_users[x]['user_id'], label : data_users[x]['name']});
							if(count == 1){
								console.log('d');
								assigned_users.push({id:data_users[x]['user_id']});
							}
							x++;
							
						}
						$scope.kpi_usersOptions = users;
						$scope.kpi_usersModel = assigned_users;
					}else{
						$scope.kpi_usersModel = [];
						$scope.kpi_usersOptions = [];
					}
					$scope.kpi_usersTitle = "Who can enter data for this KPI?"; 
					
					 
				}else{
					
					if(count > 0){
						var x = 0;
						var users = [];
						var assigned_users = [];
						while(x < count){	
							users.push({id: data_users[x]['user_id'], label : data_users[x]['name']});
							if(data_users[x].assigned == true){
								assigned_users.push({id:data_users[x]['user_id']});
							}
							x++;
							
						}
						$scope.edit_kpi_usersOptions = users;
						$scope.edit_kpi_usersModel = assigned_users;
					}else{
						$scope.edit_kpi_usersOptions = [];
						$scope.edit_kpi_usersModel = [];
					}
					
					$scope.edit_kpi_usersTitle = "Who can enter data for this KPI?"; 
				}
			});	
	}
	
	/* require minimum user to enter as 1 in add kpi form */
	$scope.kpi_usersChange = function(user){
		var users_model_count = $scope.kpi_usersModel.length;
		var users_options_count = $scope.kpi_usersOptions.length;
		if(users_model_count  == 0){
			alert("Minimum 1 user.");
			$scope.kpi_usersModel.push({id:user.id});
		}
	}
	
	/* require minimum user to enter as 1 in edit kpi form */
	$scope.edit_kpi_usersChange = function(user){
		var users_model_count = $scope.edit_kpi_usersModel.length;
		var users_options_count = $scope.edit_kpi_usersOptions.length;
		if(users_model_count  == 0){
			alert("Minimum 1 user.");
			$scope.edit_kpi_usersModel.push({id:user.id});
		}
	}
	
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




