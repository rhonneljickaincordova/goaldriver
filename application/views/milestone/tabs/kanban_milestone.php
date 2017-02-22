<!-- by milestone -->
<div ng-show="kanban_filter_id.value == 0">
	<ul id="draggablePanelList_milestone" class="list-unstyled">
		<div ng-repeat="milestone in milestones">
			<div class="col-md-4">
				<li class="panel panel-default">
					<div class="panel-heading">{{milestone.name}}</div>
					<div class="panel-body">
						<div ng-repeat="task in milestone.array_task" >
							<?php 
							if($milestone_permission_name == "readwrite")
							{
								?>
								<a href="" data-toggle="tooltip" data-placement="right" data-original-title="View" title="View" >
									<span data-toggle="modal" data-target="#edit_task_modal" data-section_target="kanban_milestone" data-task_id="{{task.task_id}}">{{task.task_name}}</span>
								</a><br>
								<?php
							}else{
								?>
								<span>{{task.task_name}}</span>
								<br>
								<?php
							}
							?>
							<i class="fa fa-user" title="{{task.owner_name}}"></i>
							<a href="" ng-click="open_task_comment_link(task.task_id,task.task_name)">
								<i data-toggle="tooltip" data-placement="right" data-original-title="Comments" title="Comments" class="fa fa-comments" title="comments" ng-show="task.comment_counter > 0">
								</i>
							</a>
							<hr>
						</div>
					</div>
					<div class="panel-footer">
					<?php 
					if($milestone_permission_name == "readwrite")
					{
						?>
						<a href="" data-target="#add_task_modal" data-toggle="modal" data-section_target="kanban-milestone" data-m_id="{{milestone.id}}"><i><u>Add Task</u></i></a>
						<?php
					}
					?>
					</div>
				</li>
			</div>	
			<div ng-class="(($index+1) %3) == 0 ? 'display_m_liner' : 'hide_liner'"></div>
		</div>
	</ul>
</div>