<div class="clearfix" style="margin-top:1em;" ng-show='add_task_div_show'>
	
	<div class="form-group">
		<div class="btn-toolbar">
			<button type="button" data-toggle='modal' data-target='#add_task_modal' data-table_name="task" class="btn btn-primary"  ><i class="fa fa-plus"></i> New Task</button>
		</div>
	</div>
	
</div>

<div class="" id="plain_table_con">
	<table id="datatable_tasks" class="table table-responsive">
		<thead>
			<tr>
				<th>Task ID</th>
				<th></th>
				<th>Task</th>
				<th>Description</th>
				<th>Start Date</th>
				<th>StartDate Format</th>
				<th>Start Date</th>
				<th>Task dueDate</th>
				<th>Task dueDate_format</th>
				<th>Due Date</th>
				<th>By Who</th>
				<th>Status</th>
				<th>Priority</th>
				<th>Milestone ID</th>
				<th>participants</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>	
<?php 
	$this->load->view("dashboard/modals/task_tab/add_task.php"); 
	$this->load->view("dashboard/modals/task_tab/edit_task.php"); 
	$this->load->view("dashboard/modals/task_tab/delete_task.php"); 
	
	/* 
	
	
	$this->load->view("milestone/modals/task_comment_standalone.php");  */
?>