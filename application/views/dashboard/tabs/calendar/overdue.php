<div class="panel panel-default" >
	<div class="panel-heading">
		Overdue  <span class="badge pull-right" title="Today's Due Task">{{overdue_counter}}</span>
	</div>
	<div class="panel-body">
			<div ng-repeat ="meeting in overdue_meetings">
				<br>
					<?php $this->load->view("dashboard/tabs/calendar/overdue_meeting.php"); ?>
				<br>
			<hr>
			</div>
			<div ng-repeat ="task in overdue_tasks">
				<br>
					<?php $this->load->view("dashboard/tabs/calendar/overdue_task.php"); ?>
				<br>
			<hr>
			</div>
			<div ng-repeat ="milestone in overdue_milestones">
				<br>
					<?php $this->load->view("dashboard/tabs/calendar/overdue_milestone.php"); ?>
				<br>
			<hr>
			</div>
			
	</div>
</div>