<div>
	<div class="panel-group" id="scheduler" role="tablist" aria-multiselectable="true">
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingOne">
			  <h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne_task_comment" aria-expanded="true" aria-controls="collapseOne">
				  <i class="accordion_icon fa fa-plus"></i> Comment</a>
			  </h4>
			</div>
			<div id="collapseOne_task_comment" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
				<div class="panel-body">
					<div class="form-group">
						<textarea id="task_comment" class="form-control" placeholder="Write Comment...." name="task_comment"  ng-model="task_comment.comment"></textarea>
					</div>
					<div class="form-group">
						<button type="button" id="task_comment" class="btn btn-default" ng-click="save_task_comment(task_id)">Post comment </button>
					</div>
					<br><br>
					
					<div ng-show="task_counter > '0'">
						<div class="container" style="overflow-x:auto; height:250px; width:auto;">	
							<div ng-repeat="comment in task_comments  | orderBy:'-date_post'">
								<div class="row">
									<div class="col-md-5" style="margin-left:-10px;">
										<div class="row">
											<div class="col-md-4" style="margin-right:-25px!important;">
												<img class="img-responsive" ng-src="{{comment_profile_pic(comment.profile_pic, comment.user_id)}}" alt="Tim Pointon" width="40">
											</div>
											<div class="container-fluid col-md-8" >
												<span><a href=""> {{comment.first_name}} {{comment.last_name}}</a></span><br>
												<span style="font-size:12px;"> on {{comment.date_post |  date:'medium'}}</span>
											</div>
										</div>
									</div>
									<div class="col-md-7" style="margin-left:-25px;">
										<div class="row"  hide-comment-edit-textarea="show_task_comment_container=false">
											<div class="col-md-12 edit_comment_btn task_comment_container" ng-click="show_task_comment_container=true;" ng-show="!show_task_comment_container" data-toggle="tooltip"  title="Click to edit comment" ng-init="tooltip_comment('edit')" >
													<div class="task_comment_arrow-left"></div>
													<span ng-bind="comment.comment" ng-show="!show_task_comment_container"></span>
											</div>
											
											<div class="col-md-12 edit_task_comment_container" ng-show="show_task_comment_container">
												<textarea  class="form-control" ng-model="comment.comment"></textarea>	
												<div class="pull-right">
												<button type="button" id="task_comment" ng-click="save_update_comment(comment.comment,comment.task_progress_id,task_id)" class="btn btn-primary btn-xs">Save </button>
												<button type="button" class="edit_task_comment btn btn-default btn-xs">Cancel </button>
												</div>
											</div>
										</div>
										
									</div>
									<div class="col-md-12" ng-show="'<?php echo  $this->session->userdata('user_id'); ?>' == comment.user_id" >
										<span class="pull-right" ng-show="!show">
											<a href="" class="delete_comment_btn" data-toggle="tooltip"  title="delete" ng-init="tooltip_comment('delete')" ng-click="delete_comment(comment.task_progress_id,task_id)">
												<i class="fa fa-trash-o"></i>
											</a>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div ng-show="task_counter == '0'">
						<span>No Comment available.</span>
					</div>
					


				</div>
			</div>
		</div>	
	</div>
</div>