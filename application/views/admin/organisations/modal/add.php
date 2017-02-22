<!-- Modal Add-->
<div class="modal fade" id="addOrganisationModal" tabindex="-1" role="dialog" aria-labelledby="addOrganisationModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="addOrganisationModalLabel">Add New Organisation</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
         <label>Organisation Name</label>
         <input type="text" ng-model="organisation_name" name="organisation_name"  class="form-control" required>
        </div>
        <div class="form-group">
          <label for="copy-existing-organ"><input type="checkbox" ng-click="isCopyExistingOrgan = !isCopyExistingOrgan" id="copy-existing-organ"> Copy from existing organisation</label>
          <?php if(count($organisations) > 0): ?>
          <div ng-show="isCopyExistingOrgan">
            <select name="copy_organisation" ng-model="copy_organisation" class="form-control">
              <option value="">--Select Organisation--</option>
              <?php foreach($organisations as $organ): ?>
              <option value="<?php echo $organ->organ_id?>"><?php echo $organ->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>
        </div>
		<p class="alert alert-danger" ng-show="isopen_organisation_name">{{_error_organisation}}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" ng-click="addOrganisation()">Add</button>
      </div>
      
    </div>
  </div>
</div>