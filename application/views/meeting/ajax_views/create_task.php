<link rel="stylesheet" href="<?php echo base_url() ?>public/chosen/chosen.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.css" />
<link rel="stylesheet" href="<?php echo base_url() ?>public/jquery-confirm/dist/jquery-confirm.min.css" />


<script src="<?php echo base_url(); ?>public/jquery-1.10.1.min.js"></script>
<script src="<?php echo base_url(); ?>public/bootstrap334/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>public/jqueryui/jquery-ui.min.js"></script>
<script src="<?php echo base_url() ?>public/chosen/chosen.jquery.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo base_url() ?>public/moment.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>public/bootstrap-tags/bootstrap-tagsinput.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/jquery-confirm/dist/jquery-confirm.min.js"></script>

<style type="text/css">
	.chosen-single
	{
		height:30px !important;
		font-size:14px;
	}
	.modal-header
	{
		background-color:#5CB85C !important;
	}
	.edit-task-header
	{
		color:#fff !important;
	}
	.search-choice
	{
		background:#cfe6e8 !important;
		border:0px !important;
		font-size:12px !important;
	}
</style>


<div ng-controller="meetingTaskCtrl" ng-init="get_userId('<?php echo $this->session->userdata('user_id'); ?>','<?php echo $this->session->userdata('organ_id'); ?>')">

	<div class="row">
		<div class="col-sm-12">

		<?php if(empty($details)) :?>
		
			<?php echo form_open("", array("id"=>"create_meeting_task_form")) ?>

				<input type="hidden" name="ntd_id" value="<?php echo encrypt($ntd_id) ?>" />

				<div class="col-sm-6">
					<div class="form-group">
						<label>Task Name</label>
						<input class="form-control" type="text" name="name" value="<?php echo $text ?>" />
					</div>

					<div class="form-group">
						<label>Owner</label>
						<select class="form-control chosen-select" name="owner">
							<?php if(!empty($users)) :?>
								<?php foreach($users as $user) :?>
									<option value="<?php echo $user->user_id ?>"><?php echo user_info("first_name", $user->user_id)." ".user_info("last_name", $user->user_id) ?></option>
								<?php endforeach ;?>
							<?php endif;?>
						</select>
					</div>

					<div class="form-group">
						<label>Who else</label>
						<select class="form-control chosen-select" name="participant[]" multiple="multiple">
							<?php if(!empty($users)) :?>
								<?php foreach($users as $user) :?>
									<option value="<?php echo $user->user_id ?>"><?php echo user_info("first_name", $user->user_id)." ".user_info("last_name", $user->user_id) ?></option>
								<?php endforeach ;?>
							<?php endif;?>
						</select>
					</div>

					<div class="form-group">
						<label>Description</label>
						<textarea class="form-control" name="description"></textarea>
					</div>
				</div>


				<div class="col-sm-6">
					<div class="form-group">
						<label>Start Date</label>
						<input class="form-control" id="task_start_date" type="text" name="start_date" />
					</div>

					<div class="form-group">
						<label>Due Date</label>
						<input class="form-control" id="task_due_date" type="text" name="date" />
					</div>

					<div class="form-group">
						<label>Priority</label>
						<select class="form-control" name="priority">
							<option value="1">None</option>
							<option value="2">Low</option>
							<option value="3">Medium</option>
							<option value="4">High</option>
						</select>
					</div>

					<div class="form-group">
						<label>Status</label>
						<select class="form-control" name="status">
							<option value="0">0%</option>
							<option value="1">10%</option>
							<option value="2">20%</option>
							<option value="3">30%</option>
							<option value="4">40%</option>
							<option value="5">50%</option>
							<option value="6">60%</option>
							<option value="7">70%</option>
							<option value="8">80%</option>
							<option value="9">90%</option>
							<option value="10">100%</option>
						</select>
					</div>

					<div class="form-group pull-right">
						<button type="button" class="btn btn-primary btn-save-meeting-task">Save</button>
					</div>
				</div>

			<?php echo form_close() ?>

		<?php else :?>

			<?php 
				$detail = $details[0];

				if(!empty($detail['participant_id']))
			    {
			      $participants = unserialize($detail['participant_id']);
			    }
			?>

			<?php echo form_open("", array("id"=>"update_meeting_task_form")) ?>

				<input type="hidden" name="ntd_id" value="<?php echo encrypt($ntd_id) ?>" />
				<input type="hidden" name="task_id" value="<?php echo encrypt($detail['task_id']) ?>" />

				<div class="col-sm-6">
					<div class="form-group">
						<label>Task Name</label>
						<input class="form-control" type="text" name="name" value="<?php echo $detail['task_name'] ?>" />
					</div>

					<div class="form-group">
						<label>Owner</label>
						<select class="form-control chosen-select" name="owner">
							<?php if(!empty($users)) :?>
								<?php foreach($users as $user) :?>
									<option value="<?php echo $user->user_id ?>" <?php if($detail['owner_id'] == $user->user_id) echo "selected" ?> ><?php echo user_info("first_name", $user->user_id)." ".user_info("last_name", $user->user_id) ?></option>
								<?php endforeach ;?>
							<?php endif;?>
						</select>
					</div>

					<div class="form-group">
						<label>Who else</label>
						<select class="form-control chosen-select" name="participant[]" multiple="multiple">
							<?php if(!empty($participants)) :?>
			                    <?php foreach($participants as $par) :?>
			                        <option value="<?php echo $par ?>" selected><?php echo user_info("first_name", $par)." ".user_info("last_name", $par) ?></option>
			                    <?php endforeach ;?>

			                <?php else:?>
			                	<?php if(!empty($users)) :?>
									<?php foreach($users as $user) :?>
										<option value="<?php echo $user->user_id ?>"><?php echo user_info("first_name", $user->user_id)." ".user_info("last_name", $user->user_id) ?></option>
									<?php endforeach ;?>
								<?php endif;?>

		                    <?php endif;?>
						</select>
					</div>

					<div class="form-group">
						<label>Description</label>
						<textarea class="form-control" name="description"><?php echo $detail['task_description'] ?></textarea>
					</div>
				</div>


				<div class="col-sm-6">
					<div class="form-group">
						<label>Start Date</label>
						<?php 
							$formatted_date = "";
							if($detail['task_startDate'] != "0000-00-00")
							{
								$start_date = $detail['task_startDate'];
						        $datetime_string = $start_date;
						        $date = strtok($datetime_string, " ");
						        $format = str_replace('/', '-', $date);
						        $formatted_date = date('d-m-Y', strtotime($format));
							}
							else
							{
								$formatted_date = null;
							}
						?>
						<input class="form-control" id="task_start_date" type="text" name="start_date" value="<?php echo $formatted_date ?>" />
					</div>

					<div class="form-group">
						<label>Due Date</label>
						<?php 
							$formatted_due_date = "";
							if($detail['task_startDate'] != "0000-00-00")
							{
								$due_date = $detail['task_dueDate'];
						        $due_datetime_string = $due_date;
						        $date_due = strtok($due_datetime_string, " ");
						        $due_format = str_replace('/', '-', $date_due);
						        $formatted_due_date = date('d-m-Y', strtotime($due_format));
							}
							else
							{
								$formatted_due_date = null;
							}
						?>
						<input class="form-control" id="task_due_date" type="text" name="date" value="<?php echo $formatted_due_date ?>" />
					</div>

					<div class="form-group">
						<label>Priority</label>
						<select class="form-control" name="priority">
							<option value="1" <?php if($detail['priority'] == 1) echo "selected" ?> >None</option>
							<option value="2" <?php if($detail['priority'] == 2) echo "selected" ?> >Low</option>
							<option value="3" <?php if($detail['priority'] == 3) echo "selected" ?> >Medium</option>
							<option value="4" <?php if($detail['priority'] == 4) echo "selected" ?> >High</option>
						</select>
					</div>

					<div class="form-group">
						<label>Status</label>
						<select class="form-control" name="status">
							<option value="0" <?php if($detail['status'] == 0) echo "selected" ?> >0%</option>
							<option value="1" <?php if($detail['status'] == 1) echo "selected" ?> >10%</option>
							<option value="2" <?php if($detail['status'] == 2) echo "selected" ?> >20%</option>
							<option value="3" <?php if($detail['status'] == 3) echo "selected" ?> >30%</option>
							<option value="4" <?php if($detail['status'] == 4) echo "selected" ?> >40%</option>
							<option value="5" <?php if($detail['status'] == 5) echo "selected" ?> >50%</option>
							<option value="6" <?php if($detail['status'] == 6) echo "selected" ?> >60%</option>
							<option value="7" <?php if($detail['status'] == 7) echo "selected" ?> >70%</option>
							<option value="8" <?php if($detail['status'] == 8) echo "selected" ?> >80%</option>
							<option value="9" <?php if($detail['status'] == 9) echo "selected" ?> >90%</option>
							<option value="10" <?php if($detail['status'] == 10) echo "selected" ?> >100%</option>
						</select>
					</div>

					<div class="form-group pull-right">
						<button type="button" class="btn btn-primary btn-update-meeting-task">Save</button>
					</div>
				</div>

			<?php echo form_close() ?>

		
		<?php endif;?>
		
		</div>
	</div>


	<?php if(!empty($details)) :?>
		<!-- comments section -->
		<div class="row">
			<div class="col-sm-12">

			<div >
				<div class="panel-group" id="scheduler" role="tablist" aria-multiselectable="true">
					<div class="panel panel-default">  
				        <div class="panel-heading">
				          <h4>
				          	<span class="">
				                <a href="#" class="panel-minimize" id="" style="color:#fff"><i class="fa fa-plus"></i></a>
				            </span>
				            <?php 
				            	if(!empty($comments))
				            	{
				            		$count = count($comments);
				            	}
				            ?>
				            Comments (<span id="comment-count"><?php echo (!empty($count)) ? $count : 0 ?></span>)
				          </h4>
				        </div>

				        <div class="panel-body" style="display:none">
				        	<?php 

				        		if(!empty($details))
				        		{
									$detail = $details[0];
								}
							?>

					        <?php echo form_open("", array("id"=>"add_task_comment")) ?>
					        	<input type="hidden" name="task_id" value="<?php echo encrypt($detail['task_id']) ?>" />

					        	<div class="form-group">
									<textarea id="" class="form-control" placeholder="Write Comment" name="comment"></textarea>
								</div>
								<div class="form-group">
						 			<button type="button" id="" class="btn btn-default btn-save-task-comment">Post comment </button>
								</div>
							<?php echo form_close() ;?>

							<br><br>

							<div>
								<div class="container" style="overflow-y:scroll; height:250px; width:auto;">	
									
									<div id="comments">
										<div id="comments-load">
											<?php if(!empty($comments)) :?>
												<?php foreach($comments as $comment) :?>
													<div class="row" style="padding:10px">
														<div class="col-md-5" style="margin-left:-10px;">
															<div class="row">
																<div class="col-md-4">
																	<img class="img-responsive" src="<?php echo base_url();?>uploads/<?php echo $comment->user_id."/".$comment->profile_pic ?>" width="50">
																</div>
																<div class="container-fluid col-md-8" >
																	<span><a href=""> <?php echo $comment->first_name." ".$comment->last_name ?> </a></span><br>
																	<?php 
																		$post_date = gd_date($comment->date_post);
																		$newDateTime = date('d/m/Y h:i:s A', strtotime($post_date));
																	?>
																	<span style="font-size:12px;"> on <?php echo $newDateTime ?> </span>
																</div>
																
															</div>
														</div>
														<div class="col-md-6" style="">
															
															<?php echo form_open("", array("id"=>"update_task_comment".$comment->task_progress_id)) ?>
																<input type="hidden" name="task_progress_id" value="<?php echo encrypt($comment->task_progress_id) ?>">
																<div class="row">
																	<div class="col-md-9">
														    			<textarea class="form-control" name="update_comment"><?php echo $comment->comment ?></textarea>
														    		</div>
														    		<div class="col-md-2">
																		<div class="pull-right">
																	   	<button type="button" class="btn btn-primary btn-xs btn-save-updated-comment" data-task-progress-id="<?php echo $comment->task_progress_id ?>">Save</button>
																   		</div>
																	</div>
																</div>
															<?php echo form_close() ?>
															
														</div>
														<div class="col-md-1">
															<span class="pull-right">
																<a href="javascript:void(0)" class="delete_comment" data-task-id="<?php echo $comment->task_progress_id ?>"><i class="fa fa-trash-o"></i></a>
															</span>
														</div>
													</div>
												<?php endforeach;?>
											<?php endif;?>
										</div>
									</div>

								</div>
							</div>

				        </div>
				    </div>
				</div>
			</div>
			</div>
		</div>
	<?php endif;?>
	
</div><!--End of meetingTaskCtrl -->


<script type="text/javascript">
	$(document).ready(function(){

		//** Datepicker
		$('#task_start_date').datetimepicker({
	       useCurrent: false, //Important! See issue #1075
	       format: "DD/MM/YYYY",
	    });

	    //** Datepicker
		$('#task_due_date').datetimepicker({
	       useCurrent: false, //Important! See issue #1075
	       format: "DD/MM/YYYY",
	    });

	    $("#task_start_date").on("dp.change", function(e){
	    	$('#task_due_date').data("DateTimePicker").minDate(e.date); //disable dates before of the starting date
	    });

	    //disable choosing of dates after the selected TO date
	    $("#task_due_date").on("dp.change", function (e) {
	        $('#task_start_date').data("DateTimePicker").maxDate(e.date);
	    });

	     /** Multiple choices **/
	    var config = {
	          '.chosen-select'    : {max_selected_options: 50, placeholder_text_multiple: "Participants"},
	          '.chosen-no-single' : {disable_search_threshold:10},
	          '.chosen-no-results': {no_results_text:'Oops, nothing found!'},
	          '.chosen-width'     : {width:"95%"}
	    }
	    for(var selector in config) 
	    {
	        $(selector).chosen(config[selector]);
	    }

	    $(".chosen-choices").addClass("form-control");


	    $('.panel-minimize').click(function(e){
	      e.preventDefault();
	      var $target = $(this).parent().parent().parent().next('.panel-body');

	      if($target.is(':visible')) 
	      { 

	        $('i',$(this)).removeClass('fa-minus').addClass('fa-plus'); 
	      }
	      else 
	      { 
	        $('i',$(this)).removeClass('fa-plus').addClass('fa-minus'); 
	      }
	      $target.slideToggle();
	    });


	    /** Save task **/
	    $('.btn-save-meeting-task').bind('click', function(){
	    	var form_data = $('#create_meeting_task_form').serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

	    	$.post(base_url+"index.php/meeting/save_meeting_task", form_data, function(response){
	    		var obj = $.parseJSON(response);

	    		if(obj['error'] == 0)
				{
					$.alert({
					    title: 'Success!',
					    content: obj['message'],
					    confirmButtonClass: 'btn-success',
					});
					location.reload();
				}
				else
				{
					$.alert({
					    title: 'Error!',
					    content: obj['message'],
					    confirmButtonClass: 'btn-danger',
					});
				}
	    	});

	    });


	    /** Update task **/
	    $('.btn-update-meeting-task').bind('click', function(){
	    	var form_data = $('#update_meeting_task_form').serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

	    	$.post(base_url+"index.php/meeting/update_meeting_task", form_data, function(response){
	    		var obj = $.parseJSON(response);

	    		if(obj['error'] == 0)
				{
					$.alert({
					    title: 'Success!',
					    content: obj['message'],
					    confirmButtonClass: 'btn-success',
					});
					location.reload();
				}
				else
				{
					$.alert({
					    title: 'Error!',
					    content: obj['message'],
					    confirmButtonClass: 'btn-danger',
					});
				}
	    	});

	    });

	    $('.btn-save-task-comment').bind('click', function(){
	    	var form_data = $('#add_task_comment').serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";;

	    	$.post(base_url+"index.php/meeting/save_comment", form_data, function(response){
	    		var obj = $.parseJSON(response);

	    		if(obj['error'] == 0)
				{
					$.alert({
					    title: 'Success!',
					    content: obj['message'],
					    confirmButtonClass: 'btn-success',
					});
					location.reload();
					//$('#comments').load(location.href + ' #comments-load');

				}
				else
				{
					$.alert({
					    title: 'Error!',
					    content: obj['message'],
					    confirmButtonClass: 'btn-danger',
					});
				}
	    	});
	    });

	    $('.delete_comment').bind('click', function(){
	    	var task_id = $(this).attr('data-task-id');
	    	var _this = $(this);

	    	$.ajax({
	    		type:"POST",
	    		url:base_url+"index.php/meeting/delete_comment",
	    		data:{task_id : task_id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>"},
	    		cache:false,
	    		success:function(response)
	    		{
	    			var obj = $.parseJSON(response);
	    			if(obj['error'] == 0)
					{
						$.alert({
						    title: 'Success!',
						    content: obj['message'],
						    confirmButtonClass: 'btn-success',
						});
						_this.parent().parent().parent().fadeOut(500);
						$('#comment-count').text(parseInt($('#comment-count').text()) - 1);
					}
					else
					{
						$.alert({
						    title: 'Error!',
						    content: obj['message'],
						    confirmButtonClass: 'btn-danger',
						});
					}
	    		},
	    		error:function(error)
	    		{
	    			console.log(error);
	    		}
	    	});
	    });


	    $('.btn-save-updated-comment').bind('click', function(){
	    	var task_progress_id = $(this).attr('data-task-progress-id');
	    	var form_data = $('#update_task_comment' + task_progress_id).serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";

	    	$.post(base_url+"index.php/meeting/save_update_comment", form_data, function(response){
	    		var obj = $.parseJSON(response);

	    		if(obj['error'] == 0)
				{
					$.alert({
					    title: 'Success!',
					    content: obj['message'],
					    confirmButtonClass: 'btn-success',
					});
					//location.reload();
					//$('#comments').load(location.href + ' #comments-load');
				}
				else
				{
					$.alert({
					    title: 'Error!',
					    content: obj['message'],
					    confirmButtonClass: 'btn-danger',
					});
				}
	    	});
	    });
	   

	});
</script>