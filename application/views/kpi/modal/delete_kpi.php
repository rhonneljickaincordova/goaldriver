
<div class="modal fade" id="delete_kpiModal" tabindex="-1" role="dialog" aria-labelledby="delete_kpiModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Delete</strong>
		</div>
		<div class="modal-body">
			<p>All data that has been entered for this KPI will be deleted.</p><p>Are you sure you want to delete?</p>
			<div class="alert alert-success ng-binding ng-hide" ng-show="delete_data" style="">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
				Successfully deleted.
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" id="delete_kpi_id" value="">
			<button type="button" id="delete-kpi" class="btn btn-danger" ng-click="deleteKPI()">Delete</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		</div>
    </div>
  </div>
</div>