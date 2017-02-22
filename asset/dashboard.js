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

.controller('dashboardCtrl', function ($scope,$http,$compile,uiCalendarConfig, $timeout,$filter,$httpParamSerializerJQLike){

	$scope.selectedAll = false;
	$scope.list_of_selected = [];
	$scope.delete_data = false;
	$scope.update_dates = false;

	$scope.overdue = [];
    $scope.duetoday = [];
	$scope.upcoming = [];	
	$scope.all_milestone = [];
	
	var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    $scope._multipleUser = {};
    $scope.milestone_tasks = this;

	 $scope.statusArray = [
	    {id: 0, name: '0'},
	    {id: 1, name: '10%'},
	    {id: 2, name: '20%'},
	    {id: 3, name: '30%'},
	    {id: 4, name: '40%'},
	    {id: 5, name: '50%'},
	    {id: 6, name: '60%'},
	    {id: 7, name: '70%'},
	    {id: 8, name: '80%'},
	    {id: 9, name: '90%'},
	    {id: 10, name:'100%'},
	];

	$scope.priorityArray = [
	    {id: 1, name: 'None'},
	    {id: 2, name: 'Low'},
	    {id: 3, name: 'Medium'},
	    {id: 4, name: 'High'},
	];	

	
	$scope.get_userId = function($user_id,$organ_id){
		$scope.get_upcomming_meeting($user_id);
		$scope.get_userNotification($user_id);
		$scope.on_click_view_type('calendar');
	}

	
	$scope.get_all_schedule = function(){
		if($("#event_calendar_main #loading_processing").length < 0){
			$("#event_calendar_main #loading_processing").show();
		}else{
			var loading_processing = $("#loading_processing_div").html();
			$("#event_calendar_main .fc-view").append(loading_processing);
			$("#event_calendar_main #loading_processing").show();
		}
		
		angular.element("loading_processing").show();
		var final_data = {id: '', csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
			method  : 'POST',
			url     : 'Schedule/get_all_schedule',
			data    :   $httpParamSerializerJQLike(final_data), 
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		 }).success(function(data){
			$scope.overdue_counter = 0;
			$scope.duetoday_counter = 0;
			$scope.upcoming_counter = 0;
			var current_date = new Date();

			if(data.meetings != null){
				angular.forEach(data.meetings,function(meeting){
					meeting.type="meeting";
					$scope.upcoming_counter++;
				});
			}
			
			angular.forEach(data.tasks,function(task)
			{
				task.status_name = '';
				task.priority_name = '';
				task.type="task";
				task.task_dueDate = Date.parse(task.task_dueDate);
				task.date_completed = Date.parse(task.date_completed);
				task.entered_on = Date.parse(task.entered_on);

				var temp_duedate = new Date(task.task_dueDate);
				
				if( temp_duedate < current_date)
				{
					$scope.overdue.push(task);
					$scope.overdue_counter++;
				}
				else if( temp_duedate == current_date)
				{
					$scope.duetoday.push(task);
					$scope.duetoday_counter++;
				}
				else if( temp_duedate > current_date)
				{
					$scope.upcoming.push(task);
					$scope.upcoming_counter++;
				}	

				angular.forEach($scope.statusArray,function(status)
				{
					if(status.id == task.status)
					{
						task.status_name = status.name;
					}
				});

				angular.forEach($scope.priorityArray,function(priority)
				{
					if(priority.id == task.priority)
					{
						task.priority_name = priority.name;
					}
				});
			});
			
			angular.forEach(data.milestones,function(milestone){

				milestone.type="milestone";
				milestone.owner_name = '';
				milestone.created_by = '';
				milestone.dueDate = Date.parse(milestone.dueDate);
				milestone.entered_on = Date.parse(milestone.entered_on);
				var temp_duedate = new Date(milestone.dueDate).toString('M-dd-yyyy');
					
				if( temp_duedate < current_date)
				{
					$scope.overdue.push(milestone);
					$scope.overdue_counter++;
				}
				else if( temp_duedate == current_date)
				{
					$scope.duetoday.push(milestone);
					$scope.duetoday_counter++;
				}
				else if( temp_duedate > current_date)
				{
					$scope.upcoming.push(milestone);
					$scope.upcoming_counter++;
				}	

				angular.forEach(data.user,function(data){
					if(milestone.owner_id == data.user_id){
						milestone.owner_name = data.first_name +' '+ data.last_name;
					}
					if(milestone.entered_by  == data.user_id){
						milestone.created_by = data.first_name +' '+ data.last_name;	
					}
				})

				angular.forEach($scope.statusArray,function(status){
					if(status.id == milestone.status)
					{
						milestone.status_name = status.name;
					}

				});
			});
			
			$scope.all_milestone = data.milestones;
			
			angular.forEach(data.events,function(event){
				event.end = Date.parse(event.end);
				$scope.events.push(event);
			});
			
			$scope.plain_tab_tasks = [];
			$scope.plain_tab_tasks = data.tasks;
			$("#event_calendar_main #loading_processing").hide();
		});
	}


	$scope.get_comment = function($task_id){
		var final_data = {id: $task_id, csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
	        method  : 'POST',
	        url     : 'Schedule/get_comment',
	        data    : $httpParamSerializerJQLike(final_data), 
	        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
        }).success(function(data){
         	$scope.task_comments = data.task_progress;
         	$scope.task_counter = $scope.task_comments.length;
		
         	angular.forEach(data.task_progress,function(comment){
				comment.date_post = Date.parse(comment.date_post);
			});
		});
	}

	
	$scope.get_upcomming_meeting = function($user_id){
		var participant = [];
		var final_data = {id: '', csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
			method  : 'POST',
			url     : 'Meeting/get_upcoming_meeting_for_dashboard',
			data    : $httpParamSerializerJQLike(final_data),
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(data){

			if(data != 'null'){

				if(data.length != 0){

					data.counter = 0;

					angular.forEach(data,function(meeting){
						if(meeting.object != undefined){
							meeting.object.participant_name = [];
							meeting.object.createdby = '';
							meeting.object.sender_email ='';
							meeting.object.formatted_when_from_date = new Date(meeting.object.formatted_when_from_date);

							data.counter++;
						
							angular.forEach(meeting.user,function(user){
								if(user.user_id == meeting.object.user_id){
									meeting.object.createdby = user.first_name +' '+ user.last_name;  
									meeting.object.sender_email = user.email;
								}
								
								angular.forEach(meeting.participant,function(participant){
									if(participant == user.user_id){
										meeting.object.participant_name.push(user.first_name +' '+ user.last_name);
									}	
								});
							});
						}
					});
					
					angular.forEach(data,function(meeting){
						meeting.object.type="meeting";
						$scope.upcoming.push(meeting.object);
					});

					$scope.upcoming_meeting = data;	
				}
			}
	    });
	}
	
	
	$scope.get_userNotification = function ($user_id){
		$scope.delete_data = false;
		var final_data = {id: $user_id, csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
	          method  : 'POST',
	          url     :  'Notification/get_userNotification',
	          data    :  $httpParamSerializerJQLike(final_data), 
	          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(data) {
			angular.forEach(data.user_notification,function(item){
				item.enteredon = Date.parse(item.enteredon);	
			});
			$scope.usernotification = data.user_notification;
		});
	}

	
	$scope.get_all_user = function(){
		var final_data = {id: '', csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
			method  : 'POST',
			url     : 'Schedule/get_all_user',
			data    : $httpParamSerializerJQLike(final_data),  
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
        }).success(function(data){
         	$scope.user = data.user;
        });
	}

	
	$scope.get_comment = function($task_id){
		$scope.save_task_comments = false;
		$scope.delete_data_comment = false;
		
		var final_data = {id: $task_id, csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
	        method  : 'POST',
	        url     : 'Schedule/get_comment',
	        data    :  $httpParamSerializerJQLike(final_data),   
	        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
        }).success(function(data){
         	angular.forEach(data.task_progress,function(progress){
         		progress.date_post = Date.parse(progress.date_post); /* david */
			});
         	$scope.task_comments = data.task_progress;
         	$scope.task_counter = $scope.task_comments.length;
        });
	}


	
	$scope.update_meeting_from_calendar = function($event_id){
		window.location.href = 'Dashboard/encrypt_id?' + $event_id;
	};

	
	$scope.save_task_comment = function($task_id){
		var final_data = {
			comment: $scope.task_comment.comment,
			task_id : $task_id,
			csrf_gd : Cookies.get('csrf_gd')		
		};

		$scope.file =  $http({
			method  : 'POST',
			url     : 'Schedule/save_Comment',
			data    :  $httpParamSerializerJQLike(final_data), 
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(data){
			$scope.task_comment.comment = '';
			$scope.save_task_comments = true;	
			$scope.message_save_task_comment = data.message;
			
			setTimeout(function(){
				$scope.get_comment($task_id);
			},500);
		});
	}


	$scope.onSaveShareTask = function($users){
		$scope.shared_to = [];
		angular.forEach($users,function(user){
			$scope.shared_to.push(user.user_id);	
		});

		var final_data = {shared_to :$scope.shared_to, csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
          method  : 'POST',
          url     : 'Dashboard/save_shared_task',
          data    :  $httpParamSerializerJQLike(final_data),  
          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
        }).success(function(data){
			
		});
	}

	
	$scope.update_calendar_event = function($event_id,$event_type){
		if($event_type == 'task'){
			console.log($scope.milestone_id);
			var temp_participant = [];
			var temp_start_date = '';
			var temp_due_date = '';

			if(angular.element('#datetimepicker4').val() == ""){
				due_data = null;
				temp_due_date = null;
			}else{
				var due_data = angular.element('#datetimepicker4').val().split(/\//);
					due_data	= [ due_data[1], due_data[0], due_data[2] ].join('/'); 
					temp_due_date = new Date(due_data);
			}
			
			if(angular.element('#datetimepicker_start_date').val() == ""){	
				var start_date = null;
				var temp_start_date = null;
			}else{
				var start_date = angular.element('#datetimepicker_start_date').val().split(/\//);
					start_date = [ start_date[1], start_date[0], start_date[2] ].join('/'); 
					temp_start_date = new Date(start_date);
			}
			
			angular.forEach($scope.participant,function(user){
				temp_participant.push(user.user_id);	
			});

			if($scope.description_task  == undefined ){
				$scope.description_task  = '';
			}

			if(temp_start_date != null && temp_due_date != null && temp_start_date > temp_due_date){
				$scope.update_dates = true;
				$scope.inline_date_message =  "Start Date is greater than Due Date.";
			}else{
				var final_data = {
					id : $event_id,
					participant : temp_participant,
					owner: $scope.owner.user_id,
					milestone: $scope.milestone_id,
					name: $scope.calendar_event_title,
					description: $scope.description_task,
					start_date : start_date,
					date: due_data,
					priority:$scope.priority,
					status: $scope.status,
					csrf_gd : Cookies.get('csrf_gd')
				};

				$scope.file =  $http({
					method  : 'POST',
					url     : 'Schedule/save_update_Task',
					data    :  $httpParamSerializerJQLike(final_data),  
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
				}).success(function(data){
					$scope.update_inline_status = true;
					$scope.inline_status_message =  data.message;

					setTimeout(function(){
						$('#modal2').modal('hide');
					}, 500);
				});
			}    
		}else if($event_type  == 'milestone'){
			var final_data = {id : $event_id, csrf_gd : Cookies.get('csrf_gd')};
			
			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/save_update_milestone',
				data    :  $httpParamSerializerJQLike(final_data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(response){
	         	$scope.save_milestone= true;	
				$scope.message_save_milestone = response.message;
			});	
		}else if($event_type == 'meeting'){

		}
	}

	
	$scope._onChangeStatus = function($status_id,$task_id){
		$scope.status = $status_id;
	}

	
	$scope.onChangeStatus = function($status_id,$task_id){
		$scope.update_inline_status = false;
			
		if($status_id == '10'){
			$scope.date_completed = new Date();
		}else{
			$scope.date_completed = null;	
		}
	
		var final_data = {
			id : $task_id,
			status: $status_id,
			date_completed : $scope.date_completed,
			csrf_gd : Cookies.get('csrf_gd')
		};
		
		$scope.file =  $http({
			method  : 'POST',
			url     : 'Schedule/update_inline_status',
			data    : $httpParamSerializerJQLike(final_data),  
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if( response.error == 0)
			{
				$scope.update_inline_status = true;
				$scope.inline_status_message =  response.message;
			}
			else
			{
				$scope.update_inline_status = false;
			}
		});
	}

	
	$scope._onChangePriority = function($priority_id,$task_id){
		$scope.priority = $priority_id;
	}

	
	$scope.onChangePriority = function($priority_id,$task_id){
		$scope.update_inline_status = false;

		var final_data = {
			id : $task_id,
			priority : $priority_id,
			csrf_gd : Cookies.get('csrf_gd')
		};

		$scope.file =  $http({
			method  : 'POST',
			url     : 'Schedule/update_inline_priority',
			data    : $httpParamSerializerJQLike(final_data),  
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			if( response.error == 0){
				$scope.update_inline_status = true;
				$scope.inline_status_message =  response.message;
			}
			else
			{
				$scope.update_inline_status = false;
			}
		});
	}

	
	$scope.onSendEmail = function($sender_id,$participant_name,$meeting){
		$scope.sender_name = '';
		$scope.participant_email = '';
		$scope.meeting_title = '';
		$scope.meeting_time_duration = $meeting.object.when_from_date +' to '+ $meeting.object.when_to_date ;

		angular.forEach($meeting.user,function(user_data){
			if($sender_id == user_data.user_id){
				$scope.sender_name = user_data.first_name +' '+ user_data.last_name;
			}
			if($participant_name  == user_data.first_name +' '+ user_data.last_name){
				$scope.participant_email = user_data.email;
			}
		});

		var final_data = {
				participant_name : $participant_name[0],
				participant_email : $scope.participant_email,
				sender_email: $meeting.object.sender_email,
				sender_name : $scope.sender_name,
				meeting_title: $meeting.object.meeting_title,
				meeting_time_duration: 	$scope.meeting_time_duration,
				csrf_gd : Cookies.get('csrf_gd')
			};

		$scope.file =  $http({
			method  : 'POST',
			url     : 'Account/send_mail',
			data    :  $httpParamSerializerJQLike(final_data), 
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response){
			$scope.reminder_sent = true;	
			$scope.reminder_message = response.message;
		});	
	}

	
	$scope.openShareTask = function(){
		$scope.get_all_user();		
	}

	
	$scope.openCancelMeeting = function($meeting_id){
		$scope.meeting_id =  $meeting_id;
		$('#modal1').modal('show');
	}

	
	$scope.onCancelMeeting = function($meeting_id){
		var final_data = {meeting_id:$meeting_id, csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
	          method  : 'POST',
	          url     :  'Meeting/delete_meeting',
	          data    :   $httpParamSerializerJQLike(final_data), 
	          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(data){
			$scope.get_upcomming_meeting('');
			$('#modal1').modal('hide');
		});
	}

	
	$scope.on_click_remove_share = function($user_id){
		/* console.log($user_id); */
	}

	
	$scope.open_task_name_link = function($id,$task_name){
		$scope.task_click = true;
		$scope.milestone_click = false;
		$scope.meeting_click = false;
		$scope.event_type = "task";
		
		$scope.get_comment($id);
		    	
		var final_data = {id : $id, csrf_gd : Cookies.get('csrf_gd')};
				
		$scope.file =  $http({
			  method  : 'POST',
			  url     : 'Schedule/get_task',
			  data    :  $httpParamSerializerJQLike(final_data), 
			  headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(data){
			$scope.user = data.user;
			angular.element('#datetimepicker_start_date').val('');
			angular.element('#datetimepicker4').val('');
		         		
			if(data.task.task_startDate != null || data.task.task_startDate != "0000-00-00 00:00:00"){
				angular.element('#datetimepicker_start_date').val(data.task.task_startDate_format);
			}
			if(data.task.task_dueDate != null || data.task.task_dueDate != "0000-00-00 00:00:00"){
				angular.element('#datetimepicker4').val(data.task.task_dueDate_format);
			}
								
			var temp_owner_id = data.task.owner_id;
			var temp_milestone_id = data.task.milestone_id;
			
			$scope.calendar_event_title = data.task.task_name;
			$scope.event_id =  data.task.task_id;
			$scope.milestone_id =  data.task.milestone_id;
			$scope.task_id = data.task.task_id;
			$scope.status_id = data.task.status;
			$scope.calendar_event_start = Date.parse(data.task.task_dueDate);
			$scope.calendar_event_status = data.task.status;
			$scope.status = data.task.status;
			$scope.priority = data.task.priority;
			$scope.calendar_event_priority = data.task.priority_name;

			angular.forEach($scope.user,function(data){
				if(data.user_id == temp_owner_id )
				{
					$scope.owner = data;
				}
			});

			console.log($scope.all_milestone);	
			angular.forEach($scope.all_milestone,function(data){
				if(data.id == temp_milestone_id){
					$scope.milestone = data;
				}
			});

			$scope.participant = [];
			angular.forEach( data.task.participant_id ,function(temp_participant){
				angular.forEach(data.user,function(temp_user){
					if(temp_user.user_id == temp_participant){
						$scope.participant.push(temp_user);
					}
				});
			});	
			
			/* change calendar event priority */
			$scope.ChangeCalendarEventPriority(data.task.priority);		
			/* change calendar event status */
			$scope.ChangeCalendarEventStatus(data.task.status);
			
			
			$scope.calendar_event_createdby =  data.task.first_name +' '+data.task.last_name;
			$('#modal2').modal('show');
		});
	}

	
	$scope.open_milestone_name_link = function($id,$name){
		$scope.task_click = false;
		$scope.milestone_click = true;
		$scope.meeting_click = false;
		$scope.calendar_event_title = $name;
		$scope.event_type = 'milestone';
		
		if($id != null)
		{	
			var final_data = {id : $id, csrf_gd : Cookies.get('csrf_gd')};
			
			$scope.file =  $http({
				  method  : 'POST',
				  url     : 'Schedule/get_milestone',
				  data    :   $httpParamSerializerJQLike(final_data), 
				  headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){
				data.milestone_task = [];
				
				angular.forEach($scope.plain_tab_tasks,function(task){
					if(data.milestone.id == task.milestone_id){
							data.milestone_task.push(task);
					}
				});
				
				$scope.event_id =  data.milestone.id;			         	
				$scope.calendar_event_start = Date.parse(data.milestone.dueDate);
				$scope.calendar_event_status = data.milestone.status;
				$scope.calendar_event_priority = data.milestone.priority_name;
				
				/* change calendar event priority */
				$scope.ChangeCalendarEventPriority(data.milestone.priority);	
				

				$scope.calendar_event_createdby =  data.milestone.first_name +' '+data.milestone.last_name;
				$('#modal2').modal('show');
				$scope.task_lists = data;
			});
		}
	}
	

	$scope.checkAll = function () {
        if ($scope.selectedAll) {
            $scope.selectedAll = true;
        } else {
            $scope.selectedAll = false;
            $scope.list_of_selected = [];
        }
        angular.forEach($scope.usernotification, function (item) {
            item.Selected = $scope.selectedAll;
        });
    }; 

	
   	$scope.onSelectNotification = function($selected,$id){
		if($selected == true){
   			$scope.list_of_selected.push($id);	
   		}else{
   			var index = $scope.list_of_selected.indexOf($id)
   			$scope.list_of_selected.splice(index,1);     
   		}
	}

	
    $scope.delete_all = function($user_id){

    	if($scope.selectedAll == false)
		{
    		var final_data = {ids: $scope.list_of_selected, csrf_gd : Cookies.get('csrf_gd')};
			
			$scope.file =  $http({
				method  : 'POST',
				url     :  'Notification/delete_notification',
				data    : $httpParamSerializerJQLike(final_data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data) {
				$scope.delete_data = true;
				$scope.delete_message =  data.message;
					
				setTimeout(function(){  
						$scope.delete_data = false;
						$scope.get_userNotification($user_id);
				}, 1000);
			});
		}
		else
		{
			var final_data = {ids : '', csrf_gd : Cookies.get('csrf_gd')};
			
			$scope.file =  $http({
				method  : 'POST',
				url     :  'Notification/delete_all_notification',
				data    :   $httpParamSerializerJQLike(final_data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data) {
				$scope.delete_data = true;
				$scope.delete_message =  data.message;
				$scope.selectedAll = false;
				
				setTimeout(function(){  
					$scope.delete_data = false;
				}, 1000);
			});
		}
	};  


	$scope.on_click_view_type = function($type){
		if($type == 'calendar'){
			$scope.calendar_view = true;
			$scope.list_view = false;
		}else{
			$scope.list_view = true;
			$scope.calendar_view = false;
			/* $scope.get_upcomming_meeting(''); */
		}
	}

	
	$scope.onSelectStatus = function ($status_id,$task_id){
		var final_data = {
				id : $task_id,
				status: $status_id,
				date_completed : $scope.date_completed,
				csrf_gd : Cookies.get('csrf_gd')
			};
		
		$scope.file =  $http({
			method  : 'POST',
			url     : 'Schedule/update_inline_status',
			data    :   $httpParamSerializerJQLike(final_data), 
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response) 
		{
			$scope.get_all_schedule();
		});
	}

	
	$scope.onSelectPriority = function($priority_id,$task_id){
		var final_data = {
				id : $task_id,
				priority : $priority_id,
				csrf_gd : Cookies.get('csrf_gd')
			};

		$scope.file =  $http({
			method  : 'POST',
			url     : 'Schedule/update_inline_priority',
			data    :  $httpParamSerializerJQLike(final_data), 
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(response) 
		{
			$scope.get_all_schedule();
		});
	}


    $scope.events = [{
		title: '',
		start: new Date(),
		end : new Date(),
  	}];

	
    $scope.alertOnEventClick = function( event, jsEvent, view){
		$scope.calendar_event_id = event.id;
		$scope.task_click = false;
		$scope.milestone_click = false;
		$scope.meeting_click = false;

      	if(event.className[0] == 'label-success')
      	{
			$scope.update_inline_status = false;
      		$scope.task_click = true;
      		$scope.event_type = 'task';

	      	if(event.id != null)
	      	{
	      		$scope.get_comment(event.id);
		    	var final_data = {id : event.id, csrf_gd : Cookies.get('csrf_gd')};
				
				$scope.file =  $http({
					method  : 'POST',
					url     : 'Schedule/get_task',
					data    :  $httpParamSerializerJQLike(final_data), 
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
				}).success(function(data){
					$scope.user = data.user;
					angular.element('#datetimepicker_start_date').val('');
					angular.element('#datetimepicker4').val('');
					
					if(data.task.task_startDate  != null && data.task.task_startDate != '0000-00-00 00:00:00' ){
						angular.element('#datetimepicker_start_date').val(data.task.task_startDate_format);
					}
					
		         	if(data.task.task_dueDate  != null && data.task.task_dueDate != '0000-00-00 00:00:00' ){
						angular.element('#datetimepicker4').val(data.task.task_dueDate_format);
					}		
					
					var temp_owner_id = data.task.owner_id;
					var temp_milestone_id = data.task.milestone_id;
					
					$scope.calendar_event_title = data.task.task_name;
					$scope.event_id =  data.task.task_id;
					$scope.task_id = data.task.task_id;
					$scope.milestone_id = data.task.milestone_id;
					$scope.status_id = data.task.status;
					$scope.calendar_event_start = Date.parse(data.task.task_dueDate);
					$scope.calendar_event_status = data.task.status;
					$scope.status = data.task.status;
					$scope.priority = data.task.priority;
					$scope.calendar_event_priority = data.task.priority_name;
					
					angular.forEach($scope.user,function(data){
						if(data.user_id == temp_owner_id )
						{
							$scope.owner = data;
						}
					});

					
					angular.forEach($scope.all_milestone,function(data){
						console.log(temp_milestone_id +"="+data.id);
						if(data.id == temp_milestone_id){
							$scope.milestone = data;
						}
					});
					
					$scope.participant = [];
					angular.forEach( data.task.participant_id ,function(temp_participant){
						angular.forEach(data.user,function(temp_user){
							if(temp_user.user_id == temp_participant){
								$scope.participant.push(temp_user);
							}
						});
					});	
					
					/* change calendar event priority */
					$scope.ChangeCalendarEventPriority(data.task.priority);					
					/* change calendar event status */
					$scope.ChangeCalendarEventStatus(data.task.status);
					
					$scope.calendar_event_createdby =  data.task.first_name +' '+data.task.last_name;
					$('#modal2').modal('show');
				});
			}
		}

      	if(event.className[0] == 'label-warning'){
      		/* milestone */
      		$scope.update_inline_status = false;
      		$scope.milestone_click = true;
      		$scope.event_type = 'milestone';
      		if(event.id != null)
	      	{	
				var final_data = {id : event.id, csrf_gd : Cookies.get('csrf_gd')};
				
				$scope.file =  $http({
			          method  : 'POST',
			          url     : 'Schedule/get_milestone',
			          data    :  $httpParamSerializerJQLike(final_data), 
			          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
				}).success(function(data){
					data.milestone_task = [];
			         	
					angular.forEach($scope.plain_tab_tasks,function(task){
						if(data.milestone.id == task.milestone_id){
							data.milestone_task.push(task);
						}
					});
					
					$scope.event_id =  data.milestone.id;			         	
					$scope.calendar_event_start = Date.parse(data.milestone.dueDate);
					$scope.calendar_event_status = data.milestone.status;
					$scope.calendar_event_priority = data.milestone.priority_name;
					/* change calendar event priority */
					$scope.ChangeCalendarEventPriority(data.milestone.priority);
					
					$scope.calendar_event_createdby =  data.milestone.first_name +' '+data.milestone.last_name;
					$('#modal2').modal('show');
					$scope.task_lists = data;
				});
			}
		}

      	if(event.className[0] == 'label-info'){
      		$scope.update_inline_status = false;
      		$scope.meeting_click = true;
      		$scope.event_type = 'meeting';

      		var participant = [];
			var final_data = {id: '', csrf_gd : Cookies.get('csrf_gd')};
			
			$scope.file =  $http({
				method  : 'POST',
				url     : 'Meeting/get_upcoming_meeting_for_dashboard',
				data    :  $httpParamSerializerJQLike(final_data),
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){	
				$scope.event_id = data[0].object.meeting_id;
				if(data.length > 0){
					data.counter = 0;
					angular.forEach(data,function(meeting){
						meeting.object.participant_name = [];
						meeting.object.createdby = '';
						meeting.object.sender_email ='';
						meeting.object.formatted_when_from_date = new Date(meeting.object.formatted_when_from_date);

						data.counter++;

						angular.forEach(meeting.user,function(user){
							if(user.user_id == meeting.object.user_id){
								meeting.object.createdby = user.first_name +' '+ user.last_name;  
								meeting.object.sender_email = user.email;
							}
							
							angular.forEach(meeting.participant,function(participant){
								if(participant == user.user_id){
									meeting.object.participant_name.push(user.first_name +' '+ user.last_name);
								}	
							});
						});
					});
					
					$scope.upcoming_meeting = data;
				}
		    });
			
			$('#modal2').modal('show');
		}
	};
    
	$scope.ChangeCalendarEventStatus = function(event_status){
		switch(event_status) {
			case '0': 	$scope.calendar_event_status = '0';
				break;
			case '1':	$scope.calendar_event_status = '10';
				break;
			case '2':	$scope.calendar_event_status = '20';
				break;
			case '3':	$scope.calendar_event_status = '30';
				break;
			case '4':	$scope.calendar_event_status = '40';
				break;
			case '5':	$scope.calendar_event_status = '50';
				break;
			case '6':	$scope.calendar_event_status = '60';
				break;
			case '7':	$scope.calendar_event_status = '70';
				break;
			case '8':	$scope.calendar_event_status = '80';
				break;
			case '9':	$scope.calendar_event_status = '90';
				break;
			case '10':	$scope.calendar_event_status = '100';
				break;
		}
	};
	
	$scope.ChangeCalendarEventPriority = function(priority){
		switch(priority) {
			case '1':	$scope.calendar_event_priority = 'None'; 
				break;
			case '2':	$scope.calendar_event_priority = 'Low';
				break;
			case '3':	$scope.calendar_event_priority = 'Medium';
				break;
			case '4':	$scope.calendar_event_priority = 'High';
				break;
		}
	};
	
    $scope.alertOnDrop = function(event, delta, revertFunc, jsEvent, ui, view){
		$scope.dragdrop = ('Event Droped to make dayDelta ' + delta);
	};

	
	$scope.viewRender = function (view,element){
		$scope.get_all_schedule();
	}  
   
   
    /* config object */
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
	        eventClick: $scope.alertOnEventClick,
	        eventDrop: $scope.alertOnDrop,
	        eventResize: $scope.alertOnResize,
	        eventRender: $scope.eventRender,
	        viewRender:$scope.viewRender,
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
	
	
	$( '#dashboard_main_tablist a[href="#calendar_main_tab"]' ).on('show.bs.tab', function (e) {
		$scope.renderCalendar('event_calendar_main');
	});
	
	
	$scope.renderCalendar = function (calendarId) {
		$timeout(function () {
			calendarTag = $('#' + calendarId);
			calendarTag.fullCalendar('render');
		}, 0);
	};
	
	
	$timeout(function () {
        $scope.renderCalendar('calendar');
    }, 1000);
	
	
	$scope.comment_profile_pic = function (profile_pic, user_id){
		if(profile_pic == null || profile_pic == "null"){
			return base_url +"public/images/unknown.png";
		}else{
			return base_url +"uploads/"+user_id+"/"+profile_pic;
		}
	}  
	
	
	$scope.delete_comment = function($id,$task_id){
		var final_data = {id : $id, csrf_gd : Cookies.get('csrf_gd')};
		
		$scope.file =  $http({
			method  : 'POST',
			url     :  'Schedule/delete_comment',
			data    : $httpParamSerializerJQLike(final_data),  
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(data){
			if( data.error == 0)
			{
				$scope.delete_data_comment = true;
				$scope.delete_message =  data.message;

				setTimeout(function()
				{  
					$scope.get_comment($task_id);

				}, 1000);
			}
			else
			{
				$scope.delete_data_comment = false;
			}
		});
	}
	
	
	$scope.save_update_comment = function($comment,$comment_id,$task_id){
		var final_data = {
			comment: $comment,
			task_progress_id : $comment_id,
			csrf_gd : Cookies.get('csrf_gd')
		};

		$scope.file =  $http({
			method  : 'POST',
			url     : 'Schedule/save_update_comment',
			data    :   $httpParamSerializerJQLike(final_data),  
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).success(function(data){	
			$scope.save_task_comments = true;	
			$scope.message_save_task_comment = data.message;
			
			setTimeout(function(){
				$scope.get_comment($task_id);
				$scope.get_all_schedule();
			},500);
		});
	}
	
	
	$scope.tooltip_comment = function(type){
		if(type == "delete"){
			$('.delete_comment_btn').tooltip({placement: "left"}); 	
		}else{
			$('.edit_comment_btn').tooltip({placement: "bottom"}); 
		}
	}
});