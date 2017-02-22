<!-- Delete Milestone Modal-->
<div class="modal fade" id="delete_milestone_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog comment_modal">
		<div class="modal-content" >

			<div class="modal-header ">
				<strong>Delete</strong>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>

			<div class="modal-body">
				<p>Are you sure to delete this?</p>
				<div class="alert alert-success"  ng-show="delete_data">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					{{delete_message}}
				</div>	
			</div>
			
			<div class="modal-footer">
				<button type="button" id="create-milestone" class="btn btn-danger" ng-click="delete_milestone()">Delete</button>
				<button type="button" id="create-team" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>

		</div>
	</div>
</div>