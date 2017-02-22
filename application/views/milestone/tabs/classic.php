<!-- Classic Tab -->

	<div class="clearfix" style="margin-top:1em;">
	<?php 
	if($milestone_permission_name == "readwrite"){
		?>
		<div class="form-group">
			<div class="btn-toolbar">
				<button type="button" class="btn btn-primary"  ng-click="open_add_milestone()" data-toggle="modal" ><i class="fa fa-plus"></i> New Milestone</button>
			</div>
		</div>
		<?php
	}
	?>
	</div>
	
<div class="" id="scheduler">
	<table id="datatable_milestones" class="table table-responsive">
		<thead>
			<tr>
				<th></th>
				<th>Milestone id</th>
				<th>Duedate</th>
				<th>Duedate_format</th>
				<th>Duedate fromat String</th>
				<th>Startdate</th>
				<th>Startdate_format</th>
				<th>Milestone description</th>
				<th>Owner</th>
				<th>Status</th>
				<th>Editable</th>
				<th>Tasks Loaded</th>
				<th>Date Sorter</th>
				<th>Task Count</th>
				<th>bShowOnDash</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
			
			
