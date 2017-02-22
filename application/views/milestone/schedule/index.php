<?php $this->load->view('includes/header'); ?>

<input type="hidden" class="check_rights" value="<?php echo !empty($disabled) ? "$disabled" : "" ?>" />
<script type="text/javascript">
	$(document).ready(function(){
		var rights = $('.check_rights').val();

		if(rights == "disabled")
		{
			$('button[data-toggle=modal]').hide();
			$('a[data-toggle=modal]').prop("disabled", true);
			$('input[type=button]').prop("disabled", true);
			$('button[type=button]').prop("disabled", true);
		}
	
	});
</script>

<div class="bg-white-wrapper">

<div ng-controller="scheduleListCtrl" ng-init="get_userId('<?php echo $this->session->userdata('user_id'); ?>','<?php echo $this->session->userdata('organ_id'); ?>')">
	
		<uib-tabset active="activeJustified" justified="true">
			<!-- Classic Tab -->
			<uib-tab index="0">
				<uib-tab-heading>
	    			<i class="fa fa-list-alt"></i> Classic
	  			</uib-tab-heading>

					<div class="container-fluid">
						<div class="form-group col-sm-6 col-md-6">
							<div class="btn-toolbar">
								<button type="button" class="btn btn-primary"  ng-click="open_milestone()"   data-toggle="modal" ><i class="fa fa-plus"></i> New Milestone</button>
							</div>
						</div>
					</div>

					<div class="container-fluid" id="scheduler">
						

						<div class="alert alert-success"  ng-show="update_inline_status">
						    <a href="#" class="close" ng-click="close()" data-dismiss="alert" aria-label="close">&times;</a>
						  	{{inline_status_message}}
						</div>
						<div ng-repeat="milestone in milestones  | orderBy:'dueDate'">
						
						 	<div class="panel-group" id="accordion - milestone.counter">
								<div class="panel panel-default">
								    <div class="panel-heading clearfix">
								      <h4 class="panel-title  pull-left">
										<a  class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#accordion-{{$index}}"  ng-click="on_click_accordion(value=!value,$index)">
										
										<i     ng-class="index != $index && value == true ? 'fa fa-plus': index == $index && value == false ? 'fa fa-minus' : index == $index && value == true ? 'fa fa-plus' : index != $index && value == false ? 'fa fa-minus' : 'fa fa-plus'"></i>
											<span>{{milestone.name}}</span>
								        </a>

								      </h4>
								      	<span class="pull-right panel-options">
								      		<span>Due Date : {{milestone.dueDate |  date:'MMMM dd, yyyy' }}</span>
											<a href="" class="edit" ng-click="update_milestone(milestone.id,$index)"   data-target="#modal2"   data-toggle="modal" title="Edit"  >Edit</a>
											<a href="" class="delete" ng-click="open_delete_milestone(milestone.id)"   data-target="#modal4"  data-toggle="modal" title="Delete" >Delete</a>
												<input type="checkbox" class="milestone_checkbox_input" ng-model="milestone.bShowOnDash"  ng-click="onMilestoneShow(milestone.bShowOnDash,milestone.id)" data-toggle="tooltip" data-placement="bottom" title="View / Hide">
				                        		<label for="rating-input-1-5" ng-class="milestone.bShowOnDash ? 'milestone_checkbox_active' :'milestone_checkbox' " ng-click="onMilestoneShow(milestone.bShowOnDash,milestone.id)"></label>
			                           </span>
										
								    </div>
								    <div id="accordion-{{$index}}"  ng-class="default_accordion ? 'panel-collapse collapse ' : 'panel-collapse collapse in'">
								      	<div class="panel-body" style="overflow:visible;">
									      	<a href="" ng-click="open(milestone.id,milestone.name,$index)" data-toggle="modal" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> New Task</a>	
											<br/><br/>	

											<ul class="nav nav-tabs  nav-justified">
											  <li class="active"><a data-toggle="tab" href="#inprogress-{{$index}}">In progress</a></li>
											  <li><a data-toggle="tab" href="#complete-{{$index}}">Completed</a></li>
											
											</ul>

											<div class="tab-content" style="border-left:none;border-right:none;border-bottom:none;">
											  	<div id="inprogress-{{$index}}" class="tab-pane fade in active">
												   	<table class="table table-hover" id="teams - milestone.counter" style="cursor:pointer;">
															<thead>
																<th></th>
																<th ng-click="sortType = 'task_name'; sortReverse = !sortReverse">
																	Task
																	<i ng-hide ="sortType == 'task_name' "  class="glyphicon glyphicon-sort pull-right"></i> 
														            <span ng-show="sortType == 'task_name' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
														            <span ng-show="sortType == 'task_name' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>

																</th>
																<th ng-click="sortType = 'task_startDate'; sortReverse = !sortReverse">
																	Start Date
																	<i ng-hide ="sortType == 'task_startDate' "  class="glyphicon glyphicon-sort pull-right"></i> 
														            <span ng-show="sortType == 'task_startDate' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
														            <span ng-show="sortType == 'task_startDate' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
																</th>

																<th ng-click="sortType = 'task_dueDate'; sortReverse = !sortReverse">
																	Due Date
																	<i ng-hide ="sortType == 'task_dueDate' "  class="glyphicon glyphicon-sort pull-right"></i> 
														            <span ng-show="sortType == 'task_dueDate' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
														            <span ng-show="sortType == 'task_dueDate' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
																</th>
																<th ng-click="sortType = 'last_name'; sortReverse = !sortReverse">
																	By Who
																	<i ng-hide ="sortType == 'last_name' "  class="glyphicon glyphicon-sort pull-right"></i> 
														            <span ng-show="sortType == 'last_name' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
														            <span ng-show="sortType == 'last_name' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
																</th>
																<th ng-click="sortType = 'status_name'; sortReverse = !sortReverse">
																	Status
																	<i ng-hide ="sortType == 'status_name' "  class="glyphicon glyphicon-sort pull-right"></i> 
														            <span ng-show="sortType == 'status_name' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
														            <span ng-show="sortType == 'status_name' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
																</th>
																<th ng-click="sortType = 'priority_name'; sortReverse = !sortReverse">
																	Priority
																	<i ng-hide ="sortType == 'priority_name' "  class="glyphicon glyphicon-sort pull-right"></i> 
														            <span ng-show="sortType == 'priority_name' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
														            <span ng-show="sortType == 'priority_name' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
																</th>
																<th></th>
															</thead>
															<tr ng-repeat="task in milestone.array_task | orderBy: sortType:sortReverse">

																<td ng-if="task.status != '10'"><a href=""  ng-click="open_task_name_link(task.task_id,task.task_name)"><i class="fa fa-comments" title="comments" ng-show="task.comment_counter > 0"></i> </a></td>
																<td ng-if="task.status != '10'"><a href=""  ng-click="update_task(task.task_id,milestone.name)"data-toggle="modal" data-target="#modal1" title="Edit">{{task.task_name}}</a></td>
																<td ng-if="task.status != '10'" >
																	{{task.task_startDate |  date:'MMMM dd, yyyy'}}
																    

																</td>	
																<td ng-if="task.status != '10'" >
																	{{task.task_dueDate |  date:'MMMM dd, yyyy'}}
																    

																</td>
																<td ng-if="task.status != '10'">
																	<ui-select id="redesign" ng-model="task.task_owner_object" append-to-body="true" ng-change="onChangeOwner($select.selected.user_id,task.task_id)">
																	    <ui-select-match >
																	       	{{$select.selected.first_name}} {{$select.selected.last_name}}
																	    </ui-select-match>
																	    <ui-select-choices repeat="item.id as item in user | filter: $select.search">
																	    	{{item.first_name}} {{item.last_name}}
																	    </ui-select-choices>
																  	</ui-select>

																</td>
																<td ng-if="task.status != '10'" style="width:100px;">

																 	<ui-select id="redesign" ng-model="task.status" append-to-body="true" ng-change="onChangeStatus($select.selected.id,task.task_id)">
																	    <ui-select-match >
																	       <span  ng-bind="$select.selected.name"></span>
																	    </ui-select-match>
																	    <ui-select-choices repeat="item.id as item in statusArray | filter: $select.search">
																	        <span ng-bind="item.name"></span>
																	    </ui-select-choices>
																  	</ui-select>

																 
																</td>
																<td ng-if="task.status != '10'" style="width:100px;">
																	<ui-select ng-model="task.priority" id="redesign" append-to-body="true" ng-change="onChangePriority($select.selected.id,task.task_id)">
																	    <ui-select-match   >
																	        <span ng-bind="$select.selected.name" id="redesign"></span>
																	    </ui-select-match>
																	    <ui-select-choices repeat="item.id as item in priorityArray | filter: $select.search ">
																	        <span ng-bind="item.name"></span>
																	    </ui-select-choices>
																  	</ui-select>		
																

																</td>
																<td ng-if="task.status != '10'">
																	<div class="pull-right ">
																		<a href="" class="edit" id="edit_task" ng-click="update_task(task.task_id,milestone.name)"  data-toggle="modal" data-target="#modal1" title="Edit" ><i class="fa fa-pencil-square-o"></i></a>
																		&nbsp;
																		<a href="" class="delete" ng-click="open_delete_task(task.task_id)"   data-toggle="modal" data-target="#modal5" title="Edit" title="Delete"><i class="fa fa-trash-o"></i></a>
																	</div>
																</td>
															</tr>
													</table>
											  	</div>
											  <div id="complete-{{$index}}" class="tab-pane fade">
											    	<table class="table table-hover" id="teams - milestone.counter">
														<thead>
															<th>Task</th>
															<th>Start Date</th>
															<th>Due Date</th>
															<th>By Who</th>
															<th>Status</th>
															<th>Priority</th>
															<th>Date Completed</th>
															<th></th>
														</thead>
														<tr ng-repeat="task in milestone.array_task | orderBy:'-dueDate'">
															<td ng-if="task.status == '10'">{{task.task_name}}</td>
															<td ng-if="task.status == '10'">{{task.task_startDate |  date:'MMMM dd, yyyy'}}</td>
															<td ng-if="task.status == '10'">{{task.task_dueDate |  date:'MMMM dd, yyyy'}}</td>
															<td ng-if="task.status == '10'">{{task.first_name}} {{task.last_name}}</td>
															<td ng-if="task.status == '10'" style="width:150px;">{{task.status_name}}</td>
															<td ng-if="task.status == '10'" style="width:150px;">{{task.priority_name}}</td>
																<td ng-if="task.status == '10'" style="width:150px;">{{task.date_completed  |  date:'MMMM dd, yyyy'}}</td>
															<td ng-if="task.status == '10'" class="pull-right">
																<a href="" id="undo_task" ng-click="undo_task(task.task_id)" title="Reopen" ><i class="fa fa-undo"></i></a>
															</td>

														</tr>
													</table>
											  </div>
											 
											</div>
											
										</div>
								    </div>
							  	</div>
							</div>	

						</div>
					</div>		
			</uib-tab>
			<!-- Plain Tab -->
			<uib-tab index="1">
				<br>
				<uib-tab-heading>
	    			<i class="fa fa-list"></i> Plain
	  			</uib-tab-heading>

				<div class="alert alert-success"  ng-show="update_inline_status">
				    <a href="#" class="close" ng-click="close()" data-dismiss="alert" aria-label="close">&times;</a>
				  	{{inline_status_message}}
				</div>

	  			<table class="table table-hover" id="plain_tab" class="display" style="cursor:pointer">
					<thead>
						<tr>
							<th></th>
							<th ng-click="sortType = 'task_name'; sortReverse = !sortReverse">
						            Task 
						            <i ng-hide ="sortType == 'task_name' "  class="glyphicon glyphicon-sort pull-right"></i> 
						            <span ng-show="sortType == 'task_name' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
						            <span ng-show="sortType == 'task_name' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
						   </th>
						   <th ng-click="sortType = 'task_dueDate'; sortReverse = !sortReverse">
							        Due Date 
						            <i ng-hide ="sortType == 'task_dueDate'"  class="glyphicon glyphicon-sort pull-right"></i> 
							        <span ng-show="sortType == 'task_dueDate' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
						            <span ng-show="sortType == 'task_dueDate' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
							</th>
							<th ng-click="sortType = 'last_name'; sortReverse = !sortReverse">
								    By Who
						            <i ng-hide ="sortType == 'last_name'"  class="glyphicon glyphicon-sort pull-right"></i> 
						            <span ng-show="sortType == 'last_name' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
						            <span ng-show="sortType == 'last_name' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
						   </th>
							<th ng-click="sortType = 'status_name'; sortReverse = !sortReverse">
							        Status
						            <i ng-hide ="sortType == 'status_name'"  class="glyphicon glyphicon-sort pull-right"></i> 
						            <span ng-show="sortType == 'status_name' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
						            <span ng-show="sortType == 'status_name' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
						   </th>
							<th ng-click="sortType = 'priority_name'; sortReverse = !sortReverse">
							        Priority 
	 					            <i ng-hide ="sortType == 'priority_name'"  class="glyphicon glyphicon-sort pull-right"></i> 
						            <span ng-show="sortType == 'priority_name' && !sortReverse" class="fa fa-sort-amount-asc pull-right"></span>
						            <span ng-show="sortType == 'priority_name' && sortReverse" class="fa fa-sort-amount-desc pull-right"></span>
							</th>
							
						   <th></th>
						</tr>
					</thead>

					<tbody>
						<tr ng-repeat="task in plain_tab_tasks | orderBy:sortType:sortReverse ">
							<td ng-if="task.status != '10'"><a href="" ng-click="update_task(task.task_id,'hide_plain')" data-toggle="modal" data-target="#modal1" ><i class="fa fa-comments" title="comments" ng-show="task.comment_counter > 0"></i> </a></td>
							<td ng-if="task.status != '10'"><a href="" ng-click="update_task(task.task_id,'hide_plain')" data-toggle="modal" data-target="#modal1" title="Edit">{{task.task_name}}</a></td>
							<td ng-if="task.status != '10'">{{task.task_dueDate |  date:'MMMM dd, yyyy'}}</td>
							<td ng-if="task.status != '10'">
								<ui-select id="redesign" ng-model="task.task_owner_object" append-to-body="true" ng-change="onChangeOwner($select.selected.user_id,task.task_id)">
								    <ui-select-match >
								       	{{$select.selected.first_name}} {{$select.selected.last_name}}
								    </ui-select-match>
								    <ui-select-choices repeat="item.id as item in user | filter: $select.search">
								    	{{item.first_name}} {{item.last_name}}
								    </ui-select-choices>
							  	</ui-select>

							</td>
							<td ng-if="task.status != '10'">
								<ui-select id="redesign" ng-model="task.status" append-to-body="true" ng-change="onChangeStatus($select.selected.id,task.task_id)">
								    <ui-select-match >
								       <span  ng-bind="$select.selected.name"></span>
								    </ui-select-match>
								    <ui-select-choices repeat="item.id as item in statusArray | filter: $select.search">
								        <span ng-bind="item.name"></span>
								    </ui-select-choices>
							  	</ui-select>
							</td ng-if="task.status != '10'">
							<td ng-if="task.status != '10'">
								<ui-select ng-model="task.priority" id="redesign" append-to-body="true" ng-change="onChangePriority($select.selected.id,task.task_id)">
								    <ui-select-match   >
								        <span ng-bind="$select.selected.name" id="redesign"></span>
								    </ui-select-match>
								    <ui-select-choices repeat="item.id as item in priorityArray | filter: $select.search ">
								        <span ng-bind="item.name"></span>
								    </ui-select-choices>
							  	</ui-select>
							</td>
							
							<td ng-if="task.status != '10'">
								<div class="pull-right">
									
									<a href=""  id="edit_task" ng-click="update_task(task.task_id,milestone.name)"  data-toggle="modal" data-target="#modal1" title="Edit" ><i class="fa fa-pencil-square-o"></i></a>
									&nbsp;

									<a href=""  ng-click="open_delete_task(task.task_id)"  data-toggle="modal" data-target="#modal5" title="Edit"   title="Delete"><i class="fa fa-trash-o"></i></a>
								
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- <uib-pagination total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="itemsPerPage"    max-size="maxSize" boundary-links="true" rotate="false" num-pages="numPages"></uib-pagination> -->
	 
			</uib-tab>
			<!-- Kanban Tab -->
			<uib-tab index="2">
				<br>
				<uib-tab-heading>
	    			<i class="fa fa-th-large"></i> Kanban
	  			</uib-tab-heading>
	  						<div class="container-fluid">
								<ui-select ng-model="kaban_filter" ng-change="onChangeStatus_Kanban($select.selected.id,task.task_id)" style="width:50%">
								    <ui-select-match >
								        <span  ng-bind="$select.selected.name"></span>
								    </ui-select-match>
								    <ui-select-choices repeat="filter.id as filter in kanban_filters | filter: $select.search">
								        <span ng-bind="filter.name"></span>
								    </ui-select-choices>
							  	</ui-select>
							</div>
							<br>
							<!-- by priority -->
							<div ng-show="kaban_filter == 1">
								<ul id="draggablePanelList_priority" class="list-unstyled">
									
									<div class="col-md-3">
										<li class="panel panel-default">
										  <div class="panel-heading">None</div>
										  <div class="panel-body">
										 	<div ng-repeat="none in filter_nones">
										 		<a href=""  ng-click="update_task(none.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{none.task_name}}</a><br>
										 		<i class="fa fa-user" title="{{none.first_name}}  {{none.last_name}}"></i>
										 		<a href=""  ng-click="open_task_name_link(none.task_id,none.task_name)"><i class="fa fa-comments" title="comments" ng-show="none.comment_counter > 0"></i></a>
											 	<hr>
											 </div>
										  </div>
										  <div class="panel-footer">
										  	
										  </div>
										</li>
									</div>

									<div class="col-md-3">
										<li class="panel panel-success">
										  <div class="panel-heading">Low</div>
										  <div class="panel-body">
										  	<div ng-repeat="low in filter_lows">
										  		<a href=""  ng-click="update_task(low.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{low.task_name}}</a><br>
										  		<i class="fa fa-user" title="{{low.first_name}}  {{low.last_name}}"></i>
										 		<a href=""  ng-click="open_task_name_link(low.task_id,low.task_name)"><i class="fa fa-comments" title="comments" ng-show="low.comment_counter > 0"></i></a>
												<hr>
										  	 </div>
										  </div>
										  <div class="panel-footer">
										  	
										  </div>
										</li>
									</div>


									<div class="col-md-3">
										<li class="panel panel-warning">
										  <div class="panel-heading">Medium</div>
										  <div class="panel-body">
										  	<div ng-repeat="medium in filter_mediums">
										  		<a href=""  ng-click="update_task(medium.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{medium.task_name}}</a><br>
										  		<i class="fa fa-user" title="{{medium.first_name}}  {{medium.last_name}}"></i>
										 		<a href=""  ng-click="open_task_name_link(medium.task_id,medium.task_name)"><i class="fa fa-comments" title="comments" ng-show="medium.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  	
										  </div>

										</li>
									</div>


									<div class="col-md-3">
										<li class="panel panel-danger">
										  <div class="panel-heading">High</div>
										  <div class="panel-body">
											<div ng-repeat="high in filter_highs">

										  		<a href="" ng-click="update_task(high.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{high.task_name}}</a><br>
										  		<i class="fa fa-user" title="{{high.first_name}}  {{high.last_name}}"></i>
										 		<a href=""  ng-click="open_task_name_link(high.task_id,high.task_name)"><i class="fa fa-comments" title="comments" ng-show="high.comment_counter > 0"></i></a>
												<hr>
										  		
											</div>
										  </div>
										  <div class="panel-footer">
										  	
										  </div>
										</li>
									</div>
								</ul>
							</div>
							<!-- by milestone -->
							<div ng-show="kaban_filter == 0">
								<ul id="draggablePanelList_milestone" class="list-unstyled">
									<div ng-repeat="milestone in milestones">
										<div class="col-md-4">
											<li class="panel panel-default">
											    <div class="panel-heading">{{milestone.name}}</div>
											    <div class="panel-body">
													<div ng-repeat="task in milestone.array_task" >
														<a href=""  ng-click="update_task(task.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit" >{{task.task_name}}</a><br>
												 		<i class="fa fa-user" title="{{task.first_name}}  {{task.last_name}}"></i>
												 		<a href=""  ng-click="open_task_name_link(task.task_id,task.task_name)"><i class="fa fa-comments" title="comments" ng-show="task.comment_counter > 0"></i></a>
													 	<hr>
													</div>
												</div>
												<div class="panel-footer">
										  			<a href="" ng-click="open(milestone.id,milestone.name)" data-toggle="modal"><i><u>Add Task</u></i></a>
										 		</div>
											</li>
										</div>	
									</div>
								</ul>
							</div>
							<!-- by percentae -->
							<div ng-show="kaban_filter == 2">
								<ul id="draggablePanelList_percentage" class="list-unstyled">
									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
										  	<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="0"
											  	aria-valuemin="0" aria-valuemax="100" style="width:5%"> 
											  	  	0%
											  </div>
											</div>
										  </div>
										  <div class="panel-body">
										  	<div ng-repeat="zero in filter_0">
												<a href="" ng-click="update_task(zero.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit"  >{{zero.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{zero.first_name}}  {{zero.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(zero.task_id,zero.task_name)"><i class="fa fa-comments" title="comments" ng-show="zero.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	

									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
										  	<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="10"
											  	aria-valuemin="0" aria-valuemax="100" style="width:10%"> 
											  	10%
											  </div>
											</div>
										  </div>
										  <div class="panel-body">
									  		<div ng-repeat="one in filter_1">
									  			<a href=""  ng-click="update_task(one.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit" >{{one.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{one.first_name}}  {{one.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(one.task_id,one.task_name)"><i class="fa fa-comments" title="comments" ng-show="one.comment_counter > 0"></i></a>
												<hr>
									  		</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	

									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
											<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="20"
											  	aria-valuemin="0" aria-valuemax="100" style="width:20%"> 
											  	20%
											  </div>
											</div>  	
										  </div>
										  <div class="panel-body">
										  	<div ng-repeat="two in filter_2">
										  		<a href=""  ng-click="update_task(two.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{two.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{two.first_name}}  {{two.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(two.task_id,two.task_name)"><i class="fa fa-comments" title="comments" ng-show="two.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	

									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
											<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="30"
											  	aria-valuemin="0" aria-valuemax="100" style="width:30%"> 
											  	30%
											  </div>
											</div>				  	
										  </div>
										  <div class="panel-body">
										  	<div ng-repeat="three in filter_3">
										  		<a href=""   ng-click="update_task(three.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{three.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{three.first_name}}  {{three.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(three.task_id,three.task_name)"><i class="fa fa-comments" title="comments" ng-show="three.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	

									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
											<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="40"
											  	aria-valuemin="0" aria-valuemax="100" style="width:40%"> 
											  	40%
											  </div>
											</div>
										  </div>
										  <div class="panel-body">
										  	<div ng-repeat="four in filter_4">
										  		<a href=""   ng-click="update_task(four.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit" >{{four.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{four.first_name}}  {{four.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(four.task_id,four.task_name)"><i class="fa fa-comments" title="comments" ng-show="four.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	
					
									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
											<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="50"
											  	aria-valuemin="0" aria-valuemax="100" style="width:50%"> 
											  	50%
											  </div>
											</div>  	
										  </div>
										  <div class="panel-body">
									  		<div ng-repeat="five in filter_5">
										  		<a href=""   ng-click="update_task(five.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{five.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{five.first_name}}  {{five.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(five.task_id,five.task_name)"><i class="fa fa-comments" title="comments" ng-show="five.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	

									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
											<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="60"
											  	aria-valuemin="0" aria-valuemax="100" style="width:60%"> 
											  	60%
											  </div>
											</div>  		
										  </div>
										  <div class="panel-body">
										 	<div ng-repeat="six in filter_6">
										  		<a href=""  ng-click="update_task(six.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{six.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{six.first_name}}  {{six.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(six.task_id,six.task_name)"><i class="fa fa-comments" title="comments" ng-show="six.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	

									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
											<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="70"
											  	aria-valuemin="0" aria-valuemax="100" style="width:70%"> 
											  	70%
											  </div>
											</div>  		
										  </div>
										  <div class="panel-body">
										  	<div ng-repeat="seven in filter_7">
										  		<a href=""  ng-click="update_task(seven.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{seven.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{seven.first_name}}  {{seven.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(seven.task_id,seven.task_name)"><i class="fa fa-comments" title="comments" ng-show="seven.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	
						
									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
											<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="80"
											  	aria-valuemin="0" aria-valuemax="100" style="width:80%"> 
											  	80%
											  </div>
											</div>  		
										  </div>
										  <div class="panel-body">
										  	<div ng-repeat="eight in filter_8">
										  		<a href=""   ng-click="update_task(eight.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{eight.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{eight.first_name}}  {{eight.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(eight.task_id,eight.task_name)"><i class="fa fa-comments" title="comments" ng-show="eight.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	

									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
											<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="90"
											  	aria-valuemin="0" aria-valuemax="100" style="width:90%"> 
											  	90%
											  </div>
											</div>  		
										  </div>
										  <div class="panel-body">
										  	<div ng-repeat="nine in filter_9">
										  		<a href=""   ng-click="update_task(nine.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{nine.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{nine.first_name}}  {{nine.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(nine.task_id,nine.task_name)"><i class="fa fa-comments" title="comments" ng-show="nine.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>	

									<div class="col-md-6">
										<li class="panel panel-default">
										  <div class="panel-heading">
										  	<div class="progress">
											  <div class="progress-bar" role="progressbar" aria-valuenow="100"
											  	aria-valuemin="0" aria-valuemax="100" style="width:100%"> 
											  		100%
											  </div>
											</div>
										  </div>
										  <div class="panel-body">
										  	<div ng-repeat="ten in filter_10">
										  		{<a href=""  ng-click="update_task(ten.task_id,'hide_kanban')" data-toggle="modal" data-target="#modal1" title="Edit">{{ten.task_name}}</a><br>
											 	<i class="fa fa-user" title="{{ten.first_name}}  {{ten.last_name}}"></i>
											 	<a href=""  ng-click="open_task_name_link(ten.task_id,ten.task_name)"><i class="fa fa-comments" title="comments" ng-show="ten.comment_counter > 0"></i></a>
												<hr>
										  	</div>
										  </div>
										  <div class="panel-footer">
										  		
										  </div>
										</li>
									</div>
									
								</ul>
							</div>
			</uib-tab>

		</uib-tabset>
		<!--Create Task  -->
		<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header" style="background-color:#5CB85C;">
		            	<div class="row">
			    		
							<h4 class="modal-title" id="myModalLabel" style="color:white;">
								<div class="col-md-1"><i class="fa fa-plus"></i></div>
								<div class="col-md-6">
									<input class="form-control" name="meeting_tags" placeholder="Task Name" ng-model="name_task" />
										<p class="alert alert-danger" ng-show="isopen_task_name">
											{{_error_task_name}}
										</p>

								</div>
							</h4>
							<div class="col-md-2 pull-right">    
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>		
		       			</div>	
						
							
				    </div>
			        <div class="modal-body">

			          	<div class="form-group" hidden>
			              <label> Milestone Id <small>(required)</small></label>  
			              <input class="form-control" placeholder="Milestone" name="meeting_title" ng-model="milestone_id" readonly/>
						</div>

			          	
			          	<div class="form-group" hidden>
			              <label>Id <small>(required)</small></label>  
			              <input class="form-control" placeholder="Milestone" name="meeting_title" ng-model="milestone_task_id" readonly/>
						</div>

			          	<div class="row">
			          		<div class="col-md-6">
					          	
								<div class="form-group" >
					             	<label>Milestone <small>(required)</small></label>  
					             	<ui-select ng-model="milestone_task" ng-disabled="disabled" ng-change="onChangeMilestone($select.selected)">
								    	<ui-select-match>{{$select.selected.name}} </ui-select-match>
									    <ui-select-choices repeat="person in all_milestone | propsFilter: {name: $select.search}">
									    	<div ng-bind-html="person.name | highlight: $select.search"></div>
									    </ui-select-choices>
								  	</ui-select>
								 
								</div>


					            <div class="form-group" >
					              	<label>Owner</label>  <br/>
					            	<ui-select ng-model="_owner_task.owner_task" ng-disabled="disabled">
								    	<ui-select-match>{{$select.selected.first_name}}  {{$select.selected.last_name}}</ui-select-match>
									    <ui-select-choices repeat="person in user | propsFilter: {first_name: $select.search}">
									    	<div ng-bind-html="person.first_name +' '+ person.last_name | highlight: $select.search"></div>
									    </ui-select-choices>
								  	</ui-select>
								 
								</div>

								<p class="alert alert-danger" ng-show="isopen_owner_task">
									{{_error_owner}}
								</p>

								<div class="form-group who-else">
					             	<label>Who else</label><br/>
									<ui-select multiple ng-model="_multipleUser.user_task" ng-disabled="disabled">
										<ui-select-match>{{$item.first_name}} {{$item.last_name}}</ui-select-match>
											<ui-select-choices repeat="person in user | propsFilter: {first_name: $select.search}">
												<div ng-bind-html="person.first_name +' '+ person.last_name | highlight: $select.search"></div>
											</ui-select-choices>
									</ui-select>
								</div>

								<p class="alert alert-danger" ng-show="isopen_who_else">
									{{_error_who_else}}
								</p>


								
							

			          			<div class="form-group">
									<label for="description" class="control-label">Description</label>
									<textarea id="description" class="form-control" name="description" ng-model="description_task"></textarea>
								</div>
	
			          		</div>

			          		<div class="col-md-6">



							    <div class="form-group">
								    <div class="row">
								        <div class='col-sm-12'>
								        		<label>Start Date</label>  <br/>
								            <input type="text" class="form-control" id="datetimepicker_start_date" />
								        </div>
								        <script type="text/javascript">
								            $(function () {
								                $('#datetimepicker_start_date').datetimepicker({
								          				format: "DD/MM/YYYY"
								                });
								            });
								        </script>
								    </div>
								</div>


							    <div class="form-group">
								    <div class="row">
								        <div class='col-sm-12'>
								        		<label>Due Date</label>  <br/>
								            <input type="text" class="form-control" id="datetimepicker4" />
								        </div>
								        <script type="text/javascript">
								            $(function () {
								                $('#datetimepicker4').datetimepicker({
								          				format: "DD/MM/YYYY"
								                });
								            });
								        </script>
								    </div>
								</div>
								<p class="alert alert-danger" ng-show="isopen_due_date">
									{{_error_due_date}}
								</p>
				

								<div class="form-group">
									<label>Priority</label>  <br/>
										<ui-select ng-model="_priority.priority_task">
										    <ui-select-match >
										        <span ng-bind="$select.selected.name"></span>
										    </ui-select-match>
										    <ui-select-choices repeat="item.id as item in priorityArray | filter: $select.search ">
										        <span ng-bind="item.name"></span>
										    </ui-select-choices>
									  	</ui-select>				  
								</div>
								<p class="alert alert-danger" ng-show="isopen_priority">
									{{_error_priority}}
								</p>

							
									<div class="form-group">
										<label>Status</label>  <br/>
											<ui-select ng-model="_status.status_task">
											    <ui-select-match>
											        <span ng-bind="$select.selected.name"></span>
											    </ui-select-match>
											    <ui-select-choices repeat="item.id as item in statusArray | filter: $select.search">
											        <span ng-bind="item.name"></span>
											    </ui-select-choices>
										  	</ui-select>
									</div>
								
								 
			          		</div>
			          	</div>

			          	<p class="alert alert-success"  ng-show="save_task">
							{{message_save_owner}}
						</p>
						
						<div ng-show="show_comment">
							<div class="panel-group" id="scheduler" role="tablist" aria-multiselectable="true">
								<div class="panel panel-default">
								    <div class="panel-heading" role="tab" id="headingOne">
								      <h4 class="panel-title">
								        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								          <i class="accordion_icon fa fa-plus"></i> Comment</a>
								      </h4>
								    </div>
								    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
									    <div class="panel-body">
									    	<div class="form-group">
												<textarea id="task_comment" class="form-control" placeholder="Write Comment...." name="task_comment"  ng-model="task_comment.comment"></textarea>
											</div>
											<div class="form-group">
						   			 			<button type="button" id="task_comment" class="btn btn-default" ng-click="save_task_comment(task_id)">Post comment </button>
											</div>
											<br><br>
											<div ng-show="task_counter > '0'">
												<div class="container" style="overflow:scroll; height:250px; width:auto;">	
													<div ng-repeat="comment in task_comments  | orderBy:'-date_post'">
														<div class="row">
															<div class="col-md-5" style="margin-left:-10px;">
																<div class="row">
																	<div class="col-md-4" style="margin-right:-25px!important;">
																		<img class="img-responsive" ng-src="<?php echo base_url();?>uploads/5/{{comment.profile_pic}}" alt="Tim Pointon" width="40">
																	</div>
																	<div class="container-fluid col-md-8" >
																		<span><a href=""> {{comment.first_name}} {{comment.last_name}}</a></span><br>
																		<span style="font-size:12px;"> on {{comment.date_post |  date:'medium'}}</span>
																	</div>
																	
																</div>
															</div>
															<div class="col-md-7" style="margin-left:-35px;margin-right:-35px;">
																<div class="row">
																	<div class="col-md-10" id="contenteditable" contenteditable="true" ng-click="show=true;" click-off="show=false;">
														    			<textarea  class="form-control" ng-show="show" ng-model="comment.comment"></textarea>										    		
														    			<span ng-bind="comment.comment" ng-show="!show"></span>
														    		</div>
														    		<div class="col-md-2" ng-show="show">
																		<div class="pull-right">
																	   	<button type="button" id="task_comment" ng-click="save_update_comment(comment.comment,comment.task_progress_id,task_id)" class="btn btn-primary btn-xs">Save </button>
																   		<button type="button" id="task_comment" class="btn btn-default btn-xs">Cancel </button>
																   		</div>
																	</div>
																</div>
																
															</div>
															<div class="col-md-12" ng-show="'<?php echo  $this->session->userdata('user_id'); ?>' == comment.user_id" >
																<span class="pull-right" ng-show="!show">
																	<a href="" ><i class="fa fa-pencil-square-o"></i></a>
																	<a href="" ng-click="delete_comment(comment.task_progress_id,task_id)"><i class="fa fa-trash-o"></i></a>
																</span>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div ng-show="task_counter == '0'">
												<span>No Comment available.</span>
											</div>
									    	<br>
											<div >
												<p class="alert alert-success"  ng-show="save_task_comments">
													{{message_save_task_comment}}
												</p>
												<p class="alert alert-danger"  ng-show="comment_field">
													{{comment_error_message}}
												</p>
												<p class="alert alert-success"  ng-show="delete_data_comment">
												    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
												  	{{delete_message}}
												</p>
											</div>


									    </div>
								    </div>
								</div>	
							</div>
						</div>			 

			            <div class="modal-footer">
							<button type="button" id="create-task" class="btn btn-primary" ng-click="savetask()">Save </button>
							<button type="button" id="create-team" class="btn btn-default" ng-click="closertask()">Cancel</button>
			        	</div>

		        	</div>
		    	</div>
			</div>
		</div>
		<!-- Create Milestone  -->
		<div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header"   style="color:white; background-color:#f0ad4e;">
						<div class="row">
							<div class="col-md-1">
								<i class="fa fa-plus"></i>
							</div>
							<div class="col-md-6">
								  <input class="form-control" name="meeting_tags"  placeholder="Milestone Name " ng-model="name" />
								
								<p class="alert alert-danger" ng-show="isopen_name">
								  {{_error_name}}
								</p>
							</div>
							<div class="col-md-2 pull-right">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>
						</div>
					</div>
					<div class="modal-body">

						<div class="form-group" hidden >
			              <label> Milestone Id <small>(required)</small></label>  
			              <input class="form-control" placeholder="Milestone" name="meeting_title" ng-model="_milestone_id" readonly/>
						</div>

						<div class="row">
							<div class="col-md-6">
							
								<div class="form-group">
								  <label>Owner <small>(required)</small></label>  
								  <ui-select ng-model="_owner.owner" ng-disabled="disabled">
								    <ui-select-match>{{$select.selected.first_name}}  {{$select.selected.last_name}}</ui-select-match>
								    <ui-select-choices repeat="person in user | propsFilter: {first_name: $select.search}">
								      <div ng-bind-html="person.first_name +' '+ person.last_name | highlight: $select.search"></div>
								      
								    </ui-select-choices>
								  </ui-select>
								</div>
								<p class="alert alert-danger" ng-show="isopen_owner">
								  {{_error_owner}}
								</p>

								<div class="form-group">
									<label for="description" class="control-label">Description</label>
									<textarea id="description" class="form-control" name="description"  ng-model="description" ></textarea>
								</div>

								<div class="form-group">
									<label>Status</label>  <br/>
										<ui-select ng-model="milestone_status.id">
										    <ui-select-match>
										        <span ng-bind="$select.selected.name"></span>
										    </ui-select-match>
										    <ui-select-choices repeat="item.id as item in statusArray | filter: $select.search">
										        <span ng-bind="item.name"></span>
										    </ui-select-choices>
									  	</ui-select>
								</div>
						
								 
								
						 

							</div>
							
							<div class="col-md-6">

								<div class="form-group">
								    <div class="row">
								        <div class='col-sm-12'>
								        		<label>Start Date</label>  <br/>
								            <input type='text' class="form-control" id='milestone_start_date'/>
								        </div>
								        <script type="text/javascript">
								            $(function () {
								                $('#milestone_start_date').datetimepicker({
								                	format: "DD/MM/YYYY"
								                });
								            });
								        </script>
								    </div>
								</div>	

								<div class="form-group">
								    <div class="row">
								        <div class='col-sm-12'>
								        		<label>Due Date</label>  <br/>
								            <input type='text' class="form-control" id='datetimepicker5' />
								        </div>
								        <script type="text/javascript">
								            $(function () {
								                $('#datetimepicker5').datetimepicker({
								                	format: "DD/MM/YYYY"
								                });
								            });
								        </script>
								    </div>
								</div>
								<p class="alert alert-danger" ng-show="isopen_duedate">
								  {{_error_dueDate}}
								</p>	

							</div>
						</div>
						
						<p class="alert alert-success"  ng-show="save_milestone">
							{{message_save_milestone}}
						</p>
					</div>
					<div class="modal-footer">
					    <button type="button" id="create-milestone" class="btn btn-primary" ng-click="savemilstone()">Save </button>
						<button type="button" id="create-team" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>

				</div>
			</div>
		</div>		
		<!-- Task Name-->
		<div class="modal fade" id="modal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
			<div class="modal-dialog comment_modal">
				<div class="modal-content" >

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">{{task_name}}<h4>
					</div>

					<div class="modal-body">

						<div id="exTab1">	
			
					    	<div class="form-group">
								<textarea id="task_comment" class="form-control" placeholder="Write Comment...." name="task_comment"  ng-model="task_comment.comment"></textarea>
							</div>
							<div class="form-group">
		   			 			<button type="button" id="task_comment" class="btn btn-default" ng-click="save_task_comment(task_id)">Post comment </button>
							</div>
							<br><br>
							<div ng-show="task_counter > '0'">
								<div class="container" style="overflow:scroll; height:250px; width:auto;">	
									<div ng-repeat="comment in task_comments  | orderBy:'-date_post'">
										<div class="row">
											<div class="col-md-5" style="margin-left:-10px;">
												<div class="row">
													<div class="col-md-4" style="margin-right:-25px!important;">
														<img class="img-responsive" ng-src="<?php echo base_url();?>uploads/5/{{comment.profile_pic}}" alt="Tim Pointon" width="40">
													</div>
													<div class="container-fluid col-md-8" >
														<span><a href=""> {{comment.first_name}} {{comment.last_name}}</a></span><br>
														<span style="font-size:12px;"> on {{comment.date_post |  date:'medium'}}</span>
													</div>
													
												</div>
											</div>
											<div class="col-md-7" style="margin-left:-35px;margin-right:-35px;">
												<div class="row">
													<div class="col-md-10" id="contenteditable" contenteditable="true" ng-click="show=true;" click-off="show=false;">
										    			<textarea  class="form-control" ng-show="show" ng-model="comment.comment"></textarea>										    		
										    			<span ng-bind="comment.comment" ng-show="!show"></span>
										    		</div>
										    		<div class="col-md-2" ng-show="show">
														<div class="pull-right">
													   	<button type="button" id="task_comment" ng-click="save_update_comment(comment.comment,comment.task_progress_id,task_id)" class="btn btn-primary btn-xs">Save </button>
												   		<button type="button" id="task_comment" class="btn btn-default btn-xs">Cancel </button>
												   		</div>
													</div>
												</div>
												
											</div>
											<div class="col-md-12" ng-show="'<?php echo  $this->session->userdata('user_id'); ?>' == comment.user_id" >
												<span class="pull-right" ng-show="!show">
													<a href="" ><i class="fa fa-pencil-square-o"></i></a>
													<a href="" ng-click="delete_comment(comment.task_progress_id,task_id)"><i class="fa fa-trash-o"></i></a>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div ng-show="task_counter == '0'">
								<span>No Comment available.</span>
							</div>
					    	<br>
							<div >
								<p class="alert alert-success"  ng-show="save_task_comments">
									{{message_save_task_comment}}
								</p>
								<p class="alert alert-danger"  ng-show="comment_field">
									{{comment_error_message}}
								</p>
								<p class="alert alert-success"  ng-show="delete_data_comment">
								    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								  	{{delete_message}}
								</p>
							</div>
						</div>
			



									  
			
					</div>
					<div class="modal-footer">
					</div>



				</div>
			</div>
		</div>

		<!-- Delete Modal Milestone -->
		<div class="modal fade" id="modal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
			<div class="modal-dialog comment_modal">
				<div class="modal-content" >


					<div class="modal-header ">
						<strong>Delete</strong>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
					</div>

					<div class="modal-body">
						<p>Are you sure to delete this?</p>
						
						<div class="alert alert-success"  ng-show="delete_data">
						    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						  	{{delete_message}}
						</div>	
			
					</div>
					<div class="modal-footer">
						<button type="button" id="create-milestone" class="btn btn-danger" ng-click="delete_milestone(temp_id)">Delete</button>
						<button type="button" id="create-team" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>

				</div>
			</div>
		</div>
		
		<div class="modal fade" id="modal5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
			<div class="modal-dialog comment_modal">
				<div class="modal-content" >


					<div class="modal-header ">
						<strong>Delete</strong>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					
					</div>

					<div class="modal-body">
						<p>Are you sure to delete this?</p>
						
						<div class="alert alert-success"  ng-show="delete_data">
						    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						  	{{delete_message}}
						</div>	
			
					</div>
					<div class="modal-footer">
						<button type="button" id="create-milestone" class="btn btn-danger" ng-click="delete_task(temp_id_task)">Delete</button>
						<button type="button" id="create-team" class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>

				</div>
			</div>
		</div>
		
			
</div>
</div> <!-- .bg-white-wrapper -->

<?php $this->load->view('includes/footer'); ?>

<script  type="text/javascript" src="<?php echo base_url(); ?>asset/schedulelist.js"></script>

<script type="text/javascript">

 jQuery(function($) {
        var panelList_priority = $('#draggablePanelList_priority');

        panelList_priority.sortable({
            handle: '.panel-heading', 
            update: function() {
                $('.panel', panelList_priority).each(function(index, elem) {
                     var $listItem = $(elem),
                         newIndex = $listItem.index();
                });
            }
        });
    });

  jQuery(function($) {
        var panelList_milestone = $('#draggablePanelList_milestone');

        panelList_milestone.sortable({
            handle: '.panel-heading', 
            update: function() {
                $('.panel', panelList_milestone).each(function(index, elem) {
                     var $listItem = $(elem),
                         newIndex = $listItem.index();
                });
            }
        });
    });

   jQuery(function($) {
        var panelList_percentage = $('#draggablePanelList_percentage');

        panelList_percentage.sortable({
            handle: '.panel-heading', 
            update: function() {
                $('.panel', panelList_percentage).each(function(index, elem) {
                     var $listItem = $(elem),
                         newIndex = $listItem.index();
                });
            }
        });
    });


 
</script>



