angular.module('moreApps')

.directive('hideOrgList', function($parse, $document) {
	
    var dir = {
        compile: function($element, attr) {
          var fn = $parse(attr["hideOrgList"]);
          return function(scope, element, attr) {
            element.bind("click", function(event) {
				event.stopPropagation();
			});
            angular.element($document[0].body).bind("click",function(event) {
                if(scope.dropdown_OrgList == true){
					scope.$apply(function() {
						scope.ul_OrgList = false;
						scope.dropdown_OrgList = false;
					});	
				}
				
            });
          };
        }
      };
    return dir;
})

.controller("UserSettings_header", UserSettings_header);

function UserSettings_header($scope, $http, $httpParamSerializerJQLike){
	$scope.dropdown_OrgList = false;
	$scope.error_OrgList = false;
	$scope.ul_OrgList = false;
	$scope.loader_div = false;
	
	var uri_string = angular.element("#uri_string").val();
	if(uri_string == "user-settings/organisations"){
		var in_myplanner = true;
	}else{
		var in_myplanner = false;
	}
	
	var url = base_url+'index.php/user-settings/organisations/ajax_get_organisations';
	
	$scope.show_OrgList = function(){
		$scope.loader_div = true;
		if($scope.dropdown_OrgList == false){
			$scope.dropdown_OrgList = true;
			$scope.listed_organisations = [];
			
			var data = {	
				action : "get_organisations",
				in_myplanner: in_myplanner,
				csrf_gd : Cookies.get('csrf_gd')
			};	
			
			$scope.file =  $http({
		        method  : 'POST',
				data 	: $httpParamSerializerJQLike(data),
				url     : url,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).success(function(response){
				$scope.loader_div = false;
				if(response.count > 0){
					$scope.error_OrgList = false;
					$scope.ul_OrgList = true;
					$scope.listed_organisations = response.data;
				}else{
					$scope.error_OrgList = true;
					$scope.ul_OrgList = false;
					$scope._error_OrgList_message = response.message;
				}
				
			}).error(function (data, status){
				$scope.loader_div = false;
				$scope.error_OrgList = true;
				$scope.ul_OrgList = false;
				$scope._error_OrgList_message = "No Organisation available.";
            });	
		}else{
			$scope.dropdown_OrgList = false;
			$scope.error_OrgList = false;
			$scope.ul_OrgList = false;
		}
	}
	
	
	$scope.changeOrganisation = function(organ_id){
		var uri_string = angular.element("#uri_string").val();
		
		if(uri_string == "user-settings/organisations"){
			var in_myplanner = true;
			var url = 'organisations/ajax_get_organisations';
		}else{
			var in_myplanner = false;
			var url = 'user-settings/organisations/ajax_get_organisations';
		} 
		
		var data = {
			action : "get_organisations",
			organ_id: organ_id,
			callback: uri_string,
			in_myplanner: in_myplanner,
			csrf_gd : Cookies.get('csrf_gd')
		};	
		
		$scope.file =  $http({
		        method  : 'POST',
				data 	: $httpParamSerializerJQLike(data),
		        url     : url,
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				if(response.result == "success"){
					location.href = base_url +"index.php/account";
				}else{
					location.reload();
				}
				
			});
	}
}	







