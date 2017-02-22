<!-- Task Comment Standalone Modal-->
<div class="modal fade" id="task_comment_standalone_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
	<div class="modal-dialog comment_modal">
		<div class="modal-content" >
			<!-- modal header -->
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">{{comment_task_name}}<h4>
			</div>
			
			<!-- modal body -->
			<div class="modal-body">
				<div id="exTab1">	
					<div class="form-group">
						<textarea id="task_comment" class="form-control" placeholder="Write Comment...." name="task_comment"  ng-model="task_comment.comment"></textarea>
					</div>
					<div class="form-group">
						<button type="button" id="task_comment" class="btn btn-default" ng-click="save_task_comment_standalone(comment_task_id)">Post comment </button>
					</div>
					<br><br>
					
					<div ng-show="task_counter > '0'">
						<div class="container" style="overflow-x:auto; height:250px; width:auto;">	
							<!-- Task Comments -->
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
									<!--
									<div class="col-md-7" style="margin-left:-35px;margin-right:-35px;">
										<div class="row">
											<div class="col-md-10 edit_comment_btn" style='cursor:pointer;' ng-init="tooltip_commen('edit')" data-toggle="tooltip" title="Click to edit" id="contenteditable" contenteditable="true" ng-click="show=true;" click-off="show=false;">
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
									</div>-->
									<div class="col-md-7" style="margin-left:-25px;">
										<div class="row" ng-click="show_task_comment_container=true;"  hide-comment-edit-textarea="show_task_comment_container=false">
											<div class="col-md-12 edit_comment_btn task_comment_container" ng-show="!show_task_comment_container" data-toggle="tooltip"  title="Click to edit comment" ng-init="tooltip_comment('edit')" >
													<div class="task_comment_arrow-left"></div>
													<span ng-bind="comment.comment" ng-show="!show_task_comment_container"></span>
											</div>
											
											<div class="col-md-12 edit_task_comment_container" ng-show="show_task_comment_container">
												<textarea  class="form-control" ng-model="comment.comment"></textarea>	
												<div class="pull-right">
												<button type="button" id="task_comment" ng-click="save_update_comment(comment.comment,comment.task_progress_id,comment_task_id)" class="btn btn-primary btn-xs">Save </button>
												<button type="button" id="task_comment" class="btn btn-default btn-xs">Cancel </button>
												</div>
											</div>
										</div>
										
									</div>
									<div class="col-md-12" ng-show="'<?php echo  $this->session->userdata('user_id'); ?>' == comment.user_id" >
										<span class="pull-right" ng-show="!show">
											<a href="" class="delete_comment_btn" data-toggle="tooltip"  title="delete" ng-init="tooltip_commen('delete')" ng-click="delete_comment(comment.task_progress_id,task_id)">
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
			
			<!-- modal footer -->
			<div class="modal-footer"></div>
		
		</div>
	</div>
</div>