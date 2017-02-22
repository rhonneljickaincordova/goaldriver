
<div class="modal fade kpi_modals" id="edit_kpiModal" tabindex="-1" role="dialog" aria-labelledby="edit_kpiModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<div class="form-group">
				<i class="fa fa-plus" style=""></i>
				<div class="kpi_modal_header">
					<label>KPI Name</label>
					<input type="text" ng-model="edit_kpi_name" name="kpi_name"  class="form-control">
					<p class="alert alert-danger" ng-show="isopen_edit_kpi_name">{{_error_edit_kpi}}</p>
				</div>
			</div>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<small>(Additional description(optional))</small>
				<input type="text" ng-model="edit_kpi_desc" name="kpi_desc"  class="form-control">
			</div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>Frequency</label>
					<select ng-model="edit_kpi_frequency" ng-options="frequency as frequency.label for frequency in frequencyList | filter: {value: $frequency.value}" class="form-control" ng-disabled="kpi_frequency_disable">
					</select>
				</div>
			</div>
			<div class="col-md-6" style="padding-right: 0px;">
				<div class="form-group">
					<label>Format</label>
					<select ng-model="edit_kpi_format" ng-options="format as format.label for format in formatList" class="form-control">
					</select>
					<!--<p class="alert alert-danger" ng-show="isopen_organisation_name">{{_error_organisation}}</p>-->
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>Target</label>
					<input type="text" ng-model="edit_kpi_target" name="kpi_target"  class="form-control">
					<p class="alert alert-danger" ng-show="isopen_edit_target">{{_error_edit_target}}</p>
				</div>
			</div>
			<div class="col-md-6" style="padding-right: 0px;">
				<div class="form-group">
					<label>Best Direction</label>
					<select ng-model="edit_kpi_best_direction" ng-options="direction as direction.label for direction in directionList" class="form-control">
					</select>
					<!--<p class="alert alert-danger" ng-show="isopen_organisation_name">{{_error_organisation}}</p>-->
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>Aggregate</label>
					<select ng-model="edit_kpi_agg_type" ng-options="aggregate as aggregate.label for aggregate in aggregateList" class="form-control">
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
						<input type="text" ng-model="edit_kpi_rag_1" name="kpi_rag_1"  class="form-control">
						</div>
						<div class="col-md-3">
						<input type="text" ng-model="edit_kpi_rag_2" name="kpi_rag_2"  class="form-control">
						</div>
						<div class="col-md-3">
						<input type="text" ng-model="edit_kpi_rag_3" name="kpi_rag_3"  class="form-control">
						</div>
						<div class="col-md-3">
						<input type="text" ng-model="edit_kpi_rag_4" name="kpi_rag_4"  class="form-control">
						</div>
					</div>
				</div>
				<div style="clear:both;"></div>
				<p class="alert alert-danger" ng-show="isopen_edit_rag">{{_error_edit_rag}}</p>
				<input type="hidden" ng-value="edit_kpi_id" name="kpi_id"  class="form-control">
				
			</div>
			<div class="form-group">
				<label>{{edit_kpi_usersTitle}}</label>
				<div ng-dropdown-multiselect="" options="edit_kpi_usersOptions" selected-model="edit_kpi_usersModel" extra-settings="edit_kpi_usersSettings" translation-texts="edit_kpi_usersTranslation" events="{onItemDeselect: edit_kpi_usersChange, onItemSelect: edit_kpi_usersChange}"></div>
				<p class="alert alert-danger" ng-show="isopen_edit_kpi_users">{{_error_edit_kpi_users}}</p>
			</div>
			<div class="form-group kpi_days_settings" ng-show="isopen_edit_kpi_days">
				<label>Days Settings</label>
				<div class="col-md-12 edit_kpi_days_list">
					<small id='kd-1' ng-click="change_kpi_days('1')">Sunday</small>
					<small id='kd-2' ng-click="change_kpi_days('2')">Monday</small>
					<small id='kd-3' ng-click="change_kpi_days('3')">Tuesday</small>
					<small id='kd-4' ng-click="change_kpi_days('4')">Wednesday</small>
					<small id='kd-5' ng-click="change_kpi_days('5')">Thursday</small>
					<small id='kd-6' ng-click="change_kpi_days('6')">Friday</small>
					<small id='kd-7' ng-click="change_kpi_days('7')">Saturday</small>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btn-sm" ng-click="updateKPI()">Update KPI</button>
			<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
		</div>
    </div>
  </div>
</div>