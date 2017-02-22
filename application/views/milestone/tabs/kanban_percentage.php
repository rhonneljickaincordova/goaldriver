<!-- by percentage -->
<div ng-show="kanban_filter_id.value == 2">
	<ul id="draggablePanelList_percentage" class="list-unstyled">
		<?php 
		for($x = 0; $x <= 10; $x++ )
		{
			$percent = $x * 10;
			$width = "width:".(($x == 0) ? 5 : $x * 10) . "%"; 
			if($x%2 == 0 && $x != 10){
				echo '<div class="col-md-12 padding-0">';
			}
			?>
			
				<div class="col-md-6">
					<li class="panel panel-default">
					  <div class="panel-heading">
						<div class="progress">
						  <div class="progress-bar" role="progressbar" aria-valuenow="0"
							aria-valuemin="0" aria-valuemax="100" style="<?php echo $width; ?>"> 
								<?php echo $percent."%"; ?>
						  </div>
						</div>
					  </div>
					  <div class="panel-body">
						<div ng-repeat="task in filter_<?php echo $x; ?>">
						<?php 
							if($milestone_permission_name == "readwrite")
							{
								?>
								<a href="" data-toggle="modal" data-target="#edit_task_modal" data-section_target="kanban_percentage" data-task_id="{{task.task_id}}"> 
									<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{task.task_name}}</span>
								</a><br>
								<?php
							}else{
								?>
								<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{task.task_name}}</span>
								<br>
								<?php
							}
							?>
							<i class="fa fa-user" title="{{task.owner_name}}"></i>
							<a href="" ng-click="open_task_comment_link(task.task_id,task.task_name)"><i data-toggle="tooltip" data-placement="right" data-original-title="Comments" title="Comments" class="fa fa-comments" title="comments" ng-show="task.comment_counter > 0"></i></a>
							<hr>
						</div>
					  </div>
					  <div class="panel-footer">
					  <?php 
						if($milestone_permission_name == "readwrite")
						{
							?>
							<a href="" data-target="#add_task_modal" data-toggle="modal" data-section_target="kanban-percentage" data-status="<?php echo $x; ?>"><i><u>Add Task</u></i></a>
							<?php
						}
						?>
					  </div>
					</li>
				</div>	
			<?php 
			if($x%2 == 1 && $x != 10){
				echo '</div>';
			}
		}
		?>	
	</ul>
</div>
