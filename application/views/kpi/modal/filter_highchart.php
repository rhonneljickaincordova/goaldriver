
<div class="modal fade" id="filter_highchartModal" tabindex="-1" role="dialog" aria-labelledby="filter_highchartLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="filter_highchartLabel">Filter Graph</h4>
		</div>
		<div class="modal-body">
			<!-- FILTER DATE -->
			<div class="col-md-6" style="padding-left: 0px;">
				 <div class="form-group">
					<label>From : </label>
					<div class='input-group date' id='dp_graph_from'>
						<input type='text' id="filter_date_text-from" class="form-control"/>
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>To : </label>
					<div class='input-group date' id='dp_graph_to'>
						<input type='text' id="filter_date_text-to" class="form-control"/>
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>
			<div style="clear:both;"></div>
			<p class="alert alert-danger" ng-show="isopen_date_error">{{_error_filter_date}}</p>
			<!-- FILTER ASSIGNED USER -->
			<div class="form-group">
				<label>{{filter_kpi_usersTitle}}</label>
				<div ng-dropdown-multiselect="" options="filter_kpi_usersOptions" selected-model="filter_kpi_usersModel" extra-settings="filter_kpi_usersSettings" translation-texts="filter_kpi_usersTranslation" events="{onItemDeselect: onFilterUserChange, onItemSelect: onFilterUserChange}"ng-dropdown-multiselect-disabled="filter_statusFilterDisabled"></div>
			</div>
			<!-- FILTER ASSIGNED USER -->
			<div class="form-group display_option_div">
				<label>Display options</label><br/>
				<label class="option_label">
					<input type="checkbox" ng-model="filter_bShowAverage" [name="filter_bShowAverage"][ng-true-value="true"][ng-false-value="false"]>
					<span class="filter_bShowAverage_tooltip" data-toggle="tooltip" title="Show average of aggregated actuals.">Show Average</span>
				</label><br/>
				<label class="option_label">
					<input type="checkbox" ng-model="filter_bShowBreakdown" [name="filter_bShowBreakdown"][ng-true-value="true"][ng-false-value="false"]>
					<span class="filter_bShowBreakdown_tooltip" data-toggle="tooltip" title="Show breakdown of data entered by assigned users.">Show Breakdown</span>
				</label>
			</div>
			<!-- FILTER GRAPH ID -->
			<input type="hidden" ng-value="filter_graph_id" class="form-control">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary btn-sm" ng-click="filter_graph()">Filter</button>
		</div>
    </div>
  </div>
</div>