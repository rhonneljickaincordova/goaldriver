var app = angular.module('moreApps');

app.filter('PropsFilter', PropsFilter);
app.filter('GetIndex', GetIndex);
app.controller('kpi_graphs', kpi_graphs);

function GetIndex(){
	return function(items, props) {
    
		if (angular.isArray(items)) {
			var itemMatches = false;
			var value_index = 0;
			var indexKey = false;
			items.forEach(function(item) {
				var keys = Object.keys(props);
				for (var i = 0; i < keys.length; i++) {
					var prop = keys[i];
					var text = props[prop].toLowerCase();
					if(item[prop] != undefined){
						if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
							itemMatches = true;
							break;
						}
					}
				}
				if(itemMatches){
					return false;
				}	 
				value_index++;
			});

			return value_index;
		
		}else{
			return 0;
		}

	};
	
}


function PropsFilter() {
  return function(items, props) {
    var out = [];
	if (angular.isArray(items)) {
      items.forEach(function(item) {
        var itemMatches = false;

        var keys = Object.keys(props);
        for (var i = 0; i < keys.length; i++) {
          var prop = keys[i];
          var text = props[prop].toLowerCase();
          if(item[prop] != undefined){
	          if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
	            itemMatches = true;
	            break;
	          }
	      }
        }

        if (itemMatches) {
          out.push(item);
        }
      });
    } else {
      // Let the output be the input untouched
      out = items;
    }

    return out;
  };
}


function kpi_graphs($scope, $http, $filter, $httpParamSerializerJQLike){
	$scope.no_kpi_exist = false;
	$scope.add_modal_id = "#add_graphModal";
	$scope.edit_modal_id = "#edit_graphModal";
	$scope.delete_modal_id = "#delete_graphModal";
	
	$scope.ajax_get_url = 'graph/ajax_get_graphs';
	$scope.ajax_add_url = 'graph/ajax_add_graph';
	$scope.ajax_edit_url = 'graph/ajax_edit_graph';
	$scope.ajax_delete_url = 'graph/ajax_delete_graph';
	$scope.ajax_settings_url = 'graph/ajax_get_graph_settings';
	$scope.ajax_get_default_shared_users =  "graph/ajax_get_shared_users_default";
	
	$scope.ajax_get_graph_types =  "graph/ajax_get_graph_types";
	$scope.ajax_get_kpis =  "kpi/ajax_get_kpis";
	$scope.ajax_get_kpis_as_member =  "kpi/ajax_get_kpis_as_member";
	
	
	$scope.graph_kpiList = [];
	$scope.graph_typeList = [];
	$scope.reset_frequency_dailyList = [
		{	value: 'daily', label: 'Daily'	}, 	
		{	value: 'weekly', label: 'Weekly'},
		{	value: 'monthly', label: 'Monthly'	},
		{	value: 'quarterly', label: 'Quarterly'	},
		{	value: 'yearly', label: 'Yearly'	}
	];
	
	
	if($("#kpi_permission").val() == "readwrite"){
		$scope.kpi_users = []; 
		$scope.kpi_usersModel = []; 
		$scope.kpi_usersOptions = [];
		$scope.kpi_usersSettings = {showCheckAll : false, showUncheckAll: false, dynamicTitle: true};
		$scope.kpi_usersTranslation = {buttonDefaultText: "Default Users", dynamicButtonTextSuffix	 : "Default user(s)"};
		
		$scope.sharedToUsers = []; 
		$scope.sharedToUsersModel = []; 
		$scope.sharedToUsersOptions = [];
		$scope.sharedToUsersSettings = {showCheckAll : false, showUncheckAll: false, dynamicTitle: true};
		$scope.sharedToUsersTranslation = {buttonDefaultText: "Share", dynamicButtonTextSuffix	 : "Shared user(s)"};
		
		$scope.edit_kpi_users = []; 
		$scope.edit_kpi_usersModel = []; 
		$scope.edit_kpi_usersOptions = [];
		$scope.edit_kpi_usersSettings = {showCheckAll : false, showUncheckAll: false, dynamicTitle: true};
		$scope.edit_kpi_usersTranslation = {buttonDefaultText: "Default Users", dynamicButtonTextSuffix	 : "Default user(s)"};
		
		$scope.edit_sharedToUsers = []; 
		$scope.edit_sharedToUsersModel = []; 
		$scope.edit_sharedToUsersOptions = [];
		$scope.edit_sharedToUsersSettings = {showCheckAll : false, showUncheckAll: false, dynamicTitle: true};
		$scope.edit_sharedToUsersTranslation = {buttonDefaultText: "Share", dynamicButtonTextSuffix	 : "Shared user(s)"};
	}
	/*Load Graphs LIst*/
	$( '#kpis_tablist a[href="#kpi_graphs_tab"]' ).on('show.bs.tab', function (e) {
		$scope.reload_graphs_list();
	});
	
	
	/* Reload Graph Table */	
	$scope.reload_graphs_list = function(){
		var data = {action: "get_graphs"};
		processGraph(data, "load", "", $scope.ajax_get_url);
		getKpis();
		getGraphTypes();
	}
	
	/* Add Graph */
	$("body").on("click", "#new_graph_btn", function(){
		getKpis();
		if($scope.no_kpi_exist == true){
			$.alert({
				title: 'Error:',
				content: 'Cannot add graph. No KPI created.'
			});
		}else{
			var kpi_id = $scope.graph_kpis.value;
			getGraphTypes();
			getUsers("kpi_users", 0, kpi_id, 'add');
			getDefaultSharedUsers();
			
			$scope.graph_name = "";
			$scope.graph_description = "";
			$scope.bShowOnDash = false;	
			$scope.bShowAverage = false;	
			$scope.bShowBreakdown = false;	
			$scope.bShowGaugeOnDash = false;	
			
			$scope.reset_frequency_type = $scope.reset_frequency_dailyList[0];	
			$($scope.add_modal_id).modal('show');
		}
	});
	
	
	$scope.addGraph = function(){
		if($scope.no_kpi_exist == false){
			users = $scope.kpi_usersModel;
			var data = {
				action : "add_graph",
				graph_name: $scope.graph_name,
				graph_description: $scope.graph_description,
				graph_kpi_id: $scope.graph_kpis.value,
				graph_type: $scope.graph_type.value,
				users : users,
				show_on_dash : $scope.bShowOnDash,
				show_average : $scope.bShowAverage,
				show_break_down : $scope.bShowBreakdown,
				show_gauge_on_dash : $scope.bShowGaugeOnDash,
				shared_users : $scope.sharedToUsersModel,
				reset_frequency : $scope.reset_frequency_type.value
			};
			
			processGraph(data, "add", $scope.add_modal_id, $scope.ajax_add_url);
		}
	}
	
	/* edit Graph */
	angular.element('#edit_graphModal').on('show.bs.modal', function(e) {
		var current_row = angular.element(e.relatedTarget).parents('tr');
		var row_data = Graph_list.row( current_row ).data();
		var settings = getGraphSettings(row_data[0]);
		
		
		$scope.$apply(function(){
			$scope.edit_graph_id = row_data[0];
			$scope.edit_graph_name = row_data[1];
			$scope.edit_graph_description = row_data[2];
			$scope.edit_graph_kpis =  getSelectedItem($scope.graph_kpiList, {value:row_data[5]});
			$scope.temp_edit_graph_kpi = $scope.edit_graph_kpis;
			$scope.edit_graph_type = getSelectedItem($scope.graph_typeList, {value:row_data[3]});
			$scope.edit_bShowOnDash = false;	
			$scope.edit_bShowAverage = false;	
			$scope.edit_bShowBreakdown = false;	
			$scope.edit_bShowGaugeOnDash = false;	
			
			
			if(row_data[11] == 1){
				$scope.edit_bShowOnDash = true;	
			}
			if(row_data[12] == 1){
				$scope.edit_bShowAverage = true;
			
			}
			if(row_data[13] == 1){
				$scope.edit_bShowBreakdown = true;
			}
			if(row_data[14] == 1){
				$scope.edit_bShowGaugeOnDash = true;	
			}
			
			var rf_index = getSelectedItem($scope.reset_frequency_dailyList, {value:row_data[15]});   	
			if(rf_index != undefined){
				$scope.edit_reset_frequency_type = rf_index;
			}else{
				$scope.edit_reset_frequency_type = $scope.reset_frequency_dailyList[0];	
			}
		});
		getUsers("shared_users", $scope.edit_graph_id, 0, "edit");
	});
	
	$scope.editGraph = function(){
		var users = [];
		if($("#kpi_permission").val() == "readwrite"){
			users = $scope.edit_kpi_usersModel;
		}
			
		var data = {	
			action : "edit_graph",
			users : users,
			graph_id : $scope.edit_graph_id,
			graph_name: $scope.edit_graph_name,
			graph_description: $scope.edit_graph_description,
			kpi_id : $scope.edit_graph_kpis.value,
			graph_type_id  : $scope.edit_graph_type.value,
			show_on_dash : $scope.edit_bShowOnDash,
			show_average : $scope.edit_bShowAverage,
			show_break_down : $scope.edit_bShowBreakdown,
			show_gauge_on_dash : $scope.edit_bShowGaugeOnDash,
			shared_users : $scope.edit_sharedToUsersModel,
			reset_frequency : $scope.edit_reset_frequency_type.value
		};
		
		processGraph(data, "edit", $scope.edit_modal_id, $scope.ajax_edit_url);
			
	}
	
	$scope.deleteGraph = function(){
		var data = {	
			action : "delete_graph",
			graph_id: $scope.delete_graph_id
		};	
		processGraph(data, "delete", $scope.delete_modal_id, $scope.ajax_delete_url);		
	}
	
	
	angular.element('#delete_graphModal').on('show.bs.modal', function(e) {
		var current_row = angular.element(e.relatedTarget).parents('tr');
		var row_data = Graph_list.row( current_row ).data();
		
		$scope.$apply(function(){
			$scope.delete_graph_id = row_data[0];
		});
	});
	
	
	function objToArray(obj){
		return $.map(obj, function(value, index) {
			return [value];
		});
	}
	
	function getGraphSettings(graph_id){
		var data = {	
				action : "get_graph_settings",
				graph_id: graph_id,
				csrf_gd : Cookies.get('csrf_gd')
			};	
		$scope.file =  $http({
			method  : 'POST',
			url     : $scope.ajax_settings_url,
			data    :  $httpParamSerializerJQLike(data), 
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				var settings = response.settings;
				var data_users = response['users'];
				var users_count = response.users_count;
				/* if(users_count > 0){ */
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
					$scope.edit_kpi_usersTitle = "Whos data will be shown on this graph?"; 
					$scope.edit_kpi_usersOptions = users;
					$scope.edit_kpi_usersModel = assigned_users;
					$scope.edit_statusFilterDisabled = false;
					
					if($("#kpi_permission").val() == "readwrite"){
						$scope.isopen_edit_display_option = true;
						$scope.isopen_add_display_option = true;
					}
					
				/* }else{
					$scope.edit_kpi_usersTitle = "No assigned users";
					$scope.edit_kpi_usersOptions = [];
					$scope.edit_kpi_usersModel = [];
					$scope.edit_statusFilterDisabled = true;
					
				} */
			}
		});	
	}
	
	
	function getDefaultSharedUsers(){
		var data = {	
				action : "get_default_shared_users",
				csrf_gd : Cookies.get('csrf_gd')
			};	
		$scope.file =  $http({
			method  : 'POST',
			url     : $scope.ajax_get_default_shared_users ,
			data    :  $httpParamSerializerJQLike(data), 
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.result == "success"){
				var data_users = response['users'];
				var count = response.count;
				
				if(count > 0){
					var x = 0;
					var users = [];
					while(x < count){	
						var name = data_users[x]['first_name']  + " "+ data_users[x]['last_name'];
						users.push({id: data_users[x]['user_id'], label :  name});
						x++;
					}
					$scope.sharedToUsersTitle = "Who can see this graph?"; 
					$scope.sharedToUsersOptions = users;
					$scope.sharedToUsersModel = [];
					$scope.sharedToUsersDisabled = false;
				}else{
					$scope.sharedToUsersTitle = "No users available";
					$scope.sharedToUsersOptions = [];
					$scope.sharedToUsersModel = [];
					$scope.sharedToUsersDisabled = true;
				}
			}
		});	
	}
	
	
	/*process graph */
	function processGraph(initial_data, function_type, modal_id, url){
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var data = angular.extend(initial_data, csrf_object);
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : url,
		        data    :  $httpParamSerializerJQLike(data), //forms user object
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				var data = response.data;
				var count = response.count;
				
				if(response.result == "success"){
					if(function_type == "load"){
						Graph_list.clear().draw();
						var x = 0
						while(x < count){
							var rowNode = Graph_list
							.row.add( 
								[ 
									data[x].graph_id, 
									data[x].graph_name, 
									data[x].description, 
									data[x].graph_type_id, 
									data[x].graph_type, 
									data[x].kpi_id, 
									data[x].kpi_name, 
									data[x].first_name +" "+data[x].last_name,
									data[x].entered,
									"",
									data[x].graph_name,
									data[x].bShowOnDash,
									data[x].bShowAverage,
									data[x].bShowBreakdown,
									data[x].bShowGaugeOnDash,
									data[x].reset_frequency_type,
									data[x].kpi_frequency
								] 
							)
							.draw()
							.node();
							
							angular.element( rowNode ).attr("id","graph_id-"+data[x].graph_id);
							
							x++;
						}
					}else if(function_type == "add"){
						var rowNode = Graph_list
							.row.add( 
								[ 
									data.graph_id, 
									data.graph_name, 
									data.description, 
									data.graph_type_id, 
									data.graph_type, 
									data.kpi_id, 
									data.kpi_name, 
									data.first_name +" "+data.last_name,
									data.entered,
									"",
									data.graph_name,
									data.bShowOnDash,
									data.bShowAverage,
									data.bShowBreakdown,
									data.bShowGaugeOnDash,
									data.reset_frequency_type,
									data.kpi_frequency
								] 
							)
							.draw()
							.node();
						
						
						angular.element( rowNode ).attr("id","graph_id-"+response.graph_id);
						$scope.popup_alert_message('btn-success', "Success", response.message, modal_id);
						
					}else if(function_type == "edit"){
						var graph_name = angular.element("#graph_id-"+response.graph_id).find(".graph_name");
						var graph_description = angular.element("#graph_id-"+response.graph_id).find(".graph_description");	
						var graph_type_id = angular.element("#graph_id-"+response.graph_id).find(".graph_type_id");	
						var graph_type = angular.element("#graph_id-"+response.graph_id).find(".graph_type");	
						var graph_kpi_id = angular.element("#graph_id-"+response.graph_id).find(".graph_kpi_id");	
						var graph_kpi_name = angular.element("#graph_id-"+response.graph_id).find(".graph_kpi_name");	
						var graph_name_sort = angular.element("#graph_id-"+response.graph_id).find(".graph_name_sort");
						var graph_show_on_dashboard = angular.element("#graph_id-"+response.graph_id).find(".graph_show_on_dashboard");
						var graph_show_average = angular.element("#graph_id-"+response.graph_id).find(".graph_show_average");
						var graph_show_breakdown = angular.element("#graph_id-"+response.graph_id).find(".graph_show_breakdown");
						var graph_show_gauge_on_dash = angular.element("#graph_id-"+response.graph_id).find(".graph_show_gauge_on_dash");
						var graph_reset_frequency_type = angular.element("#graph_id-"+response.graph_id).find(".graph_reset_frequency_type");
						var graph_kpi_frequency = angular.element("#graph_id-"+response.graph_id).find(".graph_kpi_frequency");
						
						Graph_list.cell(graph_name).data(data.graph_name);
						Graph_list.cell(graph_description).data(data.description);
						Graph_list.cell(graph_type_id).data(data.graph_type_id);
						Graph_list.cell(graph_type).data(data.graph_type);
						Graph_list.cell(graph_kpi_id).data(data.kpi_id);
						Graph_list.cell(graph_kpi_name).data(data.kpi_name);
						Graph_list.cell(graph_name_sort).data(data.graph_name);
						Graph_list.cell(graph_show_on_dashboard).data(data.bShowOnDash);
						Graph_list.cell(graph_show_average).data(data.bShowAverage);
						Graph_list.cell(graph_show_breakdown).data(data.bShowBreakdown);
						Graph_list.cell(graph_show_gauge_on_dash).data(data.bShowGaugeOnDash);
						Graph_list.cell(graph_reset_frequency_type).data(data.reset_frequency_type);
						Graph_list.cell(graph_kpi_frequency).data(data.kpi_frequency);
						
						Graph_list.draw();
						
						$scope.popup_alert_message('btn-success', "Success", response.message, modal_id);
					}else if(function_type == "delete"){
						$scope.popup_alert_message('btn-success', "Success", response.message, modal_id);
						var current_row = angular.element("#graph_id-"+response.graph_id);
						var rowNode = Graph_list.rows(current_row).remove().draw();
					}
				
				}else{
					if(function_type != "load"){
						$scope.popup_alert_message('btn-danger', "Error", response.message, modal_id);
					}
				}
			});	
		
	}
	
	function getKpis(){
		getData("kpi");
	}
	
	function getGraphTypes(){
		getData("graph_type");
	}
	
	function getData(name){
		
		if(name == "graph_type"){
			var url = $scope.ajax_get_graph_types;	
			var data = {action: "get_graph_types", csrf_gd : Cookies.get('csrf_gd')};
		}else if(name == "kpi"){
			if($("#kpi_permission").val() == "readwrite"){
				var url = $scope.ajax_get_kpis;
				var data = {action: "get_kpis", csrf_gd : Cookies.get('csrf_gd')};
			}else{
				var url = $scope.ajax_get_kpis_as_member;
				var data = {action: "get_kpis_as_member", csrf_gd : Cookies.get('csrf_gd')};
			}
		}
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : url,
				data 	: $httpParamSerializerJQLike(data),
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				var types = response.data;
				var count = response.count;
				
				if(count > 0){
					var x = 0;
					if(name == "graph_type"){
						var graph_types = [];
						while(x < count){	
							graph_types.push({value: types[x]['graph_type_id'], label : types[x]['name'] });
							
							x++;
						}
						$scope.graph_typeList = graph_types;
						$scope.graph_type = $scope.graph_typeList[0];
						$scope.edit_graph_type = $scope.graph_typeList[0];
					}else if(name == "kpi"){
						
						var kpis = [];
						while(x < count){	
							kpis.push({value: types[x]['kpi_id'], label : types[x]['name'], frequency : types[x]['frequency'] });
							x++;
						}
						$scope.graph_kpiList = kpis;
						$scope.graph_kpis = $scope.graph_kpiList[0];
						$scope.edit_graph_kpis = $scope.graph_kpiList[0];
						$scope.no_kpi_exist = false;
					}
				}else{
					if(name == "kpi"){
						$scope.no_kpi_exist = true;
						var kpis = [];
						kpis.push({value: 0, label : 'No kpi' });
						$scope.graph_kpiList = kpis;
					}
				}
			});	
	}
	
	$scope.onFilterUserChange = function(user){
		var users_model_count = $scope.kpi_usersModel.length;
		var users_options_count = $scope.kpi_usersOptions.length;
		if(users_model_count  == 0){
			alert("Minimum 1 user to filter.");
			$scope.kpi_usersModel.push({id:user.id});
		}else if(users_options_count > 1 && (users_options_count == users_model_count)){
			/*all users*/
		}
		
		if(users_model_count  > 1){
			$scope.isopen_add_display_option = true;
		}else{
			$scope.isopen_add_display_option = false;
		}
	}
	
	$scope.edit_onFilterUserChange = function(user){
		var users_model_count = $scope.edit_kpi_usersModel.length;
		var users_options_count = $scope.edit_kpi_usersOptions.length;
		if(users_model_count  == 0){
			alert("Minimum 1 user to filter.");
			$scope.edit_kpi_usersModel.push({id:user.id});
		}else if(users_options_count > 1 && (users_options_count == users_model_count)){
			/*all users*/
		}
		
		if(users_model_count  > 1){
			$scope.isopen_edit_display_option = true;
		}else{
			$scope.isopen_edit_display_option = false;
		}
	}
	
	/*Setting Selected item in dropdown select */
	function getSelectedItem(array, object){
		return array[$filter('GetIndex')(array,object )]; 
	}
	
	$scope.ChangeGoal = function(type){
		if(type == "add")
		{
			var kpi_id = $scope.graph_kpis.value;
			var frequency = $scope.graph_kpis.frequency;
			if($("#kpi_permission").val() == "readwrite"){
				getUsers("kpi_users", graph_id, kpi_id, "add");
			}
		}
		else
		{
			var kpi_id = $scope.edit_graph_kpis.value;
			var frequency = $scope.edit_graph_kpis.frequency;
			var graph_id = $scope.edit_graph_id;
			if($("#kpi_permission").val() == "readwrite"){
				getUsers("kpi_users", graph_id, kpi_id, "edit");
			}	
		}	
		
	}
	
	function getUsers(type, graph_id, kpi_id, operation_type){
		
		
		if(type == "kpi_users"){
			/* graph users for data */
			var url = 'graph/ajax_get_assigned_users';
			var data = {
					action : "get_assigned_users",
					graph_id : graph_id,
					kpi_id	: kpi_id,
					csrf_gd	: Cookies.get('csrf_gd')
				};
		}else{
			/* shared users */
			var url = 'graph/ajax_shared_to_users';
			var data = {
					action : "shared_to_users",
					graph_id : graph_id,
					kpi_id 	: kpi_id, 
					csrf_gd	: Cookies.get('csrf_gd')
				};
		}
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : url,
				data 	: $httpParamSerializerJQLike(data),
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				var data_users = response.users;
				var count = response.count;
				
				if(response.result == "success"){
					if(operation_type == "add")
					{
						if(type == "kpi_users"){
							if(count > 0){
								var x = 0;
								var users = [];
								var assigned_users = [];
								while(x < count){	
									var name = data_users[x]['first_name']  + " "+ data_users[x]['last_name'];
									users.push({id: data_users[x]['user_id'], label :  name});
									assigned_users.push({id:data_users[x]['user_id']});	
									x++;
									
								}
								$scope.kpi_usersTitle = "Whos data will be shown on this graph?"; 
								$scope.kpi_usersOptions = users;
								$scope.kpi_usersModel = assigned_users;
								$scope.statusFilterDisabled = false;
							}else{
								$scope.kpi_usersTitle = "No assigned users";
								$scope.kpi_usersOptions = [];
								$scope.kpi_usersModel = [];
								$scope.statusFilterDisabled = true;
							}
						}
						
					}
					else
					{
						if(type == "kpi_users"){
							if(count > 0){
								var x = 0;
								var users = [];
								var assigned_users = [];
								while(x < count){	
									var name = data_users[x]['first_name']  + " "+ data_users[x]['last_name'];
									users.push({id: data_users[x]['user_id'], label :  name});
									if(data_users[x]['is_graph_user'] == true){
										assigned_users.push({id:data_users[x]['user_id']});	
									}
									x++;
									
								}
								$scope.edit_kpi_usersTitle = "Whos data will be shown on this graph?"; 
								$scope.edit_kpi_usersOptions = users;
								$scope.edit_kpi_usersModel = assigned_users;
								$scope.edit_statusFilterDisabled = false;
							}else{
								$scope.edit_kpi_usersTitle = "No assigned users";
								$scope.edit_kpi_usersOptions = [];
								$scope.edit_kpi_usersModel = [];
								$scope.edit_statusFilterDisabled = true;
							}
						}else{
							if(count > 0){
								var x = 0;
								var users = [];
								var shared_users = [];
								while(x < count){	
									var name = data_users[x]['first_name']  + " "+ data_users[x]['last_name'];
									users.push({id: data_users[x]['user_id'], label :  name});
									if(data_users[x]['shared_id'] != null){
										shared_users.push({id:data_users[x]['user_id']});	
									}
									x++;
									
								}
								$scope.edit_sharedToUsersTitle = "Who can see this graph?"; 
								$scope.edit_sharedToUsersOptions = users;
								$scope.edit_sharedToUsersModel = shared_users;
								$scope.edit_sharedToUsersDisabled = false;
							}else{
								$scope.edit_sharedToUsersTitle = "No users available";
								$scope.edit_sharedToUsersOptions = [];
								$scope.edit_sharedToUsersModel = [];
								$scope.edit_sharedToUsersDisabled = true;
							}
						}
					}	
				}
			});	
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




