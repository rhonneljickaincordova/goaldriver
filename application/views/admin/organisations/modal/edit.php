<!-- Modal Edit-->
<div class="modal fade" id="editOrganisationModal" tabindex="-1" role="dialog" aria-labelledby="editOrganisationModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="editOrganisationModalLabel">Edit Organisation</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
         <label>Organisation Name</label>
         <input type="text" ng-model="edit_organisation_name" name="edit_organisation_name"  class="form-control" required>
        </div>
		<p class="alert alert-danger" ng-show="isopen_edit_organisation_name">{{_error_edit_organisation}}</p>
		<input type="hidden" ng-value="OrganId" id="organ_id" name="organ_id" value="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" ng-click="updateOrganisation()">Save</button>
      </div>
      
    </div>
  </div>
</div>