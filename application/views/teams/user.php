<?php $this->load->view('includes/header'); ?>

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />
<script type="text/javascript">
    $(document).ready(function(){
        var rights = $('.check_rights').val();

        if(rights == "disabled")
        {
        	$('button[data-toggle=modal]').remove();
            $('a[data-toggle=modal]').remove();
            $('.actions a').remove();
            $('.action_header').remove();
            $('a.user_actions').remove();
            $('td > a').removeAttr('href');
        }
    
    });
</script>

<div ng-controller='Organisation_users' class='organisation_users_container bg-white-wrapper'>

	<div class="btn-group btn-group-sm" role="group" aria-label="">
		<a href="<?php echo site_url('teams/user'); ?>" class="btn btn-default active"><i class="fa fa-male"></i> Users</a>&nbsp;&nbsp;
		<a href="<?php echo site_url('teams'); ?>" class="btn btn-default"><i class="fa fa-users"></i> Teams</a>
	</div>
	<button type="button" class="btn btn-primary btn-sm" ng-click="open_addOrganisationUser()" data-toggle="modal"><i class="fa fa-plus"></i> Add User</button>
	
	<br><br>	
	<!--response message here-->
	<div id="alert_message" class="col-md-12" ></div>
	<!--response message end-->
	<table class="table table-hover dataTable no-footer" id="organisation_users_table" role="grid">
		<thead>
			<th>User Id</th>
			<th>Name</th>
			<th>Company</th>
			<th>Telephone</th>
			<th>Job</th>
			<th style="width:180px;" class="action_header">Action</th>
		</thead>
		<?php 
		if(!empty($users)):
			foreach($users as $user):?>
			<tr id="user_id-<?php echo $user->user_id; ?>">
				<td>
					<?php echo $user->user_id; ?>
				</td>
				<td>
					<a href="<?php echo base_url("index.php/teams/edit_user/".encrypt($user->user_id)."/".encrypt($user->organ_id)) ?>"><?php echo $user->first_name.' '.$user->last_name; ?></a>
				</td>
				<td>
					<?php echo $user->company ? $user->company : '-'; ?>
				</td>
				<td>
					<?php echo $user->tel_number ? $user->tel_number : '-'; ?>
				</td>
				<td>
					<?php echo $user->job_title ? $user->job_title : '-'; ?>
				</td>
				<td style="text-align:right" class="actions">
					<a class="user_actions" href='<?php echo base_url("index.php/teams/edit_user/".encrypt($user->user_id)."/".encrypt($user->organ_id)) ?>' style='margin-right:10px;text-decoration: none;'>
						<i class='fa fa-pencil-square-o' data-toggle='tooltip' data-placement='bottom' title='Edit'></i>
					</a>

					<?php if(organ_info("owner_id", $this->session->userdata('organ_id')) != $user->user_id) :?>
					<a class="user_actions delete_org" href='#' style='margin-right:10px;text-decoration: none;'>
						<i class='fa fa-trash-o' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>
					</a>
					<?php endif;?>

					<?php if(organ_info("owner_id", $this->session->userdata('organ_id')) != $user->user_id) :?>
						<a href='<?php echo base_url("index.php/permission/set-permission/".encrypt($user->user_id)."/".encrypt($user->organ_id)) ?>' class='set_permission' style='text-decoration: none;'>
							<i class='fa fa-cogs' data-toggle='tooltip' data-placement='bottom' title='Set Permission'></i>
						</a>
					<?php endif;?>

				</td>
			</tr>
			<?php 
			endforeach; 
		else: 
		?>
		<tr><td colspan="5">You have no users at the moment, click the add user button to get started</td></tr>
		<?php endif; ?>
	</table>

	<!-- Modal -->
	<div class="modal fade" id="newuser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Add User</h4>
				</div>
				<div class="modal-body">
					<div id="response-msg"></div>
					<div class="form-group row">
						<div class="col-sm-6 form-group">
							<label for="first-name" class="control-label">First Name</label>
							<input type="text" ng-model="first_name" name="first_name"  class="form-control" required>
						</div>
						<div class="col-sm-6 form-group">
							<label for="last-name" class="control-label">Last Name</label>
							<input type="text" ng-model="last_name" name="last_name"  class="form-control" required>
						</div>
					</div>
					
					<div class="form-group">
						<label for="email" class="control-label">Email Address</label>
						<input type="text" ng-model="user_email" name="user_email"  class="form-control" required>
					</div>
					<p class="alert alert-danger" ng-show="isopen_user_email">{{_error_email}}</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary btn-sm" ng-click="addOrganisationUser()">Add</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
			window.OrganisationUsers_list = $("#organisation_users_table").DataTable({
				"aoColumnDefs":[
					{
						"aTargets": [ 0 ],  
						className: "user_id hidden"
					},
					{
						"aTargets": [ 1 ],  
						className: "name",
						// "mRender": function(data, type, row){
						// 	var text_re = '<a href="'+base_url+'index.php/teams/edit_user/'+ row[0]+'">'+data+'</a>';
						// 	return text_re;
						// }
					},{ 
						"targets": [ 2 ], 
						className: "company"  
					},{ 
						"targets": [ 3 ], 
						className: "tel_num"
					},{ 
						"targets": [ 4 ], 
						className: "job_title"
					},{ 
						"targets": [ 5 ], 
						className: "edit_delete", 
						"sortable":false,
						"searchable":false,
						// "mRender": function(data, type, row){
						// 	text_re = "<a href='edit_user/"+row[0]+"' style='margin-right:10px;text-decoration: none;'>";
						// 	text_re += "<i class='fa fa-pencil-square-o' data-toggle='tooltip' data-placement='bottom' title='Edit'></i>";
						// 	text_re += "</a>";
						// 	text_re += "<a href='#' class='delete_org' style='text-decoration: none;'>";
						// 	text_re += "<i class='fa fa-trash-o' data-toggle='tooltip' data-placement='bottom' title='Delete'></i>";
						// 	text_re += "</a>";			
							
						// 	return text_re;
						// }
					}
				],
			"paginate" :false,
			"dom" : "t",
			"order": [[ 1, "asc" ]]
		});
	});
 
 
var app = angular.module('moreApps');

app.controller('Organisation_users', Organisation_users);
function Organisation_users($scope, $http, $httpParamSerializerJQLike){
	$scope.add_modal_id = "#newuser";
	
	$scope.ajax_add_url = 'ajax_add_user';
	$scope.ajax_delete_url = 'delete_user';
	
	/*add Organisation*/
	$scope.open_addOrganisationUser = function(){
		$scope.first_name = '';
		$scope.last_name = '';
		$scope.user_email = '';
		$($scope.add_modal_id).modal('show');
	
	}
	
	$scope.addOrganisationUser = function(){
		
		$scope._error_first_name =  "";
		$scope._error_last_name =  "";
		$scope._error_email =  "";
		$scope.isopen_user_first_name = false;
		$scope.isopen_user_last_name = false;
		$scope.isopen_user_email = false;
		
		var data = {
			action : "add_user",
			first_name: $scope.first_name,
			last_name: $scope.last_name,
			user_email: $scope.user_email
		};
		
		processOrganisationUser(data, "add", $scope.add_modal_id, $scope.ajax_add_url);
		
		
			
	}
	
	
	/*delete User*/
	$("body").on("click", ".delete_org", function(){
		var isdelete = confirm('Are you sure you want to delete?');
		var rowNode = angular.element(this).parents("tr");
		var data = OrganisationUsers_list.row(rowNode).data();
		
		if(isdelete){
			var data = {	
				action : "delete_user",
				user_id: data[0]
			};	
			processOrganisationUser(data, "delete", "", $scope.ajax_delete_url)	
		}
	});
	
	/*save Organisation User*/
	function processOrganisationUser(initial_data, function_type, modal_id, url){
		var csrf_object = {csrf_gd : Cookies.get('csrf_gd')};
		var data = angular.extend(initial_data, csrf_object);
		
		$scope.file =  $http({
		        method  : 'POST',
		        url     : url,
		        data    :  $httpParamSerializerJQLike(data), 
		        headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
			}).success(function(response){
				if(response.result == "success"){
					var data = response.data;
					if(modal_id != ""){
						$(modal_id).modal('hide');
					}
					
					
					angular.element("#alert_message").html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+response.message+'</div>');
					
					
					if(function_type == "add"){
						var rowNode = OrganisationUsers_list
							.row.add( [ data.user_id, data.first_name +" "+ data.last_name, data.company, data.tel_num, data.job_title, "" ] )
							.draw()
							.node();
						
						angular.element( rowNode ).attr("id","user_id-"+response.user_id);

						window.location.href = base_url+'index.php/permission/set-permission/'+data.encrypted_userid+'/'+data.encrypted_organid; //added by ted saavedra
					
					}else if(function_type == "delete"){
						var current_row = angular.element("#user_id-"+response.user_id);
						var rowNode = OrganisationUsers_list.rows(current_row).remove().draw();
					}
					
				}else{
					if(response.type == 2){
						$scope._error_email =  response.message;
						$scope.isopen_user_email = true;
					}else{
						angular.element("#alert_message").html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+response.message+'</div>');
					}
				}
				
			});	
		
	}	
}
</script>

<?php $this->load->view('includes/footer'); ?>		