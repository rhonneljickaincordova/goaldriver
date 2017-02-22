var app = angular.module('moreApps',['ui.bootstrap','ui.select','ngSanitize','ngAnimate','ui.calendar','angularjs-dropdown-multiselect']);
app.filter('PropsFilter', PropsFilter);
app.filter('GetIndex', GetIndex);
app.filter('myTrim', myTrim);
app.controller('headerCtrl', headerCtrl);

function headerCtrl($scope,$http,$location,$rootScope,$httpParamSerializerJQLike){
	$scope.count_usernotification = '';
	$scope.usernotification = '';
	$scope.selectedAll = false;
	$scope.list_of_selected = [];
	$scope.delete_data = false;

	$scope.get_userId = function ($user_id,url){
		$scope.global_user_id = $user_id;
		$scope.global_url = url;
		$scope.getData($user_id,url);
	};

	$scope.onclick = function(choice){
		window.location =  choice.link_value;
	};



	/* update notification */
	$scope.updateStatus = function(){
		var final_data = {user_id: '', csrf_gd : Cookies.get('csrf_gd'), action: "update_notif"};
		var page_name = $("#page_name_for_header").val();
		var url = base_url +"index.php/Notification/update_userNotification";
		
		$scope.file =  $http({
			  method  : 'POST',
			  url     : url,
			  data    :   $httpParamSerializerJQLike(final_data), //forms user object
			  headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			 })
			  .success(function(data) 
			  {
				$scope.getData($scope.global_user_id,$scope.global_url);
			  });	
	}

	
	/* get Notification*/
	$scope.getData = function ($user_id,url){
		
		var final_data  = {"action": "get_notif", csrf_gd : Cookies.get('csrf_gd')  };
		
		$scope.file =  $http({
			  method  : 'POST',
			  url     :  url + '/Notification/get_userNotification',
			  data    :  $httpParamSerializerJQLike(final_data), //forms user object
			  headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			 })
			  .success(function(data) {
			
				angular.forEach(data.user_notification,function(item){
					item.enteredon = new Date(item.enteredon);	

					if(item.status == 0 ){
						item.statusdesc = 'Unseen';
					}else{
						item.statusdesc = 'Seen';
					}
				});	
				$scope.usernotification = data.user_notification;
				if(data.count != undefined){
					$scope.count_usernotification = data.count[0].count;
				}
			  });
	};

	
	/* checkAll */
	$scope.checkAll = function () 
	{
		if ($scope.selectedAll) 
		{
            $scope.selectedAll = true;
        } 
		else 
		{
            $scope.selectedAll = false;
            $scope.list_of_selected = [];
        }
		
        angular.forEach($scope.usernotification, function (item) {
            item.Selected = $scope.selectedAll;
        });
    }; 

	
	/* on select notification */
   	$scope.onSelectNotification = function($selected,$id)
	{
		if($selected == true)
		{
   			$scope.list_of_selected.push($id);	
   		}
   		else
		{
   			var index = $scope.list_of_selected.indexOf($id)
   			$scope.list_of_selected.splice(index,1);     
   		}
	}

	
	
	/* delete notification */
    $scope.delete_all = function()
	{
		if($scope.selectedAll == false)
		{
    		var final_data = {ids: $scope.list_of_selected, csrf_gd : Cookies.get('csrf_gd'), action: "delete_notif"};
			
			$scope.file =  $http({
				method  : 'POST',
				url     :  'Notification/delete_notification',
				data    :  $httpParamSerializerJQLike(final_data),  //forms user object
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			})
			.success(function(data) {
					$scope.delete_data = true;
					$scope.delete_message =  data.message;

					setTimeout(function()
					{  
						$scope.delete_data = false;

					}, 1000);
			});
		}
    	else
		{
			var data = {csrf_gd : Cookies.get('csrf_gd'), action: "delete_notif"};
			
			$scope.file =  $http({
				method  : 'POST',
				url     :  'Notification/delete_all_notification',
				data    : $httpParamSerializerJQLike(final_data),	
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			})
			  .success(function(data) {
					$scope.delete_data = true;
					$scope.delete_message =  data.message;
					$scope.selectedAll = false;
					
					setTimeout(function()
					{  
						$scope.delete_data = false;

					}, 1000);
			});
		}
	};  
} 



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
function myTrim() {
    return function(string) {
        if (!angular.isString(string)) {
            return string;
        }
        return string.replace(/[\s]/g, '');
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
       /* Let the output be the input untouched */
      out = items;
    }

    return out;
  };
}