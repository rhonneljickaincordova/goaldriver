 angular.module('moreApps')
.controller('footerCtrl', function ($scope,$http,$location,$rootScope,$httpParamSerializerJQLike){
	$scope.success_save_feedback = false;	 	
	$scope.current_url = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);	

	//if Dashboard 
	$scope.watch_url = window.location.href.substr(window.location.href.lastIndexOf('#') + 1);
	
	if($scope.current_url == 'sign_in'){
		$scope.show_footer = false;
	}if($scope.current_url != 'sign_in'){
		$scope.show_footer = true;
		$scope.happy_image = 'happy_grey.png';
		$scope.ok_image = 'ok_grey.png';
		$scope.sad_image = 'sad_grey.png';
		$scope.happy_image = false;
		$scope.fine_image = false;
		$scope.sad_image = false;
	}

	$scope.get_url = function($url){
		$scope.get_footer_status($url);
		$scope.full_current_url = $url;
	}

	$scope.get_footer_status = function($url){

		var data = {
			url : window.location.href
		};

		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var final_data = angular.extend(data, csrf_object);

		$scope.file =  $http({
          method  : 'POST',
          url     : $url + 'index.php/Feedback/get_status',
          data    :   $httpParamSerializerJQLike(final_data), //forms user object
          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
        }).success(function(data){
        	if(data == 1)
        	{
     			$scope.happy_image = true;
     		}
     		if(data == 2)
     		{
     			$scope.fine_image = true;
     		}
     		if(data == 3)
     		{
     			$scope.sad_image = true;
     		}
     		/** commented due to change of logic for getting feedback status
         	angular.forEach(data.status,function(data){
         		if(data.status_id == 1){
         			$scope.happy_image = true;
         		}
         		if(data.status_id == 2){
         			$scope.fine_image = true;
         		}
         		if(data.status_id == 3){
         			$scope.sad_image = true;
         		}
         	})
         	**/
         
        });

	}

	$scope.onClick = function($value){
		$scope.feedback = '';
		$scope.success_save_feedback = false;

		if($value == 'happy'){
		 	
		 	$scope.emotion = 1;
		 	$scope.icon = 'happy.png';
			$scope.happy_image = true;
			$scope.fine_image = false;
			$scope.sad_image = false;
		}
		if($value == 'fine'){
				
			$scope.emotion = 2;
			$scope.icon  = 'ok.png';
			$scope.fine_image = true;
			$scope.happy_image = false;
			$scope.sad_image = false;	
		}
		if($value == 'sad'){
			
			$scope.emotion = 3;
			$scope.icon = 'sad.png';
			$scope.fine_image = false;
			$scope.happy_image = false;
			$scope.sad_image = true;
		}
	}

	$scope.onClickCloseModal = function(){
		$scope.happy_image = false;
		$scope.fine_image = false;
		$scope.sad_image = false;
		$scope.get_footer_status($scope.full_current_url);
	}

	$scope.save_feedback = function($action,$url){
		$scope.success_save_feedback = false;

		if($scope.feedback == undefined){
			$scope.feedback = '';
		}
		if($action == 'cancel'){
			$scope.feedback = '';
		}

		var data = {
			feedback : $scope.feedback,
			status_id : $scope.emotion,
			url : window.location.href
		};

		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var final_data = angular.extend(data, csrf_object);



		$scope.file =  $http({
          method  : 'POST',
          url     : $url + 'index.php/Feedback/save_feedback',
          data    :  $httpParamSerializerJQLike(final_data), //forms user object
          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
        }).success(function(data){
         	// console.log(data);
         	if($action == 'save'){
	         	$scope.success_save_feedback = true;	
				$scope.feedback_message = data.message;
				setTimeout(function(){
					$('#modal_feedback').modal('hide');
					window.location.reload();
				}, 1000);
			}
			if($action == 'cancel'){
				$('#modal_feedback').modal('hide');
				window.location.reload();
			}

			$scope.get_footer_status($url);
        });
	}

}); 