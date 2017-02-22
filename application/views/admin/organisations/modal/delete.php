<!-- Modal Delete-->
<div class="modal fade" id="deleteOrganisationModal" tabindex="-1" role="dialog" aria-labelledby="deleteOrganisationModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Delete</strong>
		</div>
		<div class="modal-body">
			<p>Are you sure to delete this?</p>
			<div class="alert alert-success ng-binding ng-hide" ng-show="delete_data" style="">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
				Successfully deleted.
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" id="delete_organisation_id" value="">
			<button type="button" id="delete-organisation" class="btn btn-danger" ng-click="deleteOrganisation()">Delete</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		</div>
    </div>
  </div>
</div>