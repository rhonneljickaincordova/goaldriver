
<div class="modal fade" id="filter_gaugeModal" tabindex="-1" role="dialog" aria-labelledby="filter_gaugeLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="filter_gaugeLabel">Filter Gauge</h4>
		</div>
		<div class="modal-body">
			<!-- FILTER DATE -->
			<div class="col-md-6" style="padding-left: 0px;">
				 <div class="form-group">
					<label>From : </label>
					<div class='input-group date' id='dp_gauge_from'>
						<input type='text' id="dp_gauge_text-from" class="form-control"/>
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-6" style="padding-left: 0px;">
				<div class="form-group">
					<label>To : </label>
					<div class='input-group date' id='dp_gauge_to'>
						<input type='text' id="dp_gauge_text-to" class="form-control"/>
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>
			<div style="clear:both;"></div>
			<p class="alert alert-danger" ng-show="isopen_gauge_date_error">{{_error_filter_gauge_date}}</p>
			<!-- FILTER GAUGE ID -->
			<input type="hidden" ng-value="filter_gauge_id" class="form-control">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary btn-sm" ng-click="filter_guage()">Filter</button>
		</div>
    </div>
  </div>
</div>