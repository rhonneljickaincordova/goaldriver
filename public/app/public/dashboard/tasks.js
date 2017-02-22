angular.module('moreApps')
.filter('GetIndexNumeric', GetIndexNumeric)
.controller('taskTabCtrl', function ($scope,$http,$location,$rootScope,$filter,$httpParamSerializerJQLike){
	
	/*************************************************************************************************
	Declare Variables
	*************************************************************************************************/
		$scope.show_task_comment_container = false;
		$scope.load_all_tasks = 0;
		$scope._multipleUser = {};
		$scope._edit_taskTab_multipleUser = {};
		$scope.participants = {};
		$scope.edit_participants = {};
		$scope.temp_delete_m_id = 0;
		$scope.organ_usersList = [];
		$scope.milestonesDropdownList = [];
		$scope.processing_text = '<div><i class="fa fa-spinner fa-pulse" style="margin-right:10px;"></i><label>Loading...</label></div>';
		$scope.empty_table_text = "No Tasks have been setup yet";
		
		$scope.statusList = [
			{	value: '0', label: '0'	}, 	
			{	value: '1', label: '10%'	}, 	
			{	value: '2', label: '20%'	}, 	
			{	value: '3', label: '30%'	}, 	
			{	value: '4', label: '40%'	}, 	
			{	value: '5', label: '50%'	}, 	
			{	value: '6', label: '60%'	}, 	
			{	value: '7', label: '70%'	}, 	
			{	value: '8', label: '80%'	}, 	
			{	value: '9', label: '90%'	}, 	
			{	value: '10', label: '100%'	}
		];
		$scope.priorityList = [
		    {value: '1', label: 'None'},
		    {value: '2', label: 'Low'},
		    {value: '3', label: 'Medium'},
		    {value: '4', label: 'High'}
		];
		
		$scope.init_functions = function()
		{
			$('#task_start_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false });
			$('#task_due_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });
			$('#edit_taskTab_task_start_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false });
			$('#edit_taskTab_task_due_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });
			
			$scope.get_milestones();
			if($("#milestone_permission_name").val() == "readwrite"){
				$scope.add_task_div_show = true;
			}else{
				$scope.add_task_div_show = false;
			}
			
		}
		$scope.Tasks_list = $("#datatable_tasks").DataTable({
			'language':{ "processing"	: $scope.processing_text, "emptyTable"	: $scope.empty_table_text	},
			"createdRow": function( row, data, index ){ 
				$(row).attr('id',"task_row-"+data[0]);
				$(row).attr('data_table_name',"task");
			},
			"aoColumnDefs":[
				{	"targets": [ 0 ], className: "t_task_id hidden", "searchable": false, "sortable": false					 	},
				{	"targets": [ 1 ], className: "t_comment_count", "searchable": false, "sortable": false,
					"mRender": function(data, type, row){
						if(type == "display"){
							if(data > 0){
								if($("#milestone_permission_name").val() == "readwrite"){
									var text_re = "<a href='#' data-toggle='modal' data-target='#edit_taskTab_task_modal' data-section_target='task' >";
											text_re += "<i data-toggle='tooltip' data-placement='right' data-original-title='Comments' title='Comments' class='fa fa-comments' title='comments'></i>";;
										text_re += "</a>";
									return text_re;
								}
							}
							return "";
						}
						return data;
					}
				},
				{ 	"targets": [ 2 ], className: "t_task_name" , "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							if($("#milestone_permission_name").val() == "readwrite"){
								var text_re = "<a href='#' data-toggle='modal' data-target='#edit_taskTab_task_modal' data-section_target='milestone_task'>";
										text_re += data;
									text_re += "</a>";
								return text_re;
							}else{
								return data;
							}
						}
						return data;
					}
				},
				{ 	"targets": [ 3 ], className: "t_task_description hidden" , "searchable": false, "sortable": true			},
				{ 	"targets": [ 4 ], className: "t_task_startDate hidden", "searchable": false, "sortable": true				},
				{ 	"targets": [ 5 ], className: "t_task_startDate_format hidden", "searchable": false, "sortable": true 	},
				{ 	"targets": [ 6 ], className: "t_task_startDate_format_final", "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var defaultdate = "-";
							var static_date = "-";
							if(data != "9999-99-99"){
								var defaultdate = data;
								var static_date = row[5];
							}
							var text_re = "<input type='text' class='form-control taskdatatable_start_date' data-initialize='0'  id='taskdatatable_start_date-"+row[0]+"' data-task_id='"+row[0]+"' data-defaultdate='"+defaultdate+"' data-isclicked='0' />"
							if($("#milestone_permission_name").val() == "readwrite"){
								return text_re;
							}else{
								return static_date;
							}
						}
						
						return data;
					}
				},
				{ 	"targets": [ 7 ], className: "t_task_dueDate hidden", "searchable": false, "sortable": false						},
				{ 	"targets": [ 8 ], className: "t_task_dueDate_format hidden", "searchable": false, "sortable": true 				},
				{ 	"targets": [ 9 ], className: "t_task_dueDate_format_final", "searchable": false, "sortable": true,
					"mRender": function(data, type, row){
						if(type == "display"){
							var defaultdate = "-";
							var static_date = "-";
							if(data != "9999-99-99"){
								var defaultdate = row[0];
								var static_date = row[8];
							}
							var text_re = "<input type='text' class='form-control taskdatatable_due_date' data-initialize='0'  id='taskdatatable_due_date-"+row[0]+"' data-task_id='"+row[0]+"' data-defaultdate='"+defaultdate+"' data-isclicked='0' />"
							if($("#milestone_permission_name").val() == "readwrite"){
								return text_re;
							}else{
								return static_date;
							}
						}
						return data;
					}
				},
				{ 	"targets": [ 10 ], className: "t_owner_id", "width" : "30px", "searchable":false, "sortable" : true, "sType": "num",
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_owner = "-";
							var dropdown_text = "<select class='task_dropdown_owner form-control' id='t_dp_owner_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.organ_usersList,function(user){
								var selected = "";
								if(user.value == data){
									selected = "selected";
									static_owner = user.label;
								}
								dropdown_text += "<option value='"+user.value+"' "+selected+">"+user.label+"</option>"; 
							});
							dropdown_text += "</select>";
							
							if($("#milestone_permission_name").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_owner;
							}
						}
						return data;
					}
				},
				{	"targets": [ 11 ], className: "t_status", "searchable": false,	"sortable" : true, "sType": "num",
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_status = "-";
							var dropdown_text = "<select class='task_dropdown_status form-control' id='t_dp_status_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.statusList,function(status){
								var selected = "";
								if(status.value == data){
									selected = "selected";
									static_status = status.label;
								}
								dropdown_text += "<option value='"+status.value+"' "+selected+">"+status.label+"</option>"; 
							});
							dropdown_text += "</select>";
							
							if($("#milestone_permission_name").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_status;
							}
						}
						return data;
					}
				},
				{	"targets": [ 12 ], className: "t_priority", "searchable": false, "sortable" : true, "sType": "num",
					"mRender": function(data, type, row){
						if(type == "display"){
							var static_priority = "-";
							var dropdown_text = "<select class='task_dropdown_priority form-control' id='t_dp_priority_id-"+row[0]+"' data-task_id='"+row[0]+"'>";
							angular.forEach($scope.priorityList,function(priority){
								var selected = "";
								if(priority.value == data){
									selected = "selected";
									static_priority = priority.label;
								}
								dropdown_text += "<option value='"+priority.value+"' "+selected+">"+priority.label+"</option>"; 
							});
							dropdown_text += "</select>";
							
							if($("#milestone_permission_name").val() == "readwrite"){
								return dropdown_text;
							}else{
								return static_priority;
							}
						}
						return data;
					}
				},
				{	"targets": [ 13 ], className: "t_participant_id hidden", "searchable": false, "sortable" : false 			},
				{	"targets": [ 14 ], className: "t_milestone_id hidden", "searchable": false, "sortable" : false			 	},
				{	"targets": [ 15 ], className: "t_edit_delete", "searchable": false, "sortable" : false,
					"mRender": function(data, type, row){
						if(type == "display"){
							if($("#milestone_permission_name").val() == "readwrite"){
								text_re = "<a href='#' title='Edit' data-toggle='modal' data-target='#edit_taskTab_task_modal' class='edit_task_icon' data-section_target='task' style='margin-right:10px;text-decoration: none;'>";
									text_re += '<i class="fa fa-pencil" style="font-size:15px;"></i>';
								text_re += "</a>";
								text_re += "<a href='#' title='Delete' data-toggle='modal' data-target='#delete_task_modal' class='delete_task_icon' style='text-decoration: none;'>";
									text_re += '<i class="fa fa-trash-o" style="font-size:15px;"></i>';
								text_re += "</a>";
								return text_re;
							}
							return "";
						}
						return data;
					}
				}
			],
		  "paginate" :false,
		  "dom" : "t",
		   "order": [[ 9, "asc" ]]
		});
		
	$('#datatable_tasks').on('draw.dt', function () {
		if($("#milestone_permission_name").val() != "readwrite"){
			$("#datatable_tasks").find(".t_edit_delete").addClass("hidden");				
		}else{
			$('.edit_task_icon').tooltip({placement: "left"});
			$('.delete_task_icon').tooltip({placement: "right"});	
		}
	});		
	/*************************************************************************************************
	GetIndex of dropdown using value 
	*************************************************************************************************/
	function getSelectedItem(array, object){
		return array[$filter('GetIndexNumeric')(array,object )]; 
	}
/******************************************************************************************************************************************
***
*** MILESTONES	
***
******************************************************************************************************************************************/
		$scope.get_milestones = function()
		{
			var final_data = {action: "get_milestones", csrf_gd : Cookies.get('csrf_gd')};
			$scope.file =  $http({
					method  : 'POST',
					data : $httpParamSerializerJQLike(final_data),
					url     : "milestone/ajax_get_all_milestone",
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
					var milestones_data = response.milestones;
					var count = response.count;	
					var users_count = response.users_count;	
					var organ_users = response.organ_users;
					var user_id = response.user_id;
					
					/* user global variable */
					$scope.user_id = user_id;
					/* milestones */
					var tmp_milestones = [];
					tmp_milestones.push({value: 0, label : '' });
					if(count > 0){
						var x = 0;
						while(x < count){
							tmp_milestones.push({value: milestones_data[x]['id'], label : milestones_data[x]['name'] });
							x++;
						}
					}
					$scope.milestonesDropdownList = tmp_milestones;
					
					/***** 
					Users dropdown default 
					*****/
					var tmp_users = [];
					if(users_count > 0){
						var z = 0;
						while(z < users_count){
							var user_name = organ_users[z]['first_name'] + " " + organ_users[z]['last_name'];
							tmp_users.push({value: organ_users[z]['user_id'], label : user_name});
							z++;
						}
						
						$scope.organ_usersList = tmp_users;	
					}else{
						tmp_users.push({value: 0, label : 'No users' });
						$scope.organ_usersList = tmp_users;
					}
					
					$scope.milestone_owner = getSelectedItem($scope.organ_usersList, {value:user_id});
					$scope.edit_milestone_owner = $scope.organ_usersList[0];	
					if($scope.load_all_tasks == 1){
						$scope.Tasks_list.draw();
					}
			});
		};
/******************************************************************************************************************************************
***
***  TASKS	
***
******************************************************************************************************************************************/	
	$( "#dashboard_main_tablist a[href='#task_main']" ).on('show.bs.tab', function (e) 
	{
		var has_update_in_calendar_tasks = $("#calendarTab_taskList_update").val();
		if($scope.load_all_tasks == 0 || has_update_in_calendar_tasks == '1'){
			$scope.get_tasks();
			$("#calendarTab_taskList_update").val('0');
		}
		var has_update_in_calendar_milestones = $("#calendarTab_milestoneList_update").val();
		if(has_update_in_calendar_milestones == '1'){
			$scope.get_milestones();
			$("#calendarTab_milestoneList_update").val('0');
		}
	});		
	/* Inline edit owner */
	$("#task_main").on("change", ".task_dropdown_owner", function()
	{
		var current_row = angular.element(this).parents('tr');
		var task_id = $(this).attr('data-task_id');
		var selected_owner_id = $(this).val();
		var data = { action : "edit_task_owner", task_id : task_id, owner_id : selected_owner_id };
		
		$scope.process_Task(data, "inline_edit_owner", "", 'milestone/ajax_inline_edit_owner');
	});
	
	/* Inline edit status */
	$("#task_main").on("change", ".task_dropdown_status", function()
	{
		var current_row = angular.element(this).parents('tr');
		var task_id = $(this).attr('data-task_id');
		var selected_status = $(this).val();
		var data = { action : "edit_task_status", task_id : task_id, status : selected_status };
		
		$scope.process_Task(data, "inline_edit_status", "", 'milestone/ajax_inline_edit_status');
	});
	
	/* Inline edit priority */
	$("#task_main").on("change", ".task_dropdown_priority", function()
	{
		var current_row = angular.element(this).parents('tr');
		var task_id = $(this).attr('data-task_id');
		var selected_priority = $(this).val();
		var data = { action : "edit_task_priority", task_id : task_id, priority : selected_priority };
		
		$scope.process_Task(data, "inline_edit_priority", "", 'milestone/ajax_inline_edit_priority');
	});
	

	/*************************************************************************************************
	On Click open Add Task modal
	*************************************************************************************************/
		angular.element('#add_task_modal').on('show.bs.modal', function(e) 
		{
			$scope.participants = [];
			
			var this_button = angular.element(e.relatedTarget);
			var m_id = $scope.milestonesDropdownList[0];
			var priority = $scope.priorityList[0];
			var status = $scope.statusList[0];
			
			$scope.$apply(function(){
				$scope.task_name = "";
				$scope.task_m_id = m_id;
				$scope.task_owner = getSelectedItem($scope.organ_usersList, {value:$scope.user_id}); 
				$scope._multipleUser.user_task = ""; 
				$scope.task_description = "";
				$scope.task_priority = priority;
				$scope.task_status = status;
			});
			
			angular.element("#task_start_date").val("");
			angular.element("#task_due_date").val("");
		});
	/*************************************************************************************************
	On Click open Edit Task modal
	*************************************************************************************************/
		angular.element('#edit_taskTab_task_modal').on('show.bs.modal', function(e) 
		{
			$scope.edit_participants = [];
			
			var this_button = angular.element(e.relatedTarget);
			var current_row = this_button.parents('tr');
			var row_data = $scope.Tasks_list.row( current_row ).data();
			var m_id = row_data[14];
			var comment_count = row_data[1];
			var task_id = row_data[0];
			var task_name = row_data[2];
			var startDate = row_data[4];
			var startDate_format = row_data[5];
			var dueDate = row_data[7];
			var dueDate_format = row_data[8];
			var dueDate_format_final  = row_data[9];
			var owner_id = row_data[10];
			var status_id = row_data[11];
			var priority_id = row_data[12];
			var participant_id = row_data[13];
			
			var tmp_m_id = getSelectedItem($scope.milestonesDropdownList, {value:m_id});
			var tmp_owner = getSelectedItem($scope.organ_usersList, {value:owner_id});
			
			if(tmp_m_id != undefined){
				var edit_taskTab_task_m_id = tmp_m_id;
			}else{
				var edit_taskTab_task_m_id = $scope.milestonesDropdownList[0];
			}
			if(tmp_owner != undefined){
				var edit_taskTab_task_owner = tmp_owner;
			}else{
				var edit_taskTab_task_owner = $scope.organ_usersList[0];
			}
			$scope.$apply(function(){
				$scope.edit_taskTab_task_id = task_id;
				$scope.edit_taskTab_task_name = task_name;
				
				$scope.edit_taskTab_task_description = row_data[3];
				$scope.edit_taskTab_task_m_id = edit_taskTab_task_m_id;
				$scope.edit_taskTab_task_owner = edit_taskTab_task_owner; 
				$scope.edit_taskTab_task_status = getSelectedItem($scope.statusList, {value:status_id});  
				$scope.edit_taskTab_task_priority = getSelectedItem($scope.priorityList, {value:priority_id});  
				
				angular.forEach( participant_id ,function(participant){
							angular.forEach($scope.organ_usersList,function(_user){
								if(_user.value == participant){
									$scope.edit_participants.push(_user);
								}
							});
						});	
				
				$scope._edit_taskTab_multipleUser.user_task = $scope.edit_participants; 
			});
			
			if(startDate == null || startDate == '0000-00-00'){
					angular.element('#edit_taskTab_task_start_date').val('');
			}else{
				angular.element('#edit_taskTab_task_start_date').val(startDate_format);
			}
			
			if(dueDate_format_final == '9999-99-99'){
				angular.element('#edit_taskTab_task_due_date').val('');
			}else{
				angular.element('#edit_taskTab_task_due_date').val(dueDate_format);
			}
			
			$scope.task_counter = 0;
			$scope.task_comments = [];
			$scope.get_task_comments(task_id);
		});
	/*************************************************************************************************
	On Click Delete Task : Show Delete Task Modal
	*************************************************************************************************/
		angular.element('#delete_task_modal').on('show.bs.modal', function(e) 
		{
			var current_row = angular.element(e.relatedTarget).parents('tr');
			var row_data = $scope.Tasks_list.row( current_row ).data();	
			var task_id = row_data[0];
			
			$scope.temp_delete_task_id = task_id;
		});
	/*************************************************************************************************
	On Click Save Task button 
	*************************************************************************************************/
		$scope.save_task = function()
		{
			$scope.participants = [];
			/* start date */
			if(angular.element('#task_start_date').val() != ''){
				var temp_start_date  =  angular.element('#task_start_date').val().split(/\//);
				var start_date = [ temp_start_date[1], temp_start_date[0], temp_start_date[2] ].join('/');
				var start_date_comparison = new Date(start_date);
			}else{
				var start_date = null;
			}
			/* due date */
			if(angular.element('#task_due_date').val() != ''){
				var temp_due_date = angular.element('#task_due_date').val().split(/\//);	
				var due_date = [ temp_due_date[1], temp_due_date[0], temp_due_date[2] ].join('/');
				var due_date_comparison = new Date(due_date);
			}else{
				var due_date = null;
			}
			
			angular.forEach($scope._multipleUser.user_task,function(user){
				$scope.participants.push(user.value);	
			});
				
			var data = {
				action : "add_task",
				name: $scope.task_name,
				m_id: $scope.task_m_id.value,
				owner_id: $scope.task_owner.value,
				participants: $scope.participants,
				description: $scope.task_description,
				start_date: start_date,
				due_date : due_date,
				priority: $scope.task_priority.value,
				status: $scope.task_status.value
			};
			var has_error = $scope.validate_Task("add", $scope.task_name, start_date, due_date, start_date_comparison, due_date_comparison, $scope.task_status.value, $scope.task_priority.value);
			if(has_error == false){
				$scope.process_Task(data, "add", "#add_task_modal", 'milestone/ajax_add_task');
			}
		}
	/*************************************************************************************************
	VALIDATE Milestone : Add, Edit
	*************************************************************************************************/	
		$scope.validate_Task = function(process_type, t_name, t_start_date, t_due_date, start_date_comparison, due_date_comparison, t_status, t_priority)
		{
			$scope.isopen_task_name = false;
			$scope.isopen_edit_taskTab_task_name = false;
			$scope.isopen_due_date = false;
			$scope.isopen_edit_taskTab_due_date = false;
			
			var error = false;
			if(parseInt(t_status) < 0 || parseInt(t_status) > 10){
				error = true;
			}
			
			if(parseInt(t_priority) < 1 || parseInt(t_priority) > 4){
				error = true;
			}
			
			if(t_name == ""){
				error = true;
				if(process_type == "add"){
					$scope.isopen_task_name = true;
					$scope._error_task_name = "Task Name is required";
				}else{
					$scope.isopen_edit_taskTab_task_name = true;
					$scope._error_edit_taskTab_task_name = "Task Name is required";
				}
			}
			
			if(t_start_date != null && t_due_date != null && t_start_date != t_due_date && start_date_comparison > due_date_comparison){
				error = true;
				if(process_type == "add"){
					$scope.isopen_due_date = true;
					$scope._error_t_due_date = "Start date is Greater than Due date.";
				}else{
					$scope.isopen_edit_taskTab_due_date = true;
					$scope._error_edit_taskTab_t_due_date = "Start date is Greater than Due date.";
				}
			}
			
			return error;	
		}	
	/*************************************************************************************************
	On Click Update Task button 
	*************************************************************************************************/
		$scope.update_task = function()
		{
			$scope.edit_participants = [];
			
			/* start date */
			if(angular.element('#edit_taskTab_task_start_date').val() != ''){
				var temp_start_date  =  angular.element('#edit_taskTab_task_start_date').val().split(/\//);
				var start_date = [ temp_start_date[1], temp_start_date[0], temp_start_date[2] ].join('/');
				var start_date_comparison = new Date(start_date);
			}else{
				var start_date = null;
			}
			/* due date */
			if(angular.element('#edit_taskTab_task_due_date').val() != ''){
				var temp_due_date = angular.element('#edit_taskTab_task_due_date').val().split(/\//);	
				var due_date = [ temp_due_date[1], temp_due_date[0], temp_due_date[2] ].join('/');
				var due_date_comparison = new Date(due_date);
			}else{
				var due_date = null;
			}
			
			angular.forEach($scope._edit_taskTab_multipleUser.user_task,function(user){
				$scope.edit_participants.push(user.value);	
			});
				
			var data = {
				action : "edit_task",
				name: $scope.edit_taskTab_task_name,
				task_id: $scope.edit_taskTab_task_id,
				m_id: $scope.edit_taskTab_task_m_id.value,
				owner_id: $scope.edit_taskTab_task_owner.value,
				participants: $scope.edit_participants,
				description: $scope.edit_taskTab_task_description,
				start_date: start_date,
				due_date : due_date,
				priority: $scope.edit_taskTab_task_priority.value,
				status: $scope.edit_taskTab_task_status.value
			};
			var has_error = $scope.validate_Task("edit", $scope.edit_taskTab_task_name, start_date, due_date, start_date_comparison, due_date_comparison, $scope.edit_taskTab_task_status.value, $scope.edit_taskTab_task_priority.value);
			if(has_error == false){
				$scope.process_Task(data, "edit", "#edit_taskTab_task_modal", 'milestone/ajax_edit_task');
			}
		}
	/*************************************************************************************************
	On Click Delete Task button 
	*************************************************************************************************/
		$scope.delete_task = function()
		{
			var data = { action : "delete_task", task_id: $scope.temp_delete_task_id };
			$scope.process_Task(data, "delete", "#delete_task_modal", 'milestone/ajax_delete_task');
		};
	/*************************************************************************************************
	OnPage load get all tasks 
	called by ng-init="get_tasks(tab_id)"
	*************************************************************************************************/
	$scope.get_tasks = function()
	{
		var final_data = {action: "get_tasks", csrf_gd : Cookies.get('csrf_gd')};
		$scope.file =  $http({
				method  : 'POST',
				data : $httpParamSerializerJQLike(final_data),
				url     : 'milestone/get_all_task_by_user',
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
				var tasks_data = response.tasks;
				var count = response.count;	
				var users_count = response.users_count;	
				var organ_users = response.organ_users;
				
				$scope.Tasks_list.clear().draw();
				
				if(count > 0){
					var x = 0;
					while(x < count){
						/* call function to add task to Milestone List / Milestone Datatable */
						$scope.add_task_toTaskDatatables(tasks_data[x]);
						x++;
					}
				}
				
				$scope.load_all_tasks = 1;
				
		});
	};		
/******************************************************************************************************************************************
***
*** Process POST of TASKS/Milestone TASKS : Add, Edit, Delete
***
******************************************************************************************************************************************/		
	$scope.process_Task =  function(data, process_type, modal_id, url){
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var final_data = angular.extend(data, csrf_object);
		
		$scope.file =  $http({
				method  : 'POST',
				data : $httpParamSerializerJQLike(final_data),
				url     : url,
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if(response.error == 0){
				var data = response.task;
				
				switch(process_type ){
					case "add" : 					$scope.add_task_toTaskDatatables(data);							break;
					case "edit":					$scope.update_task_toTaskDatatables(data);						break;
					case "delete":					$scope.delete_task_toTaskDatatables(data);						break;
					case "inline_edit_owner":		$scope.update_inline_task_toTaskDatatables(data, "owner_id");	break;
					case "inline_edit_status":		$scope.update_inline_task_toTaskDatatables(data, "status");		break;
					case "inline_edit_priority":	$scope.update_inline_task_toTaskDatatables(data, "priority");	break;
				}
				
				$("#taskTab_taskList_update").val('1');
				$scope.popup_alert_message('btn-success', "Success", response.message, modal_id);
				
			}else{
				$scope.popup_alert_message('btn-danger', "Error", response.message, modal_id);
			}
		}).error(function(){
			$scope.popup_alert_message('btn-danger', "Error", "Failed to process operation.", modal_id);
		});
	}	
/******************************************************************************************************************************************
***
***  TASKS DATATABLES 	
***
******************************************************************************************************************************************/	
	/*************************************************************************************************
	INLINE UPDATE Task from Task List Datatable
	*************************************************************************************************/
		$scope.update_inline_task_toTaskDatatables = function(data, column)
		{
			var data_count = Object.keys(data).length;
			var data_keys = Object.keys(data);
			var task_id = data.task_id
			var row_task_id = "#task_row-"+task_id;
			
			if( data_count > 0){
				var new_column_data = data[column];
				var task_column = angular.element(row_task_id).find(".t_"+column);
				
				if(task_column.length > 0)
				{
					$scope.Tasks_list.cell(task_column).data(new_column_data); 
				}
			}
		};
	/*************************************************************************************************
	Add Task from Tasks/Milestones Tasks Datatable
	*************************************************************************************************/
		$scope.add_task_toTaskDatatables = function(data)
		{
			var row_data = [
					data.task_id, 
					data.comment_count, 
					data.task_name, 
					data.task_description, 
					data.task_startDate, 
					data.task_startDate_format,
					data.task_startDate_format_final,
					data.task_dueDate,
					data.task_dueDate_format,
					data.task_dueDate_format_final, 
					data.owner_id,
					data.status, 
					data.priority, 
					data.participant_id, 
					data.milestone_id, 
					data.task_id
				];
			
				var rowNode = $scope.Tasks_list.row.add(row_data).draw().node();	
				if(angular.element("#taskdatatable_start_date-"+data.task_id).length > 0){
					startdatepicker(angular.element('#taskdatatable_start_date-'+data.task_id) );
					if(data.task_startDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_startDate_format;
					}
					$('#taskdatatable_start_date-'+data.task_id).val(value);
				}
				if(angular.element("#taskdatatable_due_date-"+data.task_id).length > 0){
					duedatepicker(angular.element('#taskdatatable_due_date-'+data.task_id) );
					if(data.task_dueDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_dueDate_format;
					}
					$('#taskdatatable_due_date-'+data.task_id).val(value);
				}
		};
		
		
		function startdatepicker(datepicker_id){
			var is_initialize = $(datepicker_id).attr('data-initialize');
			if(is_initialize == 0 || is_initialize == "0"){
				$(datepicker_id).datetimepicker({
					format: "DD/MM/YYYY",
					useCurrent: false
				}).on("dp.change", function(e) {
					var defaultdate = $(e.currentTarget).attr('data-defaultdate');
					var isclicked = $(e.currentTarget).attr('data-isclicked');
					$(datepicker_id).attr('data-isclicked', 1);
					
					if((e.oldDate == null && defaultdate == "-") || (isclicked == "1" || isclicked == 1)){
						var date = $(e.currentTarget).data('date');
						var id =  $(e.currentTarget).attr('data-task_id');
						var second  =  date.split(/\//);
						var temp_due_date = [ second[2], second[1], second[0] ].join('-');
						var final_data = {
							task_id : id,
							date: temp_due_date,
							action: "edit_task_start_date",
							csrf_gd : Cookies.get('csrf_gd')
						};
						$http({ method  : 'POST',
							url     : 'milestone/ajax_inline_edit_startDate',
							data    : $httpParamSerializerJQLike(final_data), 
							headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
						}).success(function(data){
								if(data.error == 1){
									$scope.popup_alert_message('btn-danger', "Error", data.message, "");
								}else{
									var task = data.task;
									var row_task_id = "#task_row-"+task['task_id'];
									
									var task_row = angular.element(row_task_id);
									if(task_row.length > 0){
										$scope.Tasks_list.rows(task_row).remove().draw();
										$scope.add_task_toTaskDatatables(task);	
										if(task.task_startDate_format_final == "9999-99-99"){
											var value = "-";
										}else{
											var value = task.task_startDate_format;
										}
										$('#taskdatatable_start_date-'+task.task_id).val(value);
									}
									$("#taskTab_taskList_update").val('1');
									$scope.popup_alert_message('btn-success', "Success", 'Start Date is updated.', "");
								}
						});
						
					}else{
						$(datepicker_id).attr('data-isclicked', 1);
					}
				});
				$(datepicker_id).attr('data-initialize', 1);
			}
		}
		
		function duedatepicker(datepicker_id){
				$(datepicker_id).datetimepicker({
					format: "DD/MM/YYYY",
					useCurrent: false
				}).on("dp.change", function(e) {
					var defaultdate = $(e.currentTarget).attr('data-defaultdate');
					var isclicked = $(e.currentTarget).attr('data-isclicked');
					
					$(datepicker_id).attr('data-isclicked', 1);
					
					if((e.oldDate == null && defaultdate == "-") || (isclicked == "1" || isclicked == 1)){
						var date = $(e.currentTarget).data('date');
						var id =  $(e.currentTarget).attr('data-task_id');
						var second  =  date.split(/\//);
						var temp_due_date = [ second[2], second[1], second[0] ].join('-');
						var final_data = {
							task_id : id,
							date: temp_due_date,
							action: "edit_task_due_date",
							csrf_gd : Cookies.get('csrf_gd')
						};
						$http({ method  : 'POST',
							url     : 'milestone/ajax_inline_edit_dueDate',
							data    : $httpParamSerializerJQLike(final_data), 
							headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
						}).success(function(data){
								
								if(data.error == 1){
									$scope.popup_alert_message('btn-danger', "Error", data.message, "");
								}else{
									var task = data.task;
									var row_task_id = "#task_row-"+task['task_id'];
									
									var task_row = angular.element(row_task_id);
									if(task_row.length > 0){
										$scope.Tasks_list.rows(task_row).remove().draw();
										$scope.add_task_toTaskDatatables(task);	
										if(task.task_dueDate_format_final == "9999-99-99"){
											var value = "-";
										}else{
											var value = task.task_dueDate_format;
										}
										$('#taskdatatable_due_date-'+task.task_id).val(value);
									}
									$("#taskTab_taskList_update").val('1');
									$scope.popup_alert_message('btn-success', "Success", 'Due Date is updated.', "");
								}
						});
						
					}else{
						$(datepicker_id).attr('data-isclicked', 1);
					}
				});
		}
	/*************************************************************************************************
	UPDATE Task from Tasks/Milestones Tasks Datatable
	*************************************************************************************************/
		$scope.update_task_toTaskDatatables = function(data)
		{
			var data_count = Object.keys(data).length;
			var data_keys = Object.keys(data);
			var task_id = data.task_id
			var row_task_id = "#task_row-"+task_id;
			
			
			if( data_count > 0)
			{
				/* Update task in Tasks List*/
				var x = 0;
				while(x < data_count)
				{
					var task_column = angular.element(row_task_id).find(".t_"+data_keys[x]);
					if(task_column.length > 0){
						$scope.Tasks_list.cell(task_column).data(data[data_keys[x]]); 
					}
					x++;
				}
				
				/* task */
				if(angular.element("#taskdatatable_start_date-"+data.task_id).length > 0){
					startdatepicker(angular.element('#taskdatatable_start_date-'+data.task_id));
					if(data.task_startDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_startDate_format;
					}
					$('#taskdatatable_start_date-'+data.task_id).val(value);
				}
				if(angular.element("#taskdatatable_due_date-"+data.task_id).length > 0){
					duedatepicker(angular.element('#taskdatatable_due_date-'+data.task_id));
					if(data.task_dueDate_format_final == "9999-99-99"){
						var value = "-";
					}else{
						var value = data.task_dueDate_format;
					}
					$('#taskdatatable_due_date-'+data.task_id).val(value);
				}
			}
		};
	/*************************************************************************************************
	Delete Task from Task List Datatable
	*************************************************************************************************/
		$scope.delete_task_toTaskDatatables = function(task_id)
		{
			var task_row = angular.element("#task_row-"+task_id);
			var milestone_task_row = angular.element("#milestone_task_row-"+task_id);
			if(task_row.length > 0){
				$scope.Tasks_list.rows(task_row).remove().draw();	
			}
		};
/******************************************************************************************************************************************
***
***  TASK COMMENTS Functions
***
******************************************************************************************************************************************/				
	/*************************************************************************************************
	OnPage edit task modal get task comments
	*************************************************************************************************/
		$scope.get_task_comments = function(task_id)
		{
			var final_data = {action: "get_all_task_comment", csrf_gd : Cookies.get('csrf_gd'), task_id : task_id};
			$scope.file =  $http({
					method  : 'POST',
					data : $httpParamSerializerJQLike(final_data),
					url     : "milestone/ajax_get_all_task_comment",
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				$scope.task_comments = response.comments;
				$scope.task_counter = response.count;
			});
		};	
	$scope.comment_profile_pic = function (profile_pic, user_id){
			if(profile_pic == null || profile_pic == "null"){
				return base_url +"public/images/unknown.png";
			}else{
				return base_url +"uploads/"+user_id+"/"+profile_pic;
			}
		}	
		
	/*************************************************************************************************
	Process POST of Milestone : Add, Edit, Delete
	*************************************************************************************************/
		$scope.process_TaskComment =  function(data, process_type, url)
		{
			var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
			var final_data = angular.extend(data, csrf_object);
			
			$scope.file =  $http({
					method  : 'POST',
					data : $httpParamSerializerJQLike(final_data),
					url     : url,
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				if(response.error == 0){
					switch(process_type ){
						case "add" : 	
							var data = response.comment;
							$scope.task_comment.comment = '';
							$scope.task_comments.push(data);
							$scope.task_counter += 1;
						break;
						case "edit": 
							
							
						break;
						case "delete":	
							var comment_index = $filter('GetIndexNumeric')($scope.task_comments,{task_progress_id:response.task_progress_id} );
							if($scope.task_comments[comment_index] != undefined) {
								$scope.task_comments.splice(comment_index, 1);
								$scope.task_counter -= 1;
							}
						break;
					}
					
					$scope.popup_alert_message('btn-success', "Success", response.message, "");
				}else{
					$scope.popup_alert_message('btn-danger', "Error", response.message, "");
					
				}
			}).error(function()
			{
				$scope.popup_alert_message('btn-danger', "Error", "Failed to process operation.", "");
			});
		}	
	/* Save task Comment */
		$scope.save_task_comment = function(){
			if($scope.task_comment.comment ==  undefined){
                  $scope.comment_error_message = 'Comment field is required.'
                  $scope.comment_field = true;
                  return;
            }    
			
			var data = {
				action : "add_task_comment",
				comment: $scope.task_comment.comment,
				task_id : $scope.edit_taskTab_task_id
			};
			$scope.process_TaskComment(data, "add", 'milestone/ajax_add_task_comment');
			
		}

	/* Save update Comment */
		$scope.save_update_comment = function($comment,$comment_id,$task_id){
			if($comment ==  undefined || $comment == ""){
                  $scope.comment_error_message = 'Comment field is required.'
                  $scope.comment_field = true;
                  return;
            }    
			var data = {
				action : "edit_task_comment",
				comment: $comment,
				comment_id : $comment_id,			
				task_id : $task_id			
			};

			$scope.process_TaskComment(data, "edit", 'milestone/ajax_edit_task_comment');
		}
	/* Delete Comment */
		$scope.delete_comment = function($id,$task_id){
			var data = {
					action : "delete_task_comment",
					comment_id:$id, 
					task_id:$task_id
				};
			$scope.process_TaskComment(data, "delete", 'milestone/ajax_delete_task_comment');
		}
		
	/* Edit and Delete tooltip  */
		$scope.tooltip_comment = function(type){
			if(type == "delete"){
				$('.delete_comment_btn').tooltip({placement: "left"}); 	
			}else{
				$('.edit_comment_btn').tooltip({placement: "bottom"}); 
			}
		}
	/*************************************************************************************************
		Popup Alert Message
	*************************************************************************************************/
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
/******************************************************************************************************************************************
***
***  END CONTROLLER	
***
******************************************************************************************************************************************/		
}); 

/******************************************************************************************************************** 
Function extension
*********************************************************************************************************************/
function GetIndexNumeric(){
	return function(items, props) {
		if (angular.isArray(items)) {
			var itemMatches = false;
			var value_index = 0;
			var indexKey = false;
			items.forEach(function(item) {
				var keys = Object.keys(props);
				for (var i = 0; i < keys.length; i++) {
					var prop = keys[i];
					var value = props[prop];
					if(item[prop] != undefined){
						if (item[prop].toString().toLowerCase().indexOf(value) !== -1) {
							itemMatches = true;
							break;
						}
					}
				}
				if(itemMatches){ return false; }	 
				value_index++;
			});
			return value_index;
		}else{
			return 0;
		}
	};
}
