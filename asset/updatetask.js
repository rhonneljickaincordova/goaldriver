 angular.module('moreApps')
.controller('taskupdateCtrl', function ($scope,$http,$location,$rootScope){
		
		$scope._owner_task = {};
	
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
		    {id: 10, name:'100%'}
		];

		$scope.priorityArray = [
		    {id: 1, name: 'None'},
		    {id: 2, name: 'Low'},
		    {id: 3, name: 'Meduim'},
		    {id: 4, name: 'High'}
		];

		$scope.get_task = function(site_url,task_id){
	
			$rootScope.site_url = site_url;
			$rootScope.task_id = task_id;	
			$scope.get_task_user(site_url,task_id);
		};

		$scope.$watch('site_url', function() {
		      	$scope.get_all_user($rootScope.site_url);
		      	
		});

		$scope.get_all_user = function(site_url){

			var data = angular.toJson({id:''});
			$scope.file =  $http({
	          method  : 'GET',
	          url     :  $rootScope.site_url + '/Schedule/get_all_user',
	          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(data){
	         	$rootScope.user = data.user;
	         	$scope.participant = data.user;
	         });
		};

		$scope.get_task_user = function(_url,task_id){
			$scope.id = angular.toJson({ id : task_id});
			$scope.file =  $http({
	          method  : 'POST',
	          url     :  _url + '/Schedule/get_task',
	          data    :$scope.id, //forms user object
	          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(data){

	         	if(data.task != undefined)
	         	{
					$scope.newParticipant = [];
	         		angular.forEach( data.task[0].participant_id ,function(participant){
	       				angular.forEach(data.user,function(data){
	       					if(data.user_id == participant){
								$scope.newParticipant.push(data);
							}
						});
	           		});
	           		$scope.milestone_id = data.task[0].id;
		         	$scope.milestone_task = data.task[0].name;
		         	$scope._user_id = data.task[0].user_id;
		         	$scope._multipleUser =  $scope.newParticipant;
		         	$scope.name_task =  data.task[0].task_name ;
		         	$scope.description_task = data.task[0].task_description;
		         
		         	$scope._owner_task =  data.task[0].owner_id;
		         	$scope.dt_task =  new Date(data.task[0].dueDate);
		         	$scope._priority = data.task[0].priority;
		         	$scope._status = data.task[0].status;
	         	}
			   });
		};

		$scope.savetask = function(_url,taskId){
		
			var data = angular.toJson({
				id : taskId,
				participant : $scope._participant_id,
				owner : $scope._owner_task.owner_task,
				milestone : $scope.milestone_id,
				name : $scope.name_task,
				description : $scope.description_task,
				date : new Date($scope.dt_task).toLocaleDateString(),
				priority : $scope._priority,
				status : $scope._status,
			});

			$scope.file =  $http({
	          method  : 'POST',
	          url     : _url + '/Schedule/save_update_Task',
	          data    :  data, //forms user object
	          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         })
	          .success(function(response) 
	          {
	          	alert(angular.toJson(response.message));
		        setTimeout(function(){
		            window.location = _url +'/schedule';
		          },100);
	      	  });
		}
	
		$scope.onChangeParticipant = function(new_participants){
			$scope._participant_id = []
			angular.forEach(new_participants,function(participant){
				$scope._participant_id.push(participant.user_id);
			});
		}

		$scope.onChangeOwner = function(new_owner){
			$scope._owner_task = new_owner;
		}
		$scope.onChangeStatus = function(new_status){
			$scope._status = new_status;
		}
		$scope.onChangePriority = function(new_priority){
			$scope._priority = new_priority;
		}
		
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
}); 