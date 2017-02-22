<!--Create Task  -->
<div class="modal fade" id="add_task_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- modal header -->
			<div class="modal-header" style="background-color:#5CB85C;">
				<div class="row">
					<h4 class="modal-title" id="myModalLabel" style="color:white;">
						<div class="col-md-1 col-xs-2"><i class="fa fa-plus"></i></div>
						<div class="col-md-6 col-xs-8">
							<input class="form-control" name="meeting_tags" placeholder="Task Name" ng-model="task_name" />
							<p class="alert alert-danger" ng-show="isopen_task_name">{{_error_task_name}}</p>
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
							<select ng-model="task_m_id" ng-options="task_m_id as task_m_id.label for task_m_id in milestonesDropdownList" class="form-control">
							</select>
						</div>
						<!--owner dropdown-->
						<div class="form-group" >
							<label>Owner</label>  <br/>
							<select ng-model="task_owner" ng-options="task_owner as task_owner.label for task_owner in organ_usersList" class="form-control">
							</select>
						 
						</div>
						<p class="alert alert-danger" ng-show="isopen_owner_task">{{_error_owner}}</p>
						<!--who else dropdown-->
						<div class="form-group who-else">
							<label>Who else</label><br/>
							<ui-select multiple ng-model="_multipleUser.user_task">
								<ui-select-match>{{$item.label}}</ui-select-match>
									<ui-select-choices repeat="person in organ_usersList | propsFilter: {label: $select.search}">
										<div ng-bind-html="person.label | highlight: $select.search"></div>
									</ui-select-choices>
							</ui-select>
						</div>
						<p class="alert alert-danger" ng-show="isopen_who_else">{{_error_who_else}}</p>
						<!--description-->
						<div class="form-group">
							<label for="description" class="control-label">Description</label>
							<textarea id="description" class="form-control" name="description" ng-model="task_description"></textarea>
						</div>
					
					</div>
					<!--start date-->
					<div class="col-md-6">
						<div class="form-group">
							<div class="row">
								<div class='col-sm-12'>
										<label>Start Date</label>  <br/>
									<input type="text" class="form-control" id="task_start_date" />
								</div>
							</div>
						</div>
						<!--due date-->
						<div class="form-group">
							<div class="row">
								<div class='col-sm-12'>
										<label>Due Date</label>  <br/>
									<input type="text" class="form-control" id="task_due_date" />
								</div>
							</div>
						</div>
						<p class="alert alert-danger" ng-show="isopen_due_date">{{_error_t_due_date}}</p>
						<!--priority dropdown-->
						<div class="form-group">
							<label>Priority</label>  <br/>
								<select ng-model="task_priority" ng-options="task_priority as task_priority.label for task_priority in priorityList" class="form-control">
							</select>				  
						</div>
						<p class="alert alert-danger" ng-show="isopen_priority">{{_error_priority}}</p>
						<!--status dropdown-->	
						<div class="form-group">
							<label>Status</label>  <br/>
							<select ng-model="task_status" ng-options="task_status as task_status.label for task_status in statusList" class="form-control">
							</select>	
						</div>
					
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" id="create-task" class="btn btn-primary" ng-click="save_task()">Save </button>
					<button type="button" id="create-team" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</div>

			</div>
		</div>
	</div>
</div>