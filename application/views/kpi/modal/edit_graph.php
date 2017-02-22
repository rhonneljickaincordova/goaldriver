<?php 
$organ_id = $this->session->userdata("organ_id");
$is_organisation_owner = is_organisation_owner($organ_id);
?>
<div class="modal fade graph_modals" id="edit_graphModal" tabindex="-1" role="dialog" aria-labelledby="edit_graphModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<div class="graph_modal_header">
				<label>Graph Name</label>
				<input type="text" ng-model="edit_graph_name" name="graph_name"  class="form-control">
				<!--<p class="alert alert-danger" ng-show="isopen_organisation_name">{{_error_organisation}}</p>-->
			</div>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<small>(Edititional description(optional))</small>
				<input type="text" ng-model="edit_graph_description" name="graph_description"  class="form-control">
			</div>
			<div class="col-md-9" style="padding-left: 0px;">
				<div class="form-group">
					<label>KPI</label>
					<select ng-model="edit_graph_kpis" ng-change="ChangeGoal('edit')" ng-options="graph_kpi as graph_kpi.label for graph_kpi in graph_kpiList" class="form-control">
					</select>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>Type</label>
					<select ng-model="edit_graph_type" ng-options="graph_type as graph_type.label for graph_type in graph_typeList" class="form-control">
					</select>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>Reset Frequency</label>
					<select ng-model="edit_reset_frequency_type" ng-options="rf_daily_type as rf_daily_type.label for rf_daily_type in reset_frequency_dailyList" class="form-control">
					</select>
				</div>
			</div>
			<div style="clear:both;"></div>
			<div class="form-group display_option_div">
				<label>Display options</label><br/>
				<label class="option_label">
					<input type="checkbox" ng-model="edit_bShowOnDash" [name="edit_bShowOnDash"][ng-true-value="true"][ng-false-value="false"]>
					<span class="edit_bShowOnDash_tooltip" data-toggle="tooltip" title="Show this graph to Dashboard Page.">Show this graph on dashboard</span>
				</label>
				<br/>
				<label class="option_label">
					<input type="checkbox" ng-model="edit_bShowAverage" [name="edit_bShowAverage"][ng-true-value="true"][ng-false-value="false"]>
					<span class="edit_bShowAverage_tooltip" data-toggle="tooltip" title="Show average of aggregated actuals over number of dates.">Show Average</span>
				</label>
				<br/>
				<label class="option_label">
					<input type="checkbox" ng-model="edit_bShowBreakdown" [name="edit_bShowBreakdown"][ng-true-value="true"][ng-false-value="false"]>
					<span class="edit_bShowBreakdown_tooltip" data-toggle="tooltip" title="Show breakdown of data entered by assigned users.">Show Breakdown</span>
				</label>
				<br/>
				<label class="option_label">
					<input type="checkbox" ng-model="edit_bShowGaugeOnDash" [name="edit_bShowGaugeOnDash"][ng-true-value="true"][ng-false-value="false"]>
					<span class="edit_edit_bShowGaugeOnDash_tooltip" data-toggle="tooltip" title="Show progress gauge on dashboard.">Show progress gauge on dashboard</span>
				</label>
			</div>
			<div style="clear:both;"></div>
			
			<!-- FILTER ASSIGNED USER -->
			<div class="form-group">
				<label>{{edit_kpi_usersTitle}}</label>
				<div ng-dropdown-multiselect="" options="edit_kpi_usersOptions" selected-model="edit_kpi_usersModel" extra-settings="edit_kpi_usersSettings" translation-texts="edit_kpi_usersTranslation" events="{onItemDeselect: edit_onFilterUserChange, onItemSelect: edit_onFilterUserChange}" ng-dropdown-multiselect-disabled="edit_statusFilterDisabled"></div>
			</div>
				
			<div style="clear:both;"></div>
			<div class="form-group">
				<label>{{edit_sharedToUsersTitle}}</label>
				<div ng-dropdown-multiselect="" options="edit_sharedToUsersOptions" selected-model="edit_sharedToUsersModel" extra-settings="edit_sharedToUsersSettings" translation-texts="edit_sharedToUsersTranslation" ng-dropdown-multiselect-disabled="edit_sharedToUsersDisabled"></div>
			</div>
			<input type="hidden" ng-value="edit_graph_id" name="graph_id"  class="form-control">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary btn-sm" ng-click="editGraph()">Save Graph</button>
		</div>
    </div>
  </div>
</div>