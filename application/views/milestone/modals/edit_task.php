<!--Edit Task  -->
<div class="modal fade" id="edit_task_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- modal header -->
			<div class="modal-header" style="background-color:#5CB85C;">
				<div class="row">
					<h4 class="modal-title" id="myModalLabel" style="color:white;">
						<div class="col-md-1 col-xs-2"><i class="fa fa-plus"></i></div>
						<div class="col-md-6 col-xs-8">
							<input class="form-control" name="meeting_tags" placeholder="Task Name" ng-model="edit_task_name" />
							<p class="alert alert-danger" ng-show="isopen_edit_task_name">{{_error_edit_task_name}}</p>
						</div>
					</h4>
					<div class="col-md-2 pull-right">    
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>		
				</div>	
			</div>
			<!-- modal body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<!--milestone dropdown-->
						<div class="form-group" >
							<label>Milestone</label>  
							<select ng-model="edit_task_m_id" ng-options="edit_task_m_id as edit_task_m_id.label for edit_task_m_id in milestonesDropdownList" class="form-control">
							</select>
						</div>
						<!--owner dropdown-->
						<div class="form-group" >
							<label>Owner</label>  <br/>
							<select ng-model="edit_task_owner" ng-options="edit_task_owner as edit_task_owner.label for edit_task_owner in organ_usersList" class="form-control">
							</select>
						 
						</div>
						<p class="alert alert-danger" ng-show="isopen_edit_owner_task">{{_error_owner}}</p>
						<!--who else dropdown-->
						<div class="form-group who-else">
							<label>Who else</label><br/>
							<ui-select multiple ng-model="_edit_multipleUser.user_task">
								<ui-select-match>{{$item.label}}</ui-select-match>
									<ui-select-choices repeat="person in organ_usersList | propsFilter: {label: $select.search}">
										<div ng-bind-html="person.label | highlight: $select.search"></div>
									</ui-select-choices>
							</ui-select>
						</div>
						<p class="alert alert-danger" ng-show="isopen_edit_who_else">{{_error_who_else}}</p>
						<!--description-->
						<div class="form-group">
							<label for="description" class="control-label">Description</label>
							<textarea id="description" class="form-control" name="description" ng-model="edit_task_description"></textarea>
						</div>
					
					</div>
					<!--start date-->
					<div class="col-md-6">
						<div class="form-group">
							<div class="row">
								<div class='col-sm-12'>
										<label>Start Date</label>  <br/>
									<input type="text" class="form-control" id="edit_task_start_date" />
								</div>
							</div>
						</div>
						<!--due date-->
						<div class="form-group">
							<div class="row">
								<div class='col-sm-12'>
										<label>Due Date</label>  <br/>
									<input type="text" class="form-control" id="edit_task_due_date" />
								</div>
							</div>
						</div>
						<p class="alert alert-danger" ng-show="isopen_edit_due_date">{{_error_edit_t_due_date}}</p>
						<!--priority dropdown-->
						<div class="form-group">
							<label>Priority</label>  <br/>
								<select ng-model="edit_task_priority" ng-options="edit_task_priority as edit_task_priority.label for edit_task_priority in priorityList" class="form-control">
							</select>				  
						</div>
						<p class="alert alert-danger" ng-show="isopen_edit_priority">{{_error_edit_priority}}</p>
						<!--status dropdown-->	
						<div class="form-group">
							<label>Status</label>  <br/>
							<select ng-model="edit_task_status" ng-options="edit_task_status as edit_task_status.label for edit_task_status in statusList" class="form-control">
							</select>	
						</div>
					
					</div>
				</div>
				<?php 
					$this->load->view("milestone/modals/task_comment.php"); 
				?>
				<div class="modal-footer">
					<button type="button" id="create-task" class="btn btn-primary" ng-click="update_task()">Save </button>
					<button type="button" id="create-team" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</div>

			</div>
		</div>
	</div>
</div>