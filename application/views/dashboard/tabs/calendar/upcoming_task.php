<div class="row">
	<div class="col-md-2">
																					<small>
			<img class="test" data-toggle="tooltip" data-placement="left" title="{{task.first_name}} {{task.last_name}}" src="../uploads/icon1.ico">
		</small>
	</div>
	<div class="col-md-10">
		
		<div  class="row">
			<div class="col-md-4">
				<?php 
				$x = 0;
				while($x <= 10){
					$percent = $x * 10;
					?>
					<span ng-if="task.status == '<?php echo $x; ?>'">
						<div class="c100 p<?php echo $percent; ?> small  green">
							<span>{{task.status_name}}</span>
							<div class="slice">
								<div class="bar"></div>
								<div class="fill"></div>
							</div>
						</div>
					</span>
					<?php
					$x++;
				}
				?>
				<!-- to check p4 instead of p40-->
			</div>
			
			<div class="col-md-8">
				<div class="row">

					<div class="col-md-10">
						<small>
							<span><a href="" ng-click="open_task_name_link(task.task_id)">{{task.task_name}}</a></span><br>
							 by: <span>{{task.first_name}} {{task.last_name}}</span> 
						</small>
					</div>

					<div class="col-md-1">

						<div class="progress" title="None" style="width:10px" ng-if="task.priority == '1'">
						  <div class="progress-bar progress-bar-success " role="progressbar"
						  aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:100%">
							</div>
						</div>

						<div class="progress"  title="Low" style="width:10px" ng-if="task.priority == '2'">
						  <div class="progress-bar progress-bar-info " role="progressbar"
						  aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:100%">
						  </div>
						</div>

						<div class="progress" title="Medium" style="width:10px" ng-if="task.priority == '3'">
						  <div class="progress-bar progress-bar-warning " role="progressbar"
						  aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:100%">
						 </div>
						</div>

						<div class="progress" title="High" style="width:10px" ng-if="task.priority == '4'">
						  <div class="progress-bar progress-bar-danger " role="progressbar"
						  aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:100%">
						  </div>
						</div>
					</div>	
			
				</div>
				<div><small><span>due date: {{task.task_dueDate | date: "MMMM dd, yyyy"}}</span></small></div>
				<div><small> created:  {{task.entered_on | date:"MM/dd/yyyy 'at' h:mma"}}</small></div>

				<div style="position: relative;">
					<span uib-dropdown on-toggle="toggled(open)" dropdown-append-to-body>
						<a href id="simple-dropdown" uib-dropdown-toggle ng-click="get_comment(task.task_id)">
							<span><small><i class="fa fa-comments" title="comments"></i></small></span>
						</a>
						<ul class="dropdown-menu scrollable-menu" uib-dropdown-menu aria-labelledby="simple-dropdown">
							<li ng-repeat="comment in task_comments" style="width:400px;">
								<div class="container-fluid">
									<br>
								  <a href>{{comment.comment}}</a><br>
								  <small style="font-size:10px;">by : {{comment.first_name}} {{comment.last_name}} -  {{comment.date_post | date:"MM/dd/yyyy 'at' h:mma"}} </small>
								</div>
							  <hr>
							</li>
						</ul>
					</span>
						  - 
				</div>
			</div>	
			
		</div>																			
	</div>
</div>