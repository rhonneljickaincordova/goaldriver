<div class="panel panel-default" >
	<div class="panel-heading">
		Upcoming <span class="badge pull-right" title="Today's Due Task">{{upcoming_counter}}</span>
	</div>
	<div class="panel-body">
			<div ng-repeat ="meeting in upcoming_meetings">
				<br>
					<?php $this->load->view("dashboard/tabs/calendar/upcoming_meeting.php"); ?>
				<br>
			<hr>
			</div>
			<div ng-repeat ="task in upcoming_tasks">
				<br>
					<?php $this->load->view("dashboard/tabs/calendar/upcoming_task.php"); ?>
				<br>
			<hr>
			</div>
			<div ng-repeat ="milestone in upcoming_milestones">
				<br>
					<?php $this->load->view("dashboard/tabs/calendar/upcoming_milestone.php"); ?>
				<br>
			<hr>
			</div>
			
			
	</div>
</div>