var app = angular.module('moreApps');

app.controller('Organisations', Organisations);
function Organisations($scope, $http, $httpParamSerializerJQLike){
	$scope.add_modal_id = "#addOrganisationModal";
	$scope.edit_modal_id = "#editOrganisationModal";
	$scope.delete_modal_id = "#deleteOrganisationModal";
	
	$scope.ajax_add_url = 'organisations/ajax_add_organisation';
	$scope.ajax_edit_url = 'organisations/ajax_edit_organisation';
	$scope.ajax_delete_url = 'organisations/ajax_delete_organisation';
	
	/*add Organisation*/
	$scope.open_addOrganisation = function(){
		$scope.organisation_name = '';
		$($scope.add_modal_id).modal('show');
	
	}
	
	$scope.addOrganisation = function(){
		
		if($scope.organisation_name == '')
		{
			$scope._error_organisation =  "Organisation Name is required";
			$scope.isopen_organisation_name = true;
		}
		else
		{
			$scope._error_organisation =  "";
			$scope.isopen_organisation_name = false;
			
			var data = {
				action : "add_organisation",
				organisation_name: $scope.organisation_name,
				copy_organisation: $scope.copy_organisation // if they wish to copy contents from existing organisation
			};
			
			processOrganisation(data, "add", $scope.add_modal_id, $scope.ajax_add_url);
		}
	}
	
	/* edit Organisation */
	
	angular.element('#editOrganisationModal').on('show.bs.modal', function(e) {
		var current_row = angular.element(e.relatedTarget).parents('tr');
		var row_data = Organisation_list.row( current_row ).data();
		
		$scope.$apply(function(){
			$scope.OrganId = row_data[0];
			$scope.edit_organisation_name = row_data[1];
		});
	});
	
	$scope.updateOrganisation = function(){
		if($scope.edit_organisation_name == ''){
			$scope._error_edit_organisation =  "Organisation Name is required";
			$scope.isopen_edit_organisation_name = true;
		}else{
			$scope._error_edit_organisation =  "";
			$scope.isopen_edit_organisation_name = false;
			
			var data = {	
				action : "edit_organisation",
				organ_id: $scope.OrganId, 
				organisation_name: $scope.edit_organisation_name
			};
			
			processOrganisation(data, "edit", $scope.edit_modal_id, $scope.ajax_edit_url);
		}

		
			
	}
	
	angular.element('#deleteOrganisationModal').on('show.bs.modal', function(e) {
		var current_row = angular.element(e.relatedTarget).parents('tr');
		var row_data = Organisation_list.row( current_row ).data();
		
		$scope.$apply(function(){
			$scope.delete_OrganId = row_data[0];
		});
	});
	
	$scope.deleteOrganisation = function(){
		var data = {	
			action : "delete_organisation",
			organ_id: $scope.delete_OrganId
		};	
		processOrganisation(data, "delete", $scope.delete_modal_id, $scope.ajax_delete_url)		
	}
	
	/*save Organisation*/
	function processOrganisation(data, function_type, modal_id, url){
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var merged_data = angular.extend(data, csrf_object);
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : url,
		        data    :  $httpParamSerializerJQLike(merged_data), //forms user object
				headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				var data = response.data;
				if(response.result == "success"){
					if(modal_id != ""){
						$(modal_id).modal('hide');
					}
					
					
					angular.element("#alert_message").html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+response.message+'</div>');
					
					
					if(function_type == "add"){
						var rowNode = Organisation_list
							.row.add( [ response.organ_id, data[0], data[1], data[2], data[3] ] )
							.draw()
							.node();
						
						angular.element( rowNode ).attr("id","organ_id-"+response.organ_id);
						setTimeout(
					    function() {
					      location.reload();
					    }, 1000);
						
					}else if(function_type == "edit"){
						var current_row = angular.element("#organ_id-"+response.organ_id).parents('tr');
						var cell_org_name = angular.element("#organ_id-"+response.organ_id).find(".org_name");
						var cell_last_viewed = angular.element("#organ_id-"+response.organ_id).find(".updated");	
						
						update_org_name = Organisation_list.cell(cell_org_name).data(data[0]);
						update_lastviewed = Organisation_list.cell(cell_last_viewed).data(data[1]);
						Organisation_list.draw();
					}else if(function_type == "delete"){
						var current_row = angular.element("#organ_id-"+response.organ_id);
						var rowNode = Organisation_list.rows(current_row).remove().draw();
					}
				
				}else{
					angular.element("#alert_message").html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+response.message+'</div>');
				}
				
			});	
		
	}	
}







