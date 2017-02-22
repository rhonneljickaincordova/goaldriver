<div class="modal fade" id="view_meeting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background-color: #5bc0de">
				<div class="row">
					<h4 class="modal-title" id="myModalLabel" style="color:white;">
						<div class="col-md-1 col-xs-2"><i class="fa fa-plus"></i></div>
						<div class="col-md-6 col-xs-8">
							<input class="form-control" type='text' placeholder="Meeting" ng-model="view_meeting_title" readonly/>
						</div>
					</h4>
					<div class="col-md-2 pull-right">    
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>	
				</div>	
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-2">From :</div>
					<div class="col-md-4" style="padding-left:5px;"><strong>{{view_meeting_when_from_date | date: 'MMMM dd, yyyy'}}</strong></div>
				</div>
				<div class="row">
					<div class="col-md-2">To :</div>
					<div class="col-md-4" style="padding-left:5px;"><strong>{{view_meeting_when_to_date |  date: 'MMMM dd, yyyy'}}</strong></div>
				</div>
				<div class="row">
					<div class="col-md-2">Location :</div>
					<div class="col-md-3" style="padding-left:5px;"><strong>{{view_meeting_meeting_location}}</strong></div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-primary" ng-click="update_meeting_from_calendar(view_meeting_id)"><li class="fa fa-floppy-o"></li> View Meeting</button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><li class="fa fa-times"></li> Close</button>
				
			</div>

		</div>
	</div>
</div>

						