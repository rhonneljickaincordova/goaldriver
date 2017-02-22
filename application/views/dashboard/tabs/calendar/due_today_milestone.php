<div class="row">
	<div class="col-md-2">
		<small>
			<img class="test" data-toggle="tooltip" data-placement="left" title="{{milestone.owner_name}}" src="../uploads/icon1.ico">
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
					<span ng-if="milestone.status == '<?php echo $x; ?>'">
						<div class="c100 p<?php echo $percent; ?> small  green">
							<span>{{milestone.status_name}}</span>
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
			</div>
			
			<div class="col-md-8">
				<div class="row">

					<div class="col-md-12">
						<small>
							<span><a href="" ng-click="open_milestone_name_link(task.id,task.name)">{{milestone.name}}</a></span><br>
							 by: <span>{{milestone.created_by}}</span> 
						</small>
					</div>
				</div>
				<div><small><span>due date: {{milestone.dueDate | date: "MMMM dd, yyyy"}}</span></small></div>
				<div><small> created:  {{milestone.entered_on | date:"MM/dd/yyyy 'at' h:mma"}}</small></div>
			</div>	
		</div>
		
			

	</div>
</div>