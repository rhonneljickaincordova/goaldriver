
<div class="modal fade kpi_modals" id="add_kpiModal" tabindex="-1" role="dialog" aria-labelledby="add_kpiModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<form action="#">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			
			<div class="form-group">
				<i class="fa fa-plus" style=""></i>
				<div class="kpi_modal_header">
					<label>KPI Name</label>
					<input type="text" ng-model="kpi_name" name="kpi_name"  class="form-control" required>
					<p class="alert alert-danger" ng-show="isopen_kpi_name">{{_error_kpi}}</p>
				</div>
			</div>
			
			
		</div>
		<div class="modal-body">
			
			<div class="form-group">
				<small>(Additional description(optional))</small>
				<input type="text" ng-model="kpi_desc" name="kpi_desc"  class="form-control">
			</div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>Frequency</label>
					<select ng-model="kpi_frequency" ng-options="frequency as frequency.label for frequency in frequencyList" class="form-control">
					</select>
				</div>
			</div>
			<div class="col-md-6" style="padding-right: 0px;">
				<div class="form-group">
					<label>Format</label>
					<select ng-model="kpi_format" ng-options="format as format.label for format in formatList" class="form-control">
					</select>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>Target</label>
					<input type="text" ng-model="kpi_target" name="kpi_target"  class="form-control">
					<p class="alert alert-danger" ng-show="isopen_target">{{_error_target}}</p>
				</div>
			</div>
			<div class="col-md-6" style="padding-right: 0px;">
				<div class="form-group">
					<label>Best Direction</label>
					<select ng-model="kpi_best_direction" ng-options="direction as direction.label for direction in directionList" class="form-control">
					</select>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>Aggregate</label>
					<select ng-model="kpi_agg_type" ng-options="aggregate as aggregate.label for aggregate in aggregateList" class="form-control">
					</select>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="form-group rag_thresholds_div">
				<label>RAG thresholds</label>
				<div class="col-md-10">
					<img src="<?php echo base_url()."uploads/icons/rags_bg_325.png";?>"/>	
					<div>
						<div class="col-md-3">
						<input type="text" ng-model="kpi_rag_1" name="kpi_rag_1" class="form-control">
						</div>
						<div class="col-md-3">
						<input type="text" ng-model="kpi_rag_2" name="kpi_rag_2" class="form-control">
						</div>
						<div class="col-md-3">
						<input type="text" ng-model="kpi_rag_3" name="kpi_rag_3" class="form-control">
						</div>
						<div class="col-md-3">
						<input type="text" ng-model="kpi_rag_4" name="kpi_rag_4" class="form-control">
						</div>
					</div>
				
				</div>
				<div style="clear:both;"></div>
				<p class="alert alert-danger" ng-show="isopen_rag">{{_error_rag}}</p>
			</div>
			<div class="form-group">
				<label>{{kpi_usersTitle}}</label>
				<div ng-dropdown-multiselect="" options="kpi_usersOptions" selected-model="kpi_usersModel" extra-settings="kpi_usersSettings" translation-texts="kpi_usersTranslation" events="{onItemDeselect: kpi_usersChange, onItemSelect: kpi_usersChange}"></div>
				<p class="alert alert-danger" style='margin-top:10px;' ng-show="isopen_users">{{_error_kpi_users}}</p>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btn-sm" ng-click="addKPI()">Save</button>
			<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
		</div>
		</form>
    </div>
  </div>
</div>