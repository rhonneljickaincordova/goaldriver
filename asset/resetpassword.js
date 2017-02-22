 angular.module('moreApps')
.controller('resetPasswordCtrl', function ($scope,$http,$location,$timeout,$httpParamSerializerJQLike){
		$scope.showError = false;
		$scope.showSuccess = false;
		$scope.repeatpassword = '';
		$scope.password = '';


		$scope.onGetUserID = function($password_token_id,$url){
 			
 			var timer;
	 		$scope.counter = 0;
			var updateCounter = function() {
	            $scope.counter++;
	            timer = $timeout(updateCounter, 1000);
	        	
	        	if($scope.counter == 120){

			       	var data = {
						password_token_id : $password_token_id,
						
					};

					var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
					var final_data = angular.extend(data, csrf_object);

					$scope.file =  $http({
			          method  : 'POST',
			          url     : $url+'index.php/account/close_token',
			          data    : $httpParamSerializerJQLike(final_data), //forms user object
			          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			         }).success(function(data){
			         	// console.log(data);
			        
			      	  });
			        window.location = $url; 
	        	}
	        };
	        updateCounter();
    	}

        
		
		$scope.onClickSave = function($user_id, $token_id, $url){
		
			if($scope.password == '' || $scope.repeatpassword == ''){
				$scope.showError = true;
				$scope.errorMessage = 'Required fields.';
				setTimeout(function(){
					 $scope.showError = false;
				}, 500);
				return;
			}
			if($scope.password != $scope.repeatpassword){
				$scope.showError = true;
				$scope.errorMessage = 'Password are not equal.';
				setTimeout(function(){ 
					$scope.showError = false; 
				}, 500);
				return;
			}
			var data = {
				id : $user_id,
				password : $scope.password,
				token_id : $token_id,
				csrf_gd : Cookies.get('csrf_gd')
			};

			$scope.file =  $http({
	          method  : 'POST',
	          url     : $url+'index.php/account/save_new_password',
	          data    :  $httpParamSerializerJQLike(data), //forms user object
	          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
	         }).success(function(data){
	         	// console.log(data);
	          	if(data.error == 0){
	          		$scope.repeatpassword = '';
					$scope.password = '';
		          	$scope.showSuccess = true;
		          	$scope.successMessage = data.message;
	      	  	}else{
	      	  		$scope.showError = true;
					$scope.errorMessage = data.message;
	      	  	}

	      	  });
		}
	
		

}); 