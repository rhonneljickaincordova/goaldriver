<div>
	<div class="row">
		<div class="col-md-4">Title: </div>
		<div class="col-md-8 ng-binding">{{meeting.meeting_title}}</div>
	</div>	
	<div class="row">
		<div class="col-md-4">Created By: </div>
		<div class="col-md-8 ng-binding">{{meeting.first_name}} {{meeting.last_name}}</div>
	</div>
	<div class="row">
		<div class="col-md-4">From:</div>
		<div class="col-md-8 ng-binding">{{meeting.when_from_date}}</div>
	</div>
	<div class="row">
		<div class="col-md-4">To:</div>
		<div class="col-md-8 ng-binding">{{meeting.when_to_date}}</div>
	</div>
	<div class="row">
		<div class="col-md-4">Location:</div>
		<div class="col-md-8 ng-binding">{{meeting.meeting_location}}</div>
	</div>
	<div class="row">
		<div class="col-md-12" style='text-align:right'>
			<a href="Dashboard/encrypt_id/{{meeting.meeting_id}}"><small>View Meeting Info</small></a>
		</div>
	</div>
</div>