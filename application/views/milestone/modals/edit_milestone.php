<!-- Create Milestone Modal-->
<div class="modal fade" id="edit_milestone_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- modal header -->
			<div class="modal-header"   style="color:white; background-color:#f0ad4e;">
				<div class="row">
					<div class="col-md-1 col-xs-2"><i class="fa fa-plus"></i></div>
					
					<!-- milestone name -->
					<div class="col-md-6 col-xs-8" >
						  <input class="form-control" name="meeting_tags"  placeholder="Milestone Name " ng-model="edit_milestone_name" />
							<p class="alert alert-danger" ng-show="isopen_edit_name">{{_error_edit_name}}
						</p>
					</div>
					
					<div class="col-md-2 pull-right">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
				</div>
			</div>
			<!-- modal body -->
			<div class="modal-body">
				<div class="form-group" hidden >
				  <label> Milestone Id <small>(required)</small></label>  
				  <input class="form-control" placeholder="Milestone" name="meeting_title" ng-model="edit_milestone_id" readonly/>
				</div>

				<div class="row">
					<div class="col-md-6">
						<!-- milestone owner -->
						<div class="form-group">
							<label>Owner</label>  
							<select ng-model="edit_milestone_owner" ng-options="edit_milestone_owner as edit_milestone_owner.label for edit_milestone_owner in organ_usersList" class="form-control">
							</select>
						</div>
						<p class="alert alert-danger" ng-show="isopen_owner">{{_error_edit_owner}}</p>
						<!-- milestone description -->
						<div class="form-group">
							<label for="description" class="control-label">Description</label>
							<textarea id="description" class="form-control" name="description"  ng-model="edit_milestone_description" ></textarea>
						</div>
						<!-- milestone status -->
						<div class="form-group">
							<label>Status</label>  <br/>
							<select ng-model="edit_milestone_status" ng-options="edit_milestone_status as edit_milestone_status.label for edit_milestone_status in statusList" class="form-control">
							</select>	
						</div>
					</div>
					
					<div class="col-md-6">
						<!-- milestone start date -->
						<div class="form-group">
							<div class="row">
								<div class='col-sm-12'>
										<label>Start Date</label>  <br/>
										<input type='text' id="edit_milestone_start_date" class="form-control"/>
								</div>
								
							</div>
						</div>	
						<!-- milestone due date -->
						<div class="form-group">
							<div class="row">
								<div class='col-sm-12'>
										<label>Due Date</label>  <br/>
										<input type='text' id="edit_milestone_due_date" class="form-control"/>
								</div>
							</div>
						</div>
						<p class="alert alert-danger" ng-show="isopen_edit_milestone_date">{{_error_edit_milestone_date}}</p>	

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="create-milestone" class="btn btn-primary" ng-click="update_milestone()">Update Milestone </button>
				<button type="button" id="create-team" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>

		</div>
	</div>
</div>