<div class="panel panel-default" >
<div class="panel-heading">
	Due Today <span class="badge pull-right" title="Today's Due Task">{{duetoday_counter}}</span>
</div>
<div class="panel-body">
	<div ng-repeat ="meeting in duetoday_meetings">
		<br>
			<?php $this->load->view("dashboard/tabs/calendar/due_today_meeting.php"); ?>
		<br>
		<hr>
	</div>
	<div ng-repeat ="task in duetoday_tasks">
		<br>
			<?php $this->load->view("dashboard/tabs/calendar/due_today_task.php"); ?>
		<br>
		<hr>
	</div>
	<div ng-repeat ="milestone in duetoday_milestones">
		<br>
			<?php $this->load->view("dashboard/tabs/calendar/due_today_milestone.php"); ?>
		<br>
		<hr>
	</div>
	
</div>
</div>