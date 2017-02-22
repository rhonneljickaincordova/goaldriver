 angular.module('moreApps')
.controller('milestoneupdateCtrl', function ($scope,$http,$location,$rootScope){
		
		$scope.get_milestone = function(site_url,milestoneId){
			$scope.get_all_user(site_url); 
			$scope.get_milestone_user(site_url,milestoneId);
		};
		
		$scope.get_all_user = function(url){
			$scope.file =  $http({
	          method  : 'GET',
	          url     :  url + '/Schedule/get_all_user',
	          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(data){
			
			 	$scope.user = data.user;
	         });
		};

		$scope.get_milestone_user = function(_url,milestoneId){
		
			var data = angular.toJson({id:milestoneId});
			$scope.file =  $http({
	          method  : 'POST',
	          url     :  _url + '/Schedule/get_milestone',
	          data    :  data, //forms user object
	          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(data){
	
	         	angular.forEach(data.user ,function(__user){
	         			if(__user.user_id == data.milestone[0].owner_id){
	         				$scope.temp_owner  = __user;
	         			}
         		});
	         		
	         	if(data.milestone != undefined)
	         	{
			        $scope._owner =	$scope.temp_owner;
					$scope.name = data.milestone[0].name;	
					$scope.description = data.milestone[0].description;
			       	angular.element('#datetimepicker5').val(new Date(data.milestone[0].dueDate).toLocaleDateString());
		       	}
			  });
		};

		$scope.savemilestone = function(_url,milestoneId){
		
			if(typeof($scope._owner) == 'object'){

				$scope._owner = $scope._owner.user_id;
			}

			var data = angular.toJson({
				id: milestoneId,
				owner : $scope._owner,
				name: $scope.name,
				description: $scope.description,
				date: new Date(angular.element('#datetimepicker5').val()).toLocaleDateString(),
			});
			$scope.file =  $http({
		          method  : 'POST',
		          url     : _url + '/Schedule/save_update_milestone',
		          data    :  data, //forms user object
		          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		         }).success(function(response){
		         	  alert(angular.toJson(response.message));
			  	    	  setTimeout(function(){
			    	       window.location = _url +'/schedule';
			          },100);
					});	
		}
		$scope.onChangeOwner = function(new_owner){
			$scope._owner = new_owner;
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