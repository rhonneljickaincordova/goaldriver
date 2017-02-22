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
.directive('duedatepicker', function ($http,$rootScope,$httpParamSerializerJQLike) {
    return {
        restrict: 'A',
        require: 'ngModel',
         link: function (scope, element, attrs, ngModelCtrl) {
			element.datetimepicker({
                format: "DD/MM/YYYY",
                useCurrent: false 
            }).on("dp.change", function(e) {
				if((e.oldDate == null && attrs.defaultdate == "-") || (attrs.isclicked == "1" || attrs.isclicked == 1)){
					var date = element.data('date');
					var id =  attrs.id;
					var second  =  date.split(/\//);
					var temp_due_date = [ second[2], second[1], second[0] ].join('-');
					var final_data = {
						id : id,
						date: temp_due_date,
						csrf_gd : Cookies.get('csrf_gd')
					};
				
					$http({ method  : 'POST',
				        url     : 'Schedule/update_inline_duedate',
				       	data    :  $httpParamSerializerJQLike(final_data), 
				        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
					}).success(function(data){
							if(data.error == 1){
								$.alert({
									title: 'Error',
									content: data.message,
									confirmButtonClass: 'btn-danger',
								});
							}else{
								$.alert({
									title: 'Success',
									content: 'Due Date is updated.',
									confirmButtonClass: 'btn-success',
								});
							}
					});
					attrs.isclicked = 1;
				}else{
					attrs.isclicked = 1;
				}
     		});
		}
    };
}).directive('startdatepicker', function ($http,$rootScope,$httpParamSerializerJQLike) {
    return {
        restrict: 'A',
        require: 'ngModel',
         link: function (scope, element, attrs, ngModelCtrl) {
			element.datetimepicker({
                format: "DD/MM/YYYY",
                useCurrent: false
			}).on("dp.change", function(e) {
				if((e.oldDate == null && attrs.defaultdate == "-") || (attrs.isclicked == "1" || attrs.isclicked == 1)){
					var date = element.data('date');
					var id =  attrs.id;
					var second  =  date.split(/\//);
					var temp_due_date = [ second[2], second[1], second[0] ].join('-');
					var final_data = {
						id : id,
						date: temp_due_date,
						csrf_gd : Cookies.get('csrf_gd')
					};
				
					$http({ method  : 'POST',
				        url     : 'Schedule/update_inline_startdate',
				       	data    : $httpParamSerializerJQLike(final_data), 
				        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
					}).success(function(data){
							if(data.error == 1){
								$.alert({
									title: 'Error',
									content: data.message,
									confirmButtonClass: 'btn-danger',
								});
							}else{
								$.alert({
									title: 'Success',
									content: 'Start Date is updated.',
									confirmButtonClass: 'btn-success',
								});
							}
					});
					attrs.isclicked = 1;
				}else{
					attrs.isclicked = 1;
				}
			});
		}
    };
})

.controller('scheduleListCtrl', function ($scope,$http,$location,$rootScope,$filter,$httpParamSerializerJQLike){
	
	 	$scope._multipleUser = {};
	 	$scope._priority = {};
	 	$scope._status = {};
	 	$scope._date = {};
	 	$scope._owner = {};
	 	$scope.milestone_id ='';
	 	$scope._owner_task = {};
	 	$scope._milestone_filter = {};
	 	$scope.milestone_name = "";
	 	$scope.task_comment = {};
	 	$scope.updated_comment = {};
	 	$scope.task_subtask = {};
	 	$scope.milestone_status = {};

	 	$scope.update_inline_status = false;
	 	$scope.update_inline_date_failed = false;
	 	$scope.update_inline_date_success = false;
	 	$scope.delete_data = false;
	 	$scope.milestone_name_input_fields_hide = false	;
	 	$scope.show_comment = false;
	 	$scope.new_milestone_save = false;

	 	$scope.sortType     = ''; /*  set the default sort type */
  		$scope.sortReverse  = false;  /*  set the default sort order */

	 	$scope.kanban_filters = [
	 		{id: 0, name: 'By Milestone'},
		    {id: 1, name: 'By Priority'},
		    {id: 2, name: 'By Percentage'},
	 	];
	
		$scope.kaban_filter = 1;

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

/*getting data with no filters*/
		var unformatted_data  = {id : '' };
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		$scope.final_data = angular.extend(unformatted_data, csrf_object);
		
/* Open milestone */
		$scope.open_milestone = function(){
			$scope._milestone_id = '';
			angular.forEach($scope.user,function(data){
     		
     			if(data.user_id == $scope.user_id){
     				$scope._owner.owner = data;
     			}
     		})	
			$scope.milestone_name = '';
			$scope.description = '';
			angular.element('#datetimepicker5').val('');
			angular.element('#milestone_start_date').val('');
			$scope.isopen_name = false;
			$scope.isopen_duedate = false;
			$scope.isopen_owner = false;
			$scope.milestone_name_input_fields_hide = false	;
			$scope.save_milestone = false;
			$scope.milestone_dates_setup = false;
			$scope.name = '';
			$scope.milestone_status = {id:0 ,name: '0'};
			$scope.get_all_milestone();


			$('#modal2').modal('show');
		};

/* Open */
		$scope.open = function($milestone_id,$milestone_name,$index){
			$scope.accordion_index = $index;
			// $scope.value = true;
			$scope.milestone_id = $milestone_id;
			$scope.milestone_task = $milestone_name;

			angular.forEach($scope.all_milestone,function(data){
     			if(data.id == $scope.milestone_id  ){
					$scope.milestone_task = data;         				
     			}
     		});

     		angular.forEach($scope.user,function(data){
				if(data.user_id == $scope.user_id){
     				$scope._owner_task.owner_task = data;
     			}
     		})	

			$scope.name_task = '';
			$scope.description_task = '';
			$scope._multipleUser.user_task = '';
			$scope._priority.priority_task = 1;
			$scope._status.status_task ='';
			angular.element('#datetimepicker4').val('');
			angular.element('#datetimepicker_start_date').val('');

			$scope.isopen_owner_task = false;
			$scope.isopen_who_else = false;
			$scope.isopen_task_name = false;
			$scope.isopen_due_date = false;
			$scope.isopen_priority = false;
			$scope.show_comment = false;
			$scope.dates_setup = false;
			$scope.get_all_milestone();

			$('#modal1').modal('show');
		};

/* Open task name link */
		$scope.open_task_name_link = function($id,$task_name){
			$scope.comment_field = false;
			$scope.task_comment = '';
			$scope.task_name = $task_name;
			$scope.task_id = $id;
			$('#modal3').modal('show');

			$scope.get_comment($id);
			$scope.get_subtask($id);
		}

/* Close task modal */
		$scope.closertask = function(){
			$scope.milestone_task_id = '';
			$('#modal1').modal('hide');
		}

/* Close modal 3 */
		$scope.close_modal_4 = function(){
			$('#modal3').modal('hide');		
		};

/* Save Milestone */
		$scope.savemilstone = function(){
			$scope.milestone_error = [];
				
			if(angular.element('#datetimepicker5').val() != ''){
				var initial = angular.element('#datetimepicker5').val().split(/\//);	
				$scope.new_date = [ initial[1], initial[0], initial[2] ].join('/');
				var new_date_comparison = new Date($scope.new_date);
			}else{
				$scope.new_date = null;
			}
			
			if(angular.element('#milestone_start_date').val() != ''){
				var second  =  angular.element('#milestone_start_date').val().split(/\//);
				$scope.temp_start_date = [ second[1], second[0], second[2] ].join('/');
				var  temp_start_date_comparison = new Date($scope.temp_start_date);
			}else{
				$scope.temp_start_date = null;
			}

			if( temp_start_date_comparison > new_date_comparison){
				$scope.milestone_dates_setup = true;
				$scope.milestone_error_dates_setup =  "Start Date is greater than Due Date.";
				$scope.milestone_error.push($scope._error_dates_setup);
			}else{
				$scope.milestone_dates_setup = false;
			}
			
			if($scope.name == ""){	
				$scope.isopen_name = true;
				$scope._error_name =  "Milstone Name is required";
				$scope.milestone_error.push($scope._error_name);	
			}else{
				$scope.isopen_name = false;
			}
			
			if($scope._owner.owner  == ""){
				$scope.isopen_owner = true;	
				$scope._error_owner = "Owner is required";
				$scope.milestone_error.push($scope._error_owner);	
			}else{
				$scope.isopen_owner = false;	
			}

			if($scope.milestone_error.length > 0){	
				$scope.get_all_user();
				return;
			}else{
				var final_data = {
						id : $scope._milestone_id,
						owner: $scope._owner.owner.user_id,
						name: $scope.name,
						description: $scope.description,
						date: $scope.new_date,
						start_date : $scope.temp_start_date,
						status : $scope.milestone_status.id,
						csrf_gd : Cookies.get('csrf_gd')
				};

				if( $scope._milestone_id == ""){
					$scope.file =  $http({
				          method  : 'POST',
				          url     : 'Schedule/save_Milestone',
				          data    :  $httpParamSerializerJQLike(final_data),
				          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
					}).success(function(data){
							$scope.save_milestone= true;	
							$scope.message_save_milestone = data.message;

							setTimeout(function(){
								$('#modal2').modal('hide');
								$scope.get_all_schedule();
								$scope.get_all_milestone();

							}, 500);
							$scope.new_milestone_save = true;
					});	
				}else{
					$scope.file =  $http({
				          method  : 'POST',
				          url     : 'Schedule/save_update_milestone',
				          data    :   $httpParamSerializerJQLike(final_data), 
				          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
					}).success(function(response){
							$scope.save_milestone= true;	
							$scope.message_save_milestone = response.message;
							
							setTimeout(function(){
								$('#modal2').modal('hide');
								$scope.get_all_schedule();
								
							}, 500);
					});
				}
			}
		}		

/* Save Task */
		$scope.savetask = function(){
			$scope.user = [];
			$scope.task_error = [];
			
			if(angular.element('#datetimepicker4').val() != ''){
				var initial = angular.element('#datetimepicker4').val().split(/\//);
				$scope.new_date_task = [ initial[1], initial[0], initial[2] ].join('/'); 
				var new_date_task_comparison = new Date($scope.new_date_task);
			}else{
				$scope.new_date_task = null;
			}
			
			if(angular.element('#datetimepicker_start_date').val() != ''){
				var initial_start = angular.element('#datetimepicker_start_date').val().split(/\//);
				$scope.start_date_task = [ initial_start[1], initial_start[0], initial_start[2] ].join('/'); 
				var start_date_task_comaprison = new Date($scope.start_date_task);
			}else{
				$scope.start_date_task = null;
			}

			if(start_date_task_comaprison > new_date_task_comparison){
				$scope.dates_setup = true;
				$scope._error_dates_setup =  "Start Date is greater than Due Date.";
				$scope.task_error.push($scope._error_dates_setup);
			}else{
				$scope.dates_setup = false;
			}
			
			if($scope._owner_task.owner_task == ""){
				$scope.isopen_owner_task = true;
				$scope._error_owner =  "Owner is required";
				$scope.task_error.push($scope._error_owner);
			}else{
				$scope.isopen_owner_task = false;
			}

			if($scope.name_task == ""){
				$scope.isopen_task_name = true;
				$scope._error_task_name =  " Task Name is required";
				$scope.task_error.push($scope._error_task_name);
			}else{
				$scope.isopen_task_name = false;
			}

			if($scope.new_date_task == ""){
				$scope.isopen_due_date = true;
				$scope._error_due_date =  " Due Date is required";
				$scope.task_error.push($scope._error_due_date);
			}else{
				$scope.isopen_due_date = false;
			}

			if($scope._priority.priority_task == ""){
				$scope.isopen_priority = true;
				$scope._error_priority =  "Priority is required";
				$scope.task_error.push($scope._error_priority);
			}else{
				$scope.isopen_priority = false;
			}

			$scope.get_all_user();

			if($scope.task_error.length > 0 ){
				$scope.get_all_user();
				return;
			}else{
				/* var temp_milestone_id = $scope.milestone_task.id; */
				if($scope.milestone_task_id == undefined){
					$scope.milestone_task_id = '';
				}

				angular.forEach($scope._multipleUser.user_task,function(user){
					$scope.user.push(user.user_id);	
				});

				var final_data = {
						id : $scope.milestone_task_id,
						participant :$scope.user,
						owner: $scope._owner_task.owner_task.user_id,
						milestone: $scope.milestone_id,
						name: $scope.name_task,
						description: $scope.description_task,
						start_date : $scope.start_date_task,
						date: $scope.new_date_task,
						priority:$scope._priority.priority_task,
						status: $scope._status.status_task,
						csrf_gd : Cookies.get('csrf_gd')
				};

				if($scope.milestone_task_id != ''){
					$scope.file =  $http({
						method  : 'POST',
						url     : 'Schedule/save_update_Task',
						data    :   $httpParamSerializerJQLike(final_data), 
						headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
					}).success(function(response){
							$scope.save_task= true;	
							$scope.message_save_owner = response.message;
							$scope.milestone_task_id = '';

							setTimeout(function(){
								$('#modal1').modal('hide');
								$scope.get_all_user();
								$scope.get_all_schedule();
								$scope.get_all_milestone();
							
							},500);
			      	  });
				}else{
					$scope.file =  $http({
						method  : 'POST',
						url     : 'Schedule/save_Task',
						data    :   $httpParamSerializerJQLike(final_data), 
						headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
					}).success(function(data){
							$scope.save_task= true;	
							$scope.message_save_owner = data.message;
							$scope.milestone_task_id = '';

							setTimeout(function(){
							$('#modal1').modal('hide');
								$scope.get_all_user();
								$scope.get_all_schedule();
								$scope.get_all_milestone();
							
							}, 500);
					});
				}
			}
		};

/* Save task Comment */
		$scope.save_task_comment = function($task_id){
			if($scope.task_comment.comment ==  undefined){
                  $scope.comment_error_message = 'Comment field is required.'
                  $scope.comment_field = true;
                  return;
            }    
			
			var final_data = {
				comment: $scope.task_comment.comment,
				task_id : $task_id,
				csrf_gd : Cookies.get('csrf_gd')
			};
			
			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/save_Comment',
				data    :   $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){
					$scope.task_comment.comment = '';
					$scope.save_task_comments = true;	
					$scope.message_save_task_comment = data.message;
					
					setTimeout(function(){
						$scope.get_comment($task_id);
						$scope.get_all_schedule();
					},500);
			});
		}

/* Save update Comment */
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

/* Save task Subtasks */
		$scope.save_task_subtask = function($task_id){
			var data = {
				subtask: $scope.task_subtask.task,
				task_id : $task_id,
				csrf_gd : Cookies.get('csrf_gd')				
			};

			$scope.file =  $http({
					method  : 'POST',
					url     : 'Schedule/save_Subtask',
					data    :  $httpParamSerializerJQLike(final_data), 
					headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){
					$scope.task_comment.comment = '';
					$scope.save_task_comments = true;	
					$scope.message_save_task_comment = data.message;
			        setTimeout(function(){
						$scope.get_subtask($task_id);
						$scope.get_all_schedule();
			        },500);
			});
		}

/* Save update Subtasks */
		$scope.save_update_subtask = function($subtask,$subtask_id,$task_id){
			var data = {
				subtask: $subtask,
				subtask_id : $subtask_id,
				csrf_gd : Cookies.get('csrf_gd')				
			};

			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/save_update_subtask',
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){	
					$scope.task_comment.comment = '';
					$scope.save_task_comments = true;	
					$scope.message_save_task_comment = data.message;
					
					setTimeout(function(){
						$scope.get_subtask($task_id);
						$scope.get_all_schedule();
					},500);
			});
		}

/* Get Comment */
		$scope.get_comment = function($task_id){
			$scope.save_task_comments = false;
			$scope.delete_data_comment = false;
			var final_data = {id: $task_id, csrf_gd : Cookies.get('csrf_gd')};
			
			$scope.file =  $http({
		        method  : 'POST',
		        url     : 'Schedule/get_comment',
		        data    :   $httpParamSerializerJQLike(final_data),  
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	        }).success(function(data){
					angular.forEach(data.task_progress,function(progress){
						progress.date_post = new Date(progress.date_post);
					});
					
					$scope.task_comments = data.task_progress;
					$scope.task_counter = $scope.task_comments.length;
			});
		}

/* Get Subtasks */
		$scope.get_subtask = function($task_id){
			$scope.save_task_comments = false;
			$scope.delete_data_comment = false;
			
			var final_data = {id: $task_id, csrf_gd : Cookies.get('csrf_gd')};

			$scope.file =  $http({
		        method  : 'POST',
		        url     : 'Schedule/get_subtask',
		        data    :   $httpParamSerializerJQLike(final_data), 
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	        }).success(function(data){
					angular.forEach(data.task_progress,function(progress){
						progress.date_post = new Date(progress.date_post);
					});
					
					$scope.task_subtask = data.task_subtask;
					$scope.task_subtask_counter = $scope.task_subtask.length;
	        });
		}
		
/* Get all user */
		$scope.get_all_user = function(){
			var final_data = {id: '', csrf_gd : Cookies.get('csrf_gd')};

			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/get_all_user',
				data    :   $httpParamSerializerJQLike(final_data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	        }).success(function(data){
					$scope.user = data.user;
			});
		}

/* Get all Schedule */
		$scope.get_all_schedule = function(){
			var final_data  = {id : '', csrf_gd : Cookies.get('csrf_gd')};

			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/get_all_schedule',
				data    :  $httpParamSerializerJQLike(final_data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){
					data.milestones.counter = 0;
					$scope.plain_tab_tasks = [];
					$scope.plain_tab_tasks_id = data.tasks.length;
				
					data.tasks.counter = 0;
					
					$scope.viewby = 5;
					$scope.totalItems = $scope.plain_tab_tasks.length;
					$scope.currentPage = 1;
					$scope.itemsPerPage = $scope.viewby;
					$scope.maxSize = 5;

					$scope.filter_nones = [];
					$scope.filter_lows = [];
					$scope.filter_mediums = [];
					$scope.filter_highs = [];

					$scope.filter_0 = [];
					$scope.filter_1 = [];
					$scope.filter_2 = [];
					$scope.filter_3 = [];
					$scope.filter_4 = [];
					$scope.filter_5 = [];
					$scope.filter_6 = [];
					$scope.filter_7 = [];
					$scope.filter_8 = [];
					$scope.filter_9 = [];
					$scope.filter_10 = [];

					angular.forEach(data.tasks,function(task){
						switch(task.priority){
							case "1" : $scope.filter_nones.push(task); 		break;
							case "2" : $scope.filter_lows.push(task); 		break;
							case "3" : $scope.filter_mediums.push(task); 	break;
							case "4" : $scope.filter_highs.push(task); 		break;
						}
						
						switch(task.status){
							case "0" : $scope.filter_0.push(task); break;
							case "1" : $scope.filter_1.push(task); break;
							case "2" : $scope.filter_2.push(task); break;
							case "3" : $scope.filter_3.push(task); break;
							case "4" : $scope.filter_4.push(task); break;
							case "5" : $scope.filter_5.push(task); break;
							case "6" : $scope.filter_6.push(task); break;
							case "7" : $scope.filter_7.push(task); break;
							case "8" : $scope.filter_8.push(task); break;
							case "9" : $scope.filter_9.push(task); break;
							case "10": $scope.filter_10.push(task); break;
						}
					});
				
					angular.forEach(data.milestones,function(milestone)
					{
						data.milestones.counter++;
						var timestamp = milestone.dueDate;
						
						if(timestamp != null){
							milestone.dueDate  = new Date(milestone.dueDate_format);
						}else{
							milestone.dueDate  =  'N/A';
						}
						
						if(milestone.bShowOnDash == '0'){
							milestone.bShowOnDash = false;
						}else if(milestone.bShowOnDash == '1'){
							milestone.bShowOnDash = true;
						}

						milestone.array_task = [];	
					
						angular.forEach(data.tasks,function(task){
							task.status_name = '';
							task.priority_name = '';

							if(task.task_startDate == null || task.task_startDate == '0000-00-00 00:00:00'){
								task.task_startDate = '-';
								task.task_startDate_format = '-';
							}else{
								task.task_startDate  = new Date(task.task_startDate);
							}
							
							if(task.task_dueDate == null || task.task_dueDate == '0000-00-00 00:00:00'){
								task.task_dueDate = '-';
								task.task_dueDate_format = '-';
							}else{
								task.task_dueDate  = new Date(task.task_dueDate);
							}
							
							task.date_completed = new Date(task.date_completed);
							
							angular.forEach($scope.statusArray,function(status){
								if(status.id == task.status){
									task.status_name = status.name;
								}
							});

							angular.forEach($scope.priorityArray,function(priority){
								if(priority.id == task.priority){
									task.priority_name = priority.name;
								}
							});

							if(milestone.id == task.milestone_id){
								milestone.array_task.push(task);	
							}

							angular.forEach(data.comments,function(comment){
								if(task.task_id == comment.task_id){
									task.comment_counter = data.comments.length;
								}
							});
						});

						angular.forEach(milestone.array_task,function(task){
							task.temp_owner_object = [];
							task_owner_object = {};
							
							angular.forEach(data.user,function(user){
								if(task.owner_id == user.user_id){
									task.temp_owner_object.push(user);	
								}
							});
						
							task.task_owner_object = task.temp_owner_object[0];
						});
							
						milestone.task_counter = '';
						milestone.task_counter = milestone.array_task.length;
					});
						
					$scope.milestones = data.milestones;

					angular.forEach(data.tasks,function(task){
						task.task_dueDate = $filter('date')(task.task_dueDate, "dd/MM/yyyy");
						task.task_startDate = $filter('date')(task.task_startDate, "dd/MM/yyyy");
						task.task_dueDate_format = $filter('date')(task.task_dueDate_format, "dd/MM/yyyy");
						task.task_startDate_format = $filter('date')(task.task_startDate_format, "dd/MM/yyyy");
					});

					$scope.plain_tab_tasks = data.tasks;
							
					if($scope.milestones.counter > 1){
						$scope.default_accordion = true;
					}else if($scope.milestones.counter == 1){
						$scope.default_accordion = false;	
					}

					$scope.update_inline_status = false;
					$scope.delete_data = false;
					$scope.save_task = false;

					if($scope.accordion_index != undefined){
						var accordion = '#accordion-'+$scope.accordion_index; 	
						
						$(accordion).collapse({
						  toggle: true
						});

					}
			});
		}

/* Get userId */
		$scope.get_userId = function($user_id,$organ_id){
			$scope.user_id = $user_id;
			$scope.get_all_user();
			$scope.get_all_schedule();
			$scope.get_all_milestone();
		}

/* Get all milestone */
		$scope.get_all_milestone = function(){
			var final_data = { id : '', csrf_gd : Cookies.get('csrf_gd') };
			$(".dataTables_processing").show();

			$scope.file =  $http({
		          method  : 'POST',
		          url     : 'Schedule/get_all_milestone',
		          data    : $httpParamSerializerJQLike(final_data), 
		          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){
					$scope.all_milestone = data.milestones;
				
					if($scope.accordion_index != undefined ){
						var accordion = '#accordion-'+$scope.accordion_index; 	
						$(accordion).collapse({
							toggle: true
						});
					}
						
					if($scope.new_milestone_save == true){
						$('#accordion-0').collapse({
						  toggle: true
						});
					}

					$(".dataTables_processing").hide();
			});
		}
		
/* Update task */
		$scope.update_task = function($id,$name){
			$scope.task_id = $id;
			$scope.get_comment($id);
            $scope.comment_field = false;
            $scope.dates_setup = false;
			$scope.show_comment = true;
			
			if($name == 'hide_kanban'){
				$scope.milestone_name_input_fields_hide = true;
			}else if($name == 'hide_plain'){
				$scope.milestone_name_input_fields_hide = true;
			}

			var final_data = { id : $id, csrf_gd : Cookies.get('csrf_gd') }; 

			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/get_task',
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){
					if(data.task[0].task_startDate == null || data.task[0].task_startDate == '0000-00-00 00:00:00'){
						 angular.element('#datetimepicker_start_date').val('');
					}else{
						angular.element('#datetimepicker_start_date').val(data.task[0].task_startDate_format);
					}

					if(data.task[0].task_dueDate == null || data.task[0].task_dueDate == '0000-00-00 00:00:00'){
							angular.element('#datetimepicker4').val('');
					}else{
						angular.element('#datetimepicker4').val(data.task[0].task_dueDate_format);
					}

					$scope.owner =[];
					$scope.newParticipant = [];
						
					angular.forEach( data.task[0].participant_id ,function(participant){
						angular.forEach(data.user,function(_user){
							if(_user.user_id == participant){
								$scope.newParticipant.push(_user);
							}
						});
					});	
					
					angular.forEach(data.user,function(_user){
						if(_user.user_id == data.task[0].owner_id){
							$scope.owner.push(_user);
						}
					
					});

					var temp_milestone_id = data.task[0].milestone_id;

					angular.forEach($scope.all_milestone,function(data){
						if(data.id == temp_milestone_id  ){
							$scope.milestone_task = data;         				
						}
					});	
					
					$scope.milestone_id = data.task[0].milestone_id;
					$scope.milestone_task_id = data.task[0].task_id;
					// $scope.milestone_task = $name;
					$scope._owner_task.owner_task =  $scope.owner[0];
					$scope._multipleUser.user_task = $scope.newParticipant;
					$scope.name_task = data.task[0].task_name;
					$scope.description_task = data.task[0].task_description;
					$scope._priority.priority_task = data.task[0].priority;
					$scope._status.status_task = data.task[0].status;
			});
		}

/* Update Milestone */
		$scope.update_milestone = function(milestoneId,$index){
			$('#modal2').modal('show');
			$scope.accordion_index = $index;
			$scope.save_milestone = false;
			$scope.milestone_dates_setup = false;

			var final_data = {id:milestoneId, csrf_gd : Cookies.get('csrf_gd')};

			$scope.file =  $http({
				method  : 'POST',
				url     :  'Schedule/get_milestone',
				data    : $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	        }).success(function(data){
					if(data.milestone[0].dueDate == null || data.milestone[0].dueDate == '0000-00-00 00:00:00'){
							angular.element('#datetimepicker5').val('');
					}else{
						angular.element('#datetimepicker5').val(data.milestone[0].dueDate_format);
					}

					if(data.milestone[0].startDate == null || data.milestone[0].startDate == '0000-00-00 00:00:00'){
							angular.element('#milestone_start_date').val('');
					}else{
						angular.element('#milestone_start_date').val(data.milestone[0].startDate_format);
					}

					$scope.temp_owner = [];
					
					angular.forEach(data.user ,function(__user){
						if(__user.user_id == data.milestone[0].owner_id){
							$scope.temp_owner.push(__user);
						}
					});
					
					$scope._milestone_id = data.milestone[0].id;
					$scope._owner.owner =	$scope.temp_owner[0];
					$scope.name = data.milestone[0].name;	
					$scope.description = data.milestone[0].description;
					$scope.milestone_status.id = data.milestone[0].status;
			});
		}

/* Open delete task */
		$scope.open_delete_task = function($id){
			$scope.temp_id_task = $id;
		}
		
/* Delete task */
		$scope.delete_task = function($id){
			var final_data = {id:$id, csrf_gd : Cookies.get('csrf_gd')};

			$scope.file =  $http({
				method  : 'POST',
				url     :  'Schedule/delete_task',
				data    : $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){
					if( data.error == 0){
						$scope.delete_data = true;
						$scope.delete_message =  data.message;

						setTimeout(function()
						{  
							$scope.get_all_schedule();
							$('#modal5').modal('hide');
						}, 1000);
					}else{
						$scope.delete_data = false;
					}
			});
		}

/* Open delete milestone */
		$scope.open_delete_milestone = function($id){
			$scope.temp_id = $id;
			$('#modal4').modal('show');
		}
		
/* Delete milestone */
		$scope.delete_milestone = function($id){
			var final_data = {id:$id, csrf_gd : Cookies.get('csrf_gd')};
		
			$scope.file =  $http({
				method  : 'POST',
				url     :  'Schedule/delete_milestone',
				data    : $httpParamSerializerJQLike(final_data), 
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(data){
					if( data.error == 0){
						$scope.delete_data = true;
						$scope.delete_message =  data.message;

						setTimeout(function()
						{  
							$scope.get_all_schedule();
							$('#modal4').modal('hide');
						}, 1000);

					}else{
						$scope.delete_data = false;
					}
			});
		}

/* Delete Comment */
		$scope.delete_comment = function($id,$task_id){
			var final_data = {id:$id,csrf_gd : Cookies.get('csrf_gd')};

			$scope.file =  $http({
				method  : 'POST',
				url     :  'Schedule/delete_comment',
				data    : $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(data){
					if( data.error == 0){
						$scope.delete_data_comment = true;
						$scope.delete_message =  data.message;

						setTimeout(function()
						{  
							$scope.get_comment($task_id);

						}, 1000);
					}else{
						$scope.delete_data_comment = false;
					}
			});
		}
		
/* Delete Subtask */
		$scope.delete_subtask =  function($id,$task_id){
			var final_data = {id:$id, csrf_gd : Cookies.get('csrf_gd')};

			$scope.file =  $http({
				method  : 'POST',
				url     :  'Schedule/delete_subtask',
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(data){
					if( data.error == 0){
						$scope.delete_data_comment = true;
						$scope.delete_message =  data.message;

						setTimeout(function()
						{  
							$scope.get_subtask($task_id);

						}, 1000);
					}else{
						$scope.delete_data_comment = false;
					}
			});
		}

/* Undo task */
		$scope.undo_task = function($task_id){
			var final_data = {
				id : $task_id,
				status: 0,
				date_completed : null,
				csrf_gd : Cookies.get('csrf_gd')
			};

			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/update_inline_status',
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		    }).success(function(response){
					if( response.error == 0){
						$scope.update_inline_status = true;
						$scope.inline_status_message =  response.message;
							
						setTimeout(function()
						{  
							$scope.get_all_schedule();
						}, 1000);
					}else{
						$scope.update_inline_status = false;
					}
			});
		}
		
/* Change Status Kanban */
		$scope.onChangeStatus_Kanban = function($status_id,$task_id){
			$scope.kaban_filter = $status_id;
		}

/* Change Status */		
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
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
					if( response.error == 0){
						$scope.update_inline_status = true;
						$scope.inline_status_message =  response.message;
							
						setTimeout(function()
						{  
							$scope.get_all_schedule();
							$scope.get_all_milestone();
						}, 1000);
					}else{
						$scope.update_inline_status = false;
					}
			});
		}

/* Change Priority  */
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
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
					if( response.error == 0){
						$scope.update_inline_status = true;
						$scope.inline_status_message =  response.message;
						
						setTimeout(function()
						{  
							$scope.get_all_schedule();
							$scope.get_all_milestone();
						}, 1000);

					}else{
						$scope.update_inline_status = false;
					}
			});
		}

/* Change Owner */
		$scope.onChangeOwner = function($owner_id,$task_id){
			$scope.update_inline_status = false;

			var final_data = {
				id : $task_id,
				owner_id : $owner_id,
				csrf_gd : Cookies.get('csrf_gd')
			};

			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/update_inline_owner',
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
					if( response.error == 0){
						$scope.update_inline_status = true;
						$scope.inline_status_message =  response.message;
						
						setTimeout(function()
						{  
							$scope.get_all_schedule();
							$scope.get_all_milestone();
						}, 1000);

					}else{
						$scope.update_inline_status = false;
					}
			});
		}

/* Change Milestone  */
		$scope.onChangeMilestone = function($data){
			$scope.milestone_id = $data.id;
		}
		
/* Change plain tab duedate */
		$scope.onChangePlainTabDueDate = function($date){
			 /* console.log($date); */
		}

/* Milestone show */
		$scope.onMilestoneShow =function($value,$milestone_id){
			var temp_value = '';	
			
			if($value  == true){
				temp_value = 0;
			}else{
				temp_value = 1;
			}

			var final_data = {
				value : temp_value,
				milestone_id : $milestone_id,
				csrf_gd : Cookies.get('csrf_gd')				
			};

			$scope.file =  $http({
				method  : 'POST',
				url     : 'Schedule/save_update_milestone_view',
				data    :  $httpParamSerializerJQLike(final_data),  
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(data){
					var message = "";
					var title = "";
					if(data.error == 0){
						title = "Success";
						
						if(temp_value == 0){
							message = "This milestone has been removed from your dashboard";
						}else{
							message = "This milestone has been added to your dashboard";
						}
						
					}else{
						title = "Error";
						message = "Failed to update.";
					}
					
					$.alert({
						title: title,
						content: message,
						confirmButtonClass: 'btn-success',
					});
					
					$scope.get_all_schedule();
			});
		}

/* Delete Item */
		$scope.deleteItem = function(item, $event){
      		$event.stopPropagation();
      		$event.preventDefault();
		}

/* value */
		$scope.value = true;
		$scope.orig = angular.copy($scope.value);

/* Click accordion */
		$scope.on_click_accordion = function(value,index){
			$scope.accordion_index = index;
			$scope.value = value;
			$scope.value = true;
		}

/* Close */
		$scope.close = function(){
			$scope.update_inline_status = false;
		}

/* Today */
		$scope.today = function() {
			$scope.dt = new Date();
		}

		
		$scope.today();

/* Date options */
	    $scope.dateOptions = {
		    dateDisabled: false,
		    formatYear: 'yy',
		    maxDate: new Date(2020, 5, 22),
		    minDate: new Date(),
		    startingDay: 1
	    };

		
		$scope.open1 = function() {
			$scope.popup1.opened = true;
		};

		
		$scope.popup1 = {
			opened: false
		};
		
/* Tootip Comment */
		$scope.tooltip_comment = function(type){
			if(type == "delete"){
				$('.delete_comment_btn').tooltip({placement: "left"}); 	
			}else{
				$('.edit_comment_btn').tooltip({placement: "bottom"}); 
			}
		}
		
/* Comment profile pic */
		$scope.comment_profile_pic = function (profile_pic, user_id){
			if(profile_pic == null || profile_pic == "null"){
				return base_url +"public/images/unknown.png";
			}else{
				return base_url +"uploads/"+user_id+"/"+profile_pic;
			}
		}
		
}); 