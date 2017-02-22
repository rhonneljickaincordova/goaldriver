angular.module('moreApps')
.filter('propsFilter', function() {
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
      /* Let the output be the input untouched */
      out = items;
    }

    return out;
  };
})
.filter('GetIndexNumeric', GetIndexNumeric)
.directive('clickOff', function($parse, $document) {
    var dir = {
        compile: function($element, attr) {
          var fn = $parse(attr["clickOff"]);
          return function(scope, element, attr) {
            element.bind("click", function(event) {
              event.stopPropagation();
            });
            angular.element($document[0].body).bind("click",function(event) {
                scope.$apply(function() {
                    fn(scope, {$event:event});
                });
            });
          };
        }
      };
    return dir;
})
.directive('hideCommentEditTextarea', function($parse, $document) {
	var dir = {
        compile: function($element, attr) {
		var fn = $parse(attr["hideCommentEditTextarea"]);
          return function(scope, element, attr) {
            element.bind("click", function(event) {
				event.stopPropagation();
			});
            angular.element($document[0].body).bind("click",function(event) {
				if(scope.show_task_comment_container == true){
					scope.$apply(function() {
						fn(scope, {$event:event});
					});	
				}
			});
			element.find('.edit_task_comment').bind("click",function(event) {
				if(scope.show_task_comment_container == true){
					scope.$apply(function() {
						fn(scope, {$event:event});
					});	
				}
			});
          };
        }
      };
    return dir;
})
.controller('calendarTabCtrl', function ($scope,$http,$compile,uiCalendarConfig, $timeout,$filter,$httpParamSerializerJQLike){
	/*************************************************************************************************
	Declare Variables
	*************************************************************************************************/
	$scope.EventsCalendar = "";
	
	$scope.selectedAll = false;
	$scope.list_of_selected = [];
	$scope.delete_data = false;
	$scope.update_dates = false;

	$scope.overdue_tasks = [];
	$scope.overdue_milestones = [];
	$scope.overdue_meetings = [];
    $scope.duetoday_tasks = [];
    $scope.duetoday_milestones = [];
    $scope.duetoday_meetings = [];
	$scope.upcoming_tasks = [];	
	$scope.upcoming_milestones = [];	
	$scope.upcoming_meetings = [];	
	$scope.no_dueDate_tasks = [];
	$scope.no_dueDate_milestones = [];
	$scope.no_dueDate_meetings = [];
	$scope.overdue_counter = 0;
	$scope.duetoday_counter = 0;
	$scope.upcoming_counter = 0;
	$scope.load_all_tasks = 0;
	$scope.load_all_milestones = 0;
	$scope.load_all_meetings = 0;
	
	$scope.all_milestone = [];
	$scope.milestonesDropdownList = [];
	
	var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

	$scope._edit_multipleUser = {};
	$scope._multipleUser = {};
    
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
		
	$scope.events = [{
		title: '',
		start: new Date(),
		end : new Date(),
  	}];
	
	$scope.calendar_month_year = "";
/******************************************************************************************************************************************
***
***  CALENDAR TAB Functions
***
******************************************************************************************************************************************/	
	$scope.init_functions = function()
	{
		$('#edit_task_start_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false });
		$('#edit_task_due_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });
		$('#edit_milestone_due_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });
		$('#edit_milestone_start_date').datetimepicker({ format :  "DD/MM/YYYY", useCurrent: false  });
		
		if(angular.element("#milestone_permission_name").val() == "readwrite"){
			$scope.show_readwrite_btn = true;
			$scope.show_add_comment_div = true;
			$scope.disabled = false;
		}else{
			$scope.show_readwrite_btn = false;
			$scope.show_add_comment_div = false;
			$scope.readonly_event = true;
			$scope.disabled = true;
			angular.element("#edit_task_start_date").attr("readonly", true);
			angular.element("#edit_task_due_date").attr("readonly", true);
			angular.element("#edit_milestone_due_date").attr("readonly", true);
			angular.element("#edit_milestone_start_date").attr("readonly", true);
		}
	}
	/*************************************************************************************************
	OnClick Calendar Tab
	*************************************************************************************************/
	$( '#dashboard_main_tablist a[href="#calendar_main_tab"]' ).on('show.bs.tab', function (e) 
	{
		$scope.renderCalendar('event_calendar_main');
		$scope.get_userNotification();
		$scope.on_click_view_type('calendar');

		if($scope.load_all_milestones == 0){
			$scope.get_milestones();
		}
	});
	
	$scope.renderCalendar = function (calendarId) {
		$timeout(function () {
			calendarTag = $('#' + calendarId);
			$scope.EventsCalendar = calendarTag.fullCalendar('render');
		}, 0);
	};
	
	$timeout(function () {
        $scope.renderCalendar('calendar');
    }, 1000);
	/*************************************************************************************************
	Get Monthly Events : Milestones, Tasks and Meetings
	*************************************************************************************************/
	$scope.get_monthly_schedule = function(month_year)
	{
		$scope.loading_processing('show');
		$scope.calendar_month_year = month_year;
		var final_data = {action: 'get_monthly_schedule', month_year: month_year, csrf_gd : Cookies.get('csrf_gd')};
		$scope.file =  $http({
			method  : 'POST',
			url     : 'Dashboard/get_monthly_schedule',
			data    :   $httpParamSerializerJQLike(final_data), 
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		 }).success(function(data)
		 {
				angular.forEach(data.events,function(event){
					event.end = Date.parse(event.end);
					$scope.events.push(event);
				});
				
				$scope.loading_processing('hide');
		})
		.error(function(e){
			$scope.loading_processing('hide');
		});
	}
	
	 $scope.viewRender = function (view,element){
		$scope.get_monthly_schedule(view.title);
	}  
   
   $scope.on_click_view_type = function($type){
		if($type == 'calendar'){
			$scope.calendar_view = true;
			$scope.list_view = false;
			$(".li_calendarView_icon").addClass("active");
		}else{
			$scope.list_view = true;
			$scope.calendar_view = false;
			$(".li_listView_icon").removeClass("active");
			
			if($scope.load_all_meetings == 0){
				$scope.get_meetings();
			}
			
			if($scope.load_all_milestones == 0){
				setTimeout(function(){
					$scope.get_milestones();
				},1000);
			}
			
			var has_update_in_tasks_tasks = $("#taskTab_taskList_update").val();
			if($scope.load_all_tasks == 0 || has_update_in_tasks_tasks == '1'){
				setTimeout(function(){
					$scope.get_tasks();
					$("#taskTab_taskList_update").val('0');
				},1000);
			}
		}
	}

	$scope.alertOnEventClick = function( event, jsEvent, view){
		$scope.calendar_event_id = event.id;
		$scope.meeting_click = false;
		if(event.id != null)
		{
			switch(event.className[0]){
				case 'label-success':	$scope.get_task(event.id);			break;
				case 'label-warning':	$scope.get_milestone(event.id);		break;
				case 'label-info':		$scope.get_meeting(event.id);		break;				
			}
		}
    };
    /*************************************************************************************************
	Google Calendar Config Object
	*************************************************************************************************/
    $scope.uiConfig = {
		googleCalendarApiKey:'AIzaSyDQaaOge72YWpRMAZdkltLo76LRbZ-91Pc',
        events: {
            googleCalendarId:'moreplan2016@gmail.com',
             className: 'gcal-event' 
        },	
		calendar:{
	        height: 700,
	        editable: true,
	       	header:{
	          left: 'title',
	          center: '',
	          right: 'today prev,next'
	        },
	        dayClick :$scope.dayClick,
			eventStartEditable : false,
	        eventClick: $scope.alertOnEventClick,
	        eventRender: $scope.eventRender,
	        viewRender: $scope.viewRender,
	   	    eventRender: function(event, eventElement) {
	   	    	switch(event.className[0]) {
					case 'label-success':	
						/* task */
						if (event.imageurl) {
							eventElement.find("div.fc-content").prepend("<img src='" + event.imageurl +"' width='12' height='12'  style='padding-bottom:2px;'>");
						}
						break;
					case 'label-warning':	
						/* milestone */
						if (event.imageurl) {
							eventElement.find("div.fc-content").prepend("<img src='" + event.imageurl +"' width='12' height='12'  style='padding-bottom:2px;'>");
						}
						break;
					case 'label-info':	
						/* meeting */
						if (event.imageurl) {
							eventElement.find("div.fc-content").prepend("<img src='" + event.imageurl +"' width='12' height='12' style='padding-bottom:2px;'>");
						}
						break;
				}	
			}
		}
	};
	
	
	$scope.eventSources = [$scope.events];
	
/******************************************************************************************************************************************
***
*** MILESTONES	
***
******************************************************************************************************************************************/	
	/*************************************************************************************************
	OnPage load get all milestones 
	*************************************************************************************************/
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
					var organ_users = response.organ_users;
					var users_count = response.users_count;	
					
					
					$scope.all_milestone = milestones_data; 
					$scope.overdue_milestones = [];
					$scope.duetoday_milestones = [];
					$scope.upcoming_milestones = [];
					
					var tmp_milestones = [];
					tmp_milestones.push({value: 0, label : '' });
					angular.forEach(milestones_data, function(milestone)
					{
						tmp_milestones.push({value: milestone.id, label : milestone.name });
						var milestone_group_type = $scope.determine_group_type(milestone.dueDate);
					
						switch(milestone_group_type){
							case "overdue": 	$scope.overdue_milestones.push(milestone);	break;
							case "duetoday": 	$scope.duetoday_milestones.push(milestone);	break;
							case "upcoming": 	$scope.upcoming_milestones.push(milestone);	break;
						}
					});
					
					$scope.milestonesDropdownList = tmp_milestones;
					$scope.load_all_milestones = 1;
					
					/* Organisation Users */
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
			});
		};
	/*************************************************************************************************
	Get Milestone Details
	*************************************************************************************************/		
	$scope.get_milestone = function(m_id)
	{
		var final_data = {m_id : m_id, csrf_gd : Cookies.get('csrf_gd'), action: "get_milestone"};
				
		$scope.file =  $http({
			  method  : 'POST',
			  url     : 'Dashboard/ajax_get_milestone',
			  data    :  $httpParamSerializerJQLike(final_data), 
			  headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response)
		{
			if(response.error == 0)
			{
				var milestone = response.milestone;
				$scope.edit_milestone_type = $scope.determine_group_type(milestone.dueDate);
				$scope.event_id = milestone.id;
				
				$scope.edit_milestone_id = milestone.id;
				$scope.edit_milestone_name = milestone.name;
				$scope.edit_milestone_description = milestone.description;
				
				$scope.edit_milestone_status = $scope.statusList[0];
				var status_index = getSelectedItem($scope.statusList, {value:milestone.status});
				if(status_index != undefined){
					$scope.edit_milestone_status = 	status_index;
				}
				
				
				$scope.edit_milestone_owner = $scope.organ_usersList[0];
				angular.forEach($scope.organ_usersList,function(data){
					if(data.value == milestone.owner_id ){
						$scope.edit_milestone_owner = data;
					}
				});
				
				angular.element('#edit_milestone_due_date').val('');
				angular.element('#edit_milestone_start_date').val('');
				if(milestone.dueDate != null && milestone.dueDate != '0000-00-00'){
					angular.element('#edit_milestone_due_date').val(milestone.dueDate_format);
				}

				if(milestone.startDate != null && milestone.startDate != '0000-00-00'){
					angular.element('#edit_milestone_start_date').val(milestone.startDate_format);	
				}
				
				$('#edit_milestone_modal').modal('show');
			}
			else
			{
				$scope.popup_alert_message('btn-danger', "Error", response.message, "#edit_milestone_modal");
			}
		});
	}	
	/*************************************************************************************************
	Get Milestone Details
	*************************************************************************************************/
	$scope.open_milestone_name_link = function($id){
		if($id != null)
		{	
			$scope.get_milestone($id);;
		}
	}
	/************************************************************************************************* 
	On Click Update Milestone button 
	*************************************************************************************************/
		$scope.update_milestone = function()
		{
			/* start date */
			if(angular.element('#edit_milestone_start_date').val() != ''){
				var temp_start_date  =  angular.element('#edit_milestone_start_date').val().split(/\//);
				var start_date = [ temp_start_date[1], temp_start_date[0], temp_start_date[2] ].join('/');
				var start_date_comparison = new Date(start_date);
			}else{
				var start_date = null;
			}
			/* due date */
			if(angular.element('#edit_milestone_due_date').val() != ''){
				var temp_due_date = angular.element('#edit_milestone_due_date').val().split(/\//);	
				var due_date = [ temp_due_date[1], temp_due_date[0], temp_due_date[2] ].join('/');
				var due_date_comparison = new Date(due_date);
			}else{
				var due_date = null;
			}
			
			var final_data = {
				action : "edit_milestone",
				m_id: $scope.edit_milestone_id,
				name: $scope.edit_milestone_name,
				owner_id: $scope.edit_milestone_owner.value,
				description: $scope.edit_milestone_description,
				status: $scope.edit_milestone_status.value,
				start_date: start_date,
				due_date : due_date,
				csrf_gd : Cookies.get('csrf_gd')
			};
			var has_error = $scope.validate_Milestone($scope.edit_milestone_name, $scope.edit_milestone_status.value, start_date, due_date, start_date_comparison, due_date_comparison);
			if(has_error == false){
				$scope.process_edit_Milestone(final_data);
			}
		}
		$scope.process_edit_Milestone = function(final_data){
			$scope.file =  $http({
				method  : 'POST',
				url     : 'milestone/ajax_edit_milestone',
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				if(response.error == 0){
					var milestone = response.milestone;
					if($scope.load_all_milestones == 1){
						var milestone_group_type = $scope.determine_group_type(milestone.ueDate);
						
						if($scope.edit_milestone_type != milestone_group_type){
							/* remove to group */
							switch($scope.edit_milestone_type){
								case "overdue":
									var milestone_index = $filter('GetIndexNumeric')($scope.overdue_milestones,{id:milestone.id} );
									if($scope.overdue_milestones[milestone_index] != undefined){
										$scope.overdue_milestones.splice(milestone_index, 1);
									}	
								break;
								case "duetoday": 
									var milestone_index = $filter('GetIndexNumeric')($scope.duetoday_milestones,{id:milestone.id} );
									if($scope.duetoday_milestones[milestone_index] != undefined){
										$scope.duetoday_milestones.splice(milestone_index, 1);
									}	
								break;
								case "upcoming": 
									var milestone_index = $filter('GetIndexNumeric')($scope.upcoming_milestones,{id:milestone.id} );
									if($scope.upcoming_milestones[milestone_index] != undefined){
										$scope.upcoming_milestones.splice(milestone_index, 1);
									}	
								break;
							}
							/* add to group */
							switch(milestone_group_type){
								case "overdue": 	$scope.overdue_milestones.push(milestone);	break;
								case "duetoday": 	$scope.duetoday_milestones.push(milestone);	break;
								case "upcoming": 	$scope.upcoming_milestones.push(milestone);	break;
							}
						}else{
							/* update */
							switch($scope.edit_milestone_type){
								case "overdue": 
									var milestone_index = $filter('GetIndexNumeric')($scope.overdue_milestones,{id:milestone.id} );
									if($scope.overdue_milestones[milestone_index] != undefined){
										$scope.overdue_milestones[milestone_index] = milestone;
									}
								break;
								case "duetoday": 
									var milestone_index = $filter('GetIndexNumeric')($scope.duetoday_milestones,{id:milestone.id} );
									if($scope.duetoday_milestones[milestone_index] != undefined){
										$scope.duetoday_milestones[milestone_index] = milestone;
									}
								break;
								case "upcoming": 
									var milestone_index = $filter('GetIndexNumeric')($scope.upcoming_milestones,{id:milestone.id} );
									if($scope.upcoming_milestones[milestone_index] != undefined){
										$scope.upcoming_milestones[milestone_index] = milestone;
									}
								break;
							}
						}
					
					}
					
					if($scope.calendar_month_year != ""){
						$scope.events.splice(0);
						$scope.get_monthly_schedule($scope.calendar_month_year);
					}
					
					$("#calendarTab_milestoneList_update").val('1');
					$scope.popup_alert_message('btn-success', "Success", response.message, "#edit_milestone_modal");
				}
				else
				{
					$scope.popup_alert_message('btn-danger', "Error", response.message, "#edit_milestone_modal");
				}	
			}).error(function()
			{
				$scope.popup_alert_message('btn-danger', "Error", "Failed to process operation.", "#edit_milestone_modal");
			});
	}
	/*************************************************************************************************
	VALIDATE Milestone : Edit
	*************************************************************************************************/	
		$scope.validate_Milestone = function(m_name, m_status, m_start_date, m_due_date, start_date_comparison, due_date_comparison)
		{
			$scope.isopen_edit_name = false;
			$scope.isopen_edit_milestone_date = false;
			
			var error = false;
			if(parseInt(m_status) < 0 || parseInt(m_status) > 10){
				error = true;
			}
			
			if(m_name == ""){
				error = true;
				$scope.isopen_edit_name = true;
				$scope._error_edit_name = "Milstone Name is required";
			}
			
			if(m_start_date != null && m_due_date != null && m_start_date != m_due_date && start_date_comparison > due_date_comparison){
				error = true;
				$scope.isopen_edit_milestone_date = true;
				$scope._error_edit_milestone_date = "Start date is Greater than Due date.";
			}
			
			return error;	
		}
/******************************************************************************************************************************************
***
***  TASKS	
***
******************************************************************************************************************************************/	
	
	/*************************************************************************************************
	get all tasks 
	*************************************************************************************************/
		$scope.get_tasks = function(tab_id)
		{
			var final_data = {action: "get_tasks", csrf_gd : Cookies.get('csrf_gd')};
			$scope.file =  $http({
					method  : 'POST',
					data : $httpParamSerializerJQLike(final_data),
					url     : "milestone/ajax_get_all_task",
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
					var tasks_data = response.tasks;
					var count = response.count;	
					
					$scope.overdue_tasks = [];
					$scope.duetoday_tasks = [];
					$scope.upcoming_tasks = [];
					
					angular.forEach(tasks_data, function(task)
					{
						var task_group_type = $scope.determine_group_type(task.task_dueDate);
						
						switch(task_group_type){
							case "overdue": 	$scope.overdue_tasks.push(task);	break;
							case "duetoday": 	$scope.duetoday_tasks.push(task);	break;
							case "upcoming": 	$scope.upcoming_tasks.push(task);	break;
						}
					});
					
					$scope.load_all_tasks = 1;
			});
		};		
	/*************************************************************************************************
	Get Task Details and Comments
	*************************************************************************************************/		
	$scope.get_task = function(task_id)
	{
		var final_data = {task_id : task_id, csrf_gd : Cookies.get('csrf_gd'), action: "get_task"};
		
		$scope.file =  $http({
			  method  : 'POST',
			  url     : 'Dashboard/ajax_get_task',
			  data    :  $httpParamSerializerJQLike(final_data), 
			  headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response)
		{
			if(response.error == 0){
				var task = response.task;
				$scope.event_id = task.task_id;
				$scope.edit_task_type = $scope.determine_group_type(task.task_dueDate);
						
				$scope.edit_task_id = task.task_id;
				$scope.edit_task_name = task.task_name;
				
				$scope.edit_task_status = getSelectedItem($scope.statusList, {value:task.status});  
				$scope.edit_task_priority = getSelectedItem($scope.priorityList, {value:task.priority});  
				
	
				var tmp_owner = getSelectedItem($scope.organ_usersList, {value:task.owner_id});
				if(tmp_owner != undefined){
					$scope.edit_task_owner = tmp_owner;
				}else{
					$scope.edit_task_owner = $scope.organ_usersList[0];
				}
				
				
				var tmp_m_id = getSelectedItem($scope.milestonesDropdownList, {value:task.milestone_id});
				if(tmp_m_id != undefined){
					$scope.edit_task_m_id = tmp_m_id;
				}else{
					$scope.edit_task_m_id = $scope.milestonesDropdownList[0];
				}
				
				$scope.edit_participants = [];
				angular.forEach( task.participant_id ,function(participant){
							angular.forEach($scope.organ_usersList,function(_user){
								if(_user.value == participant){
									$scope.edit_participants.push(_user);
								}
							});
						});	
				
				$scope._edit_multipleUser.user_task = $scope.edit_participants; 
				
				angular.element('#edit_task_start_date').val('');
				angular.element('#edit_task_due_date').val('');
				
				if(task.task_startDate != null && task.task_startDate != '0000-00-00'){
					angular.element('#edit_task_start_date').val(task.task_startDate_format);
				}
				if(task.task_dueDate != null && task.task_dueDate != '0000-00-00'){
					angular.element('#edit_task_due_date').val(task.task_dueDate_format);
				}
			
				$scope.task_comments = [];
				
				if(task.comment_count > 0){
					$scope.get_task_comments(task_id);	
				}
				$('#edit_task_modal').modal('show');
			}else{
				$scope.popup_alert_message('btn-danger', "Error", response.message, "");
			}
			
		});
	}
	/*************************************************************************************************
	OnClick Show Edit Event - Task Modal
	*************************************************************************************************/		
    $scope.open_task_name_link = function($id)
	{
		$scope.get_task($id);    	
	}
	$scope.update_task = function(){
		var error = false; 
		$scope.edit_participants = [];
		
		/* start date */
		if(angular.element('#edit_task_start_date').val() != ''){
			var temp_start_date  =  angular.element('#edit_task_start_date').val().split(/\//);
			var start_date = [ temp_start_date[1], temp_start_date[0], temp_start_date[2] ].join('/');
			var start_date_comparison = new Date(start_date);
		}else{
			var start_date = null;
		}
		/* due date */
		if(angular.element('#edit_task_due_date').val() != ''){
			var temp_due_date = angular.element('#edit_task_due_date').val().split(/\//);	
			var due_date = [ temp_due_date[1], temp_due_date[0], temp_due_date[2] ].join('/');
			var due_date_comparison = new Date(due_date);
		}else{
			var due_date = null;
		}
		angular.forEach($scope._edit_multipleUser.user_task,function(user){
			$scope.edit_participants.push(user.value);	
		});
			
			
		var final_data = {
			action : "edit_task",
			name: $scope.edit_task_name,
			task_id: $scope.edit_task_id,
			m_id: $scope.edit_task_m_id.value,
			owner_id: $scope.edit_task_owner.value,
			participants: $scope.edit_participants,
			description: $scope.edit_task_description,
			start_date: start_date,
			due_date : due_date,
			priority: $scope.edit_task_priority.value,
			status: $scope.edit_task_status.value,
			csrf_gd : Cookies.get('csrf_gd')
		};
		
		var has_error = $scope.validate_Task($scope.edit_task_name, start_date, due_date, start_date_comparison, due_date_comparison, $scope.status, $scope.priority);
		if(has_error == false){
			$scope.process_edit_Task(final_data);
		}	
	}
	$scope.process_edit_Task = function(final_data){
			$scope.file =  $http({
				method  : 'POST',
				url     : 'milestone/ajax_edit_task',
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				if(response.error == 0){
					var task = response.task;
					if($scope.load_all_tasks == 1){
						var task_group_type = $scope.determine_group_type(task.task_dueDate);
						
						if($scope.edit_task_type != task_group_type){
							/* remove to group */
							switch($scope.edit_task_type){
								case "overdue":
									var task_index = $filter('GetIndexNumeric')($scope.overdue_tasks,{task_id:task.task_id} );
									if($scope.overdue_tasks[task_index] != undefined){
										$scope.overdue_tasks.splice(task_index, 1);
									}	
								break;
								case "duetoday": 
									var task_index = $filter('GetIndexNumeric')($scope.duetoday_tasks,{task_id:task.task_id} );
									if($scope.duetoday_tasks[task_index] != undefined){
										$scope.duetoday_tasks.splice(task_index, 1);
									}	
								break;
								case "upcoming": 
									var task_index = $filter('GetIndexNumeric')($scope.upcoming_tasks,{task_id:task.task_id} );
									if($scope.upcoming_tasks[task_index] != undefined){
										$scope.upcoming_tasks.splice(task_index, 1);
									}	
								break;
							}
							/* add to group */
							switch(task_group_type){
								case "overdue": 	$scope.overdue_tasks.push(task);	break;
								case "duetoday": 	$scope.duetoday_tasks.push(task);	break;
								case "upcoming": 	$scope.upcoming_tasks.push(task);	break;
							}
						}else{
							/* update */
							switch($scope.edit_task_type){
								case "overdue": 
									var task_index = $filter('GetIndexNumeric')($scope.overdue_tasks,{task_id:task.task_id} );
									if($scope.overdue_tasks[task_index] != undefined){
										$scope.overdue_tasks[task_index] = task;
									}
								break;
								case "duetoday": 
									var task_index = $filter('GetIndexNumeric')($scope.duetoday_tasks,{task_id:task.task_id} );
									if($scope.duetoday_tasks[task_index] != undefined){
										$scope.duetoday_tasks[task_index] = task;
									}
								break;
								case "upcoming": 
									var task_index = $filter('GetIndexNumeric')($scope.upcoming_tasks,{task_id:task.task_id} );
									if($scope.upcoming_tasks[task_index] != undefined){
										$scope.upcoming_tasks[task_index] = task;
									}
								break;
							}
						}
						
					}
					
					if($scope.calendar_month_year != ""){
						$scope.events.splice(0);
						$scope.get_monthly_schedule($scope.calendar_month_year);	
					}
					
					$("#calendarTab_taskList_update").val('1');
					$scope.popup_alert_message('btn-success', "Success", response.message, "#edit_task_modal");
				}
				else
				{
					$scope.popup_alert_message('btn-danger', "Error", response.message, "#edit_task_modal");
				}	
			}).error(function()
			{
				$scope.popup_alert_message('btn-danger', "Error", "Failed to process operation.", "#edit_task_modal");
			});
	}
	
	/*************************************************************************************************
	VALIDATE Milestone : Edit
	*************************************************************************************************/	
		$scope.validate_Task = function(t_name, t_start_date, t_due_date, start_date_comparison, due_date_comparison, t_status, t_priority)
		{
			$scope.isopen_edit_task_name = false;
			$scope.isopen_edit_due_date = false;
			
			var error = false;
			if(parseInt(t_status) < 0 || parseInt(t_status) > 10){
				error = true;
			}
			
			if(parseInt(t_priority) < 1 || parseInt(t_priority) > 4){
				error = true;
			}
			
			if(t_name == ""){
				error = true;
				$scope.isopen_edit_task_name = true;
				$scope._error_edit_task_name = "Task Name is required";
			}
			
			if(t_start_date != null && t_due_date != null && t_start_date != t_due_date && start_date_comparison > due_date_comparison){
				error = true;
				$scope.isopen_edit_due_date = true;
				$scope._error_edit_t_due_date = "Start date is Greater than Due date.";
			}
			
			return error;	
		}
	/*************************************************************************************************
	Determine Group Type : Task and Milestone
	*************************************************************************************************/	
	$scope.determine_group_type = function(due_date)
	{
		var type = 'overdue';
		
		if(due_date != null && due_date != "0000-00-00"){
			var temp_duedate = new Date(due_date);
			var current_date = $scope.get_current_date();
			
			if(current_date.date_formatted == due_date){
				type = "duetoday";
			}else if( temp_duedate > current_date.current_date){
				
			}
		}
		
		return type;					
	}	
	
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
						/* 	var comment_index = $filter('GetIndexNumeric')($scope.task_comments,{task_progress_id:response.task_progress_id} );
							if($scope.task_comments[comment_index] != undefined) {
								$scope.task_comments = 
								$scope.task_counter -= 1;
							}
							 */
						break;
						case "delete":	
							var comment_index = $filter('GetIndexNumeric')($scope.task_comments,{task_progress_id:response.task_progress_id} );
							if($scope.task_comments[comment_index] != undefined) {
								$scope.task_comments.splice(comment_index, 1);
								$scope.task_counter -= 1;
							}
						break;
					}
						$.alert({
							title: 'Success',
							content: response.message,
							confirmButtonClass: 'btn-success'
						});
				}else{
					$.alert({
						title: 'Error',
						content: response.message,
						confirmButtonClass: 'btn-danger'
					});
				}
			}).error(function(){
				$.alert({
					title: 'Error',
					content: "Failed to process operation.",
					confirmButtonClass: 'btn-danger'
				});
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
				task_id : $scope.edit_task_id
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
/******************************************************************************************************************************************
***
***  MEETINGS	
***
******************************************************************************************************************************************/		
	$scope.get_meetings = function(){
		var final_data = {action: 'get_meetings', csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
			method  : 'POST',
			url     : 'Dashboard/ajax_get_all_meetings',
			data    : $httpParamSerializerJQLike(final_data),
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
				if(response.count != undefined){
					var meetings_data = response.meetings;
					var count = response.count;	
					
					$scope.overdue_meetings = [];
					$scope.duetoday_meetings = [];
					$scope.upcoming_meetings = [];
					
					angular.forEach(meetings_data, function(meeting)
					{
						switch(meeting.meeting_group_type){
							case "overdue": 	$scope.overdue_meetings.push(meeting);	break;
							case "duetoday": 	$scope.duetoday_meetings.push(meeting);	break;
							case "upcoming": 	$scope.upcoming_meetings.push(meeting);	break;
						}
					});
					
					$scope.load_all_meetings = 1;
				}
		});
	}
	$scope.get_meeting = function($event_id){
		var final_data = {meeting_id: $event_id, action: 'get_meeting', csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
			method  : 'POST',
			url     : 'Dashboard/ajax_get_meeting',
			data    : $httpParamSerializerJQLike(final_data),
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
				if(response.error != undefined && response.error == 0){
					var meeting = response.meeting;
						
					$scope.view_meeting_id = meeting.meeting_id;
					$scope.view_meeting_title = meeting.meeting_title;
					$scope.view_meeting_when_from_date = meeting.when_from_date;
					$scope.view_meeting_when_to_date = meeting.when_to_date;
					$scope.view_meeting_meeting_location = meeting.meeting_location;	
					
					$('#view_meeting').modal('show');
				}
		});
	}
	
	$scope.update_meeting_from_calendar = function($event_id){
		window.location.href = 'Dashboard/encrypt_id/' + $event_id;
	};
	
	
/******************************************************************************************************************************************
***
***  NOTIFICATION Functions
***
******************************************************************************************************************************************/					
	/* Get user notifications */
	$scope.get_userNotification = function (){
		$scope.delete_data = false;
		var final_data = {csrf_gd : Cookies.get('csrf_gd'), action: "get_notif"};
		
		$scope.file =  $http({
			  method  : 'POST',
			  url     :  'Notification/get_userNotification_by_organ',
			  data    :  $httpParamSerializerJQLike(final_data), 
			  headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(data) {
			if(data.count != undefined){
				angular.forEach(data.user_notification,function(item){
					item.enteredon = Date.parse(item.enteredon);	
				});
				$scope.usernotification = data.user_notification;	
			}
		});
	}	
	/* Delete specific/all notifications */
	$scope.delete_all = function($user_id)
	{
		if($scope.selectedAll == false){
			var final_data = {ids: $scope.list_of_selected, csrf_gd : Cookies.get('csrf_gd'), action: "delete_notif"};
			var url = 'Notification/delete_notification';
		}else{
			var final_data = {csrf_gd : Cookies.get('csrf_gd'), action: "delete_notif"};
			var url = 'Notification/delete_all_notification_by_organ';
		}
		
		$scope.file =  $http({
				method  : 'POST',
				url     :  url,
				data    : $httpParamSerializerJQLike(final_data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response) {
				if(response.error == 0){
					$scope.get_userNotification();
					
					$scope.popup_alert_message('btn-success', "Success", response.message, "");
				}else{
					$scope.popup_alert_message('btn-danger', "Error", response.message, "");
				}
			});
	};
	/* CheckAll Notifications */
	$scope.checkAll = function () {
		if($scope.selectedAll){
			$scope.selectedAll = true;
		}else{
			$scope.selectedAll = false;
			$scope.list_of_selected = [];
		}
		angular.forEach($scope.usernotification, function (item) {
			item.Selected = $scope.selectedAll;
		});
	}; 
	/* Select Specific Notification */
	$scope.onSelectNotification = function($selected,$id){
		if($selected == true){
			$scope.list_of_selected.push($id);	
		}else{
			var index = $scope.list_of_selected.indexOf($id)
			$scope.list_of_selected.splice(index,1);     
		}
	}
	/*************************************************************************************************
		HIDE : SHOW
		LOADING PROCESSING ON CALENDAR
	*************************************************************************************************/
	$scope.loading_processing = function(type)
	{
		if(type=="show"){
			if($("#event_calendar_main #loading_processing").length < 0){
				$("#event_calendar_main #loading_processing").show();
			}else{
				var loading_processing = $("#loading_processing_div").html();
				$("#event_calendar_main .fc-view").append(loading_processing);
				$("#event_calendar_main #loading_processing").show();
			}
			
			angular.element("loading_processing").show();
		}else{
			$("#event_calendar_main #loading_processing").hide();
		}
	}
	/*************************************************************************************************
		Get Current Date by js
	*************************************************************************************************/
	$scope.get_current_date = function(){
		var cur_date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		var final_dates = {
			current_date : cur_date,
			date_formatted : y+"-"+m+"-"+d
		}
		return final_dates;
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
	/*************************************************************************************************
	GetIndex of dropdown using value 
	*************************************************************************************************/
	function getSelectedItem(array, object){
		return array[$filter('GetIndexNumeric')(array,object )]; 
	}
});

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