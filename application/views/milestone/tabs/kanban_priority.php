<!-- by priority -->
<div ng-show="kanban_filter_id.value == 1">
	<ul id="draggablePanelList_priority" class="list-unstyled">
		
		<div class="col-md-3">
			<li class="panel panel-default">
			  <div class="panel-heading">None</div>
			  <div class="panel-body">
				<div ng-repeat="none in filter_nones">
					<?php 
					if($milestone_permission_name == "readwrite")
					{
						?>
						<a href="" data-toggle="modal" data-target="#edit_task_modal" data-section_target="kanban_priority" data-task_id="{{none.task_id}}">
							<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{none.task_name}}</span>
						</a><br>
						<?php
					}else{
						?>
						<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{none.task_name}}</span>
						<br>
						<?php
					}
					?>		
					<i class="fa fa-user" title="{{none.owner_name}}"></i>
					<a href="" ng-click="open_task_comment_link(none.task_id,none.task_name)">
						<i data-toggle="tooltip" data-placement="right" data-original-title="Comments" title="Comments" class="fa fa-comments" title="Comments" ng-show="none.comment_counter > 0"></i>
					</a>
					<hr>
				 </div>
			  </div>
			  <div class="panel-footer">
			  <?php 
				if($milestone_permission_name == "readwrite")
				{
					?>
					<a href="" data-target="#add_task_modal" data-toggle="modal" data-section_target="kanban-priority" data-priority="1"><i><u>Add Task</u></i></a>
					<?php
				}
				?>
			  </div>
			</li>
		</div>

		<div class="col-md-3">
			<li class="panel panel-success">
			  <div class="panel-heading">Low</div>
			  <div class="panel-body">
				<div ng-repeat="low in filter_lows">
					<?php 
					if($milestone_permission_name == "readwrite")
					{
						?>
					<a href="" data-toggle="modal" data-target="#edit_task_modal" data-task_id="{{low.task_id}}"> 
						<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{low.task_name}}</span>
					</a><br>
					<?php
					}else{
						?>
						<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{low.task_name}}</span>
						<br>
						<?php
					}
					?>	
					<i class="fa fa-user" title="{{low.owner_nane}}"></i>
					<a href="" ng-click="open_task_comment_link(low.task_id,low.task_name)">
						<i data-toggle="tooltip" data-placement="right" data-original-title="Comments" title="Comments"  class="fa fa-comments" title="comments" ng-show="low.comment_counter > 0"></i>
					</a>
					<hr>
				 </div>
			  </div>
			  <div class="panel-footer">
			  <?php 
				if($milestone_permission_name == "readwrite")
				{
					?>
					<a href="" data-target="#add_task_modal" data-toggle="modal" data-section_target="kanban-priority" data-priority="2"><i><u>Add Task</u></i></a>
					<?php
				}
				?>
			  </div>
			</li>
		</div>


		<div class="col-md-3">
			<li class="panel panel-warning">
			  <div class="panel-heading">Medium</div>
			  <div class="panel-body">
				<div ng-repeat="medium in filter_mediums">
				<?php 
					if($milestone_permission_name == "readwrite")
					{
						?>
					<a href="" data-toggle="modal" data-target="#edit_task_modal" data-task_id="{{medium.task_id}}"> 
						<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{medium.task_name}}</span></a><br>
					<?php
					}else{
						?>
						<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{medium.task_name}}</span><br>
						<?php
					}
					?>		
					<i class="fa fa-user" title="{{medium.owner_name}}"></i>
					<a href=""  ng-click="open_task_comment_link(medium.task_id,medium.task_name)">
						<i data-toggle="tooltip" data-placement="right" data-original-title="Comments" title="Comments"  class="fa fa-comments" title="comments" ng-show="medium.comment_counter > 0"></i>
					</a>
					<hr>
				</div>
			  </div>
			  <div class="panel-footer">
			<?php 
				if($milestone_permission_name == "readwrite")
				{
					?>
					<a href="" data-target="#add_task_modal" data-toggle="modal" data-section_target="kanban-priority" data-priority="3"><i><u>Add Task</u></i></a>
					<?php
				}
				?>	
			  </div>
			</li>
		</div>


		<div class="col-md-3">
			<li class="panel panel-danger">
			  <div class="panel-heading">High</div>
			  <div class="panel-body">
				<div ng-repeat="high in filter_highs">
				<?php 
					if($milestone_permission_name == "readwrite")
					{
						?>
					<a href="" data-toggle="modal" data-target="#edit_task_modal"  data-task_id="{{high.task_id}}" > 
						<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{high.task_name}}</span>
					</a><br>
					<?php
					}else{
						?>
						<span data-toggle="tooltip" data-placement="right" data-original-title="View" title="View">{{high.task_name}}</span>
						<br>
						<?php
					}
					?>	
					<i class="fa fa-user" title="{{high.owner_name}}"></i>
					<a href="" ng-click="open_task_comment_link(high.task_id,high.task_name)">
						<i data-toggle="tooltip" data-placement="right" data-original-title="Comments" title="Comments"  class="fa fa-comments" title="comments" ng-show="high.comment_counter > 0"></i>
					</a>
					<hr>
					
				</div>
			  </div>
			  <div class="panel-footer">
			  <?php 
				if($milestone_permission_name == "readwrite")
				{
					?>
					<a href="" data-target="#add_task_modal" data-toggle="modal" data-section_target="kanban-priority" data-priority="4"><i><u>Add Task</u></i></a>
					<?php
				}
				?>
			  </div>
			</li>
		</div>
	</ul>
</div>
