<div class="bg-white-wrapper col-sm-9">

	<div class="content-section" id="content-section">
		<div class="inner-content-section">
			<div class="preview-content">
				<h3><span><?php echo $section_info->title; ?></span> <small><a class="edit-section-title" href="javascript:;" onclick="update_section_title(this);" id="<?php echo $section_info->section_id; ?>" title="<?php echo $section_info->title; ?>">Edit title</a></small></h3>
				<div class="plan-section-content">
				<?php 
				$section_data = array();
				$section_data['instructions'] = $section_info->instructions;
				$section_data['example'] = $section_info->example;
				$section_data['sec_id'] = $section_info->section_id;
				$section_data['table'] = 'section';


				$this->load->view('includes/instruction-example', $section_data); ?>
				
					<div class="editable-section editable" id="<?php echo $section_info->section_id; ?>" field="content" data-toggle="tooltip" data-placement="left" title="Click to edit this content">
						<?php echo ($section_info->content != '' ? html_entity_decode($section_info->content) : "Click to edit this section"); ?>
					</div>
				</div>
			</div>
			
			<hr>
			
			<?php if(count($subsections)): ?>
			<ul id="sub-sections-lists">
				<?php 
				$counter = 0;
				foreach($subsections as $item): 
					// retreive comments on subsections
					$this->db->order_by('entered', 'DESC');
					$comments = $this->subsection_comment->get_many_by('subsection_id', $item->subsection_id);
					?>
					<li id="item-<?php echo $item->subsection_id; ?>">
					<div class="plan-section clearfix">
						<div class="form-group clearfix">
							<h4 id="section-title-<?php echo $item->subsection_id; ?>">
								<span class="subsection-title"><?php echo $item->title; ?></span> 
								<a data-toggle="tooltip" data-placement="top" class="edit-section-title" href="javascript:;" id="<?php echo $item->subsection_id; ?>" onclick="update_subsection(this, '<?php echo $item->title; ?>');" title="<?php echo $item->title; ?>">Edit Title</a>
								<span class="pull-right" style="margin-right:40px;">
									<i class="fa fa-arrows move-position" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Click &amp; drag to a new position"></i>
									<?php if(count($subsections) > 1): ?>
										<?php if($item->position == 0): ?>
											<i data-toggle="tooltip" data-placement="top" title="Move down" style="cursor:pointer" class="fa fa-arrow-down" id="subsection-move-down-<?php echo $counter; ?>" data-id="<?php echo $item->subsection_id; ?>" data-position="<?php echo $item->position; ?>" data-section-id="<?php echo $item->section_id; ?>"></i> 
										<?php else: ?>
											<i data-toggle="tooltip" data-placement="top" title="Move up" style="cursor:pointer" class="fa fa-arrow-up" id="subsection-move-up-<?Php echo $counter; ?>" data-id="<?php echo $item->subsection_id; ?>" data-position="<?php echo $item->position; ?>" data-section-id="<?php echo $item->section_id; ?>"></i> 
											<i data-toggle="tooltip" data-placement="top" title="Move down" style="cursor:pointer" class="fa fa-arrow-down" id="subsection-move-down-<?php echo $counter; ?>" data-id="<?php echo $item->subsection_id; ?>" data-position="<?php echo $item->position; ?>" data-section-id="<?php echo $item->section_id; ?>"></i> 
										<?php endif; ?>
									<?php endif; ?>
								</span>
							</h4>
							<div class="plan-section-content clearfix">
							<?php if($item->type == 'text'): ?>
							<?php 
							$subsection_data = array();
							$subsection_data['instructions'] = $item->instructions;
							$subsection_data['example'] = $item->example;
							$subsection_data['sec_id'] = $item->subsection_id;
							$subsection_data['table'] = 'subsection';

							$this->load->view('includes/instruction-example', $subsection_data); 

							?>
							<div class="subsection-content editable editable-subsection" subid="<?php echo $item->subsection_id; ?>" field="data" data-toggle="tooltip" data-placement="left" title="Click to edit this content">
								<?php echo $item->data != NULL ? html_entity_decode($item->data) : 'Click to edit this section'; ?>
							</div>
							<?php else: ?>


								
								<!-- Chart here -->

							<?php endif; ?>
								<div class="comment-box" id="comment-box<?php echo $counter; ?>">
									<a href="#" class="toggle-comment-box"><span class="comment-icon"></span><span id="total-comment"><?php echo count($comments) > 0 ? count($comments) : 0; ?></span> <?php echo count($comments) < 2 ? 'comment' : 'comments'; ?></a>
									
									<div class="comment-area">
										<textarea rows="2" id="comment-field" class="form-control" placeholder="Write a comment..."></textarea>
										<input type="submit" id="submit-comment" subsectionid="<?php echo $item->subsection_id; ?>" class="btn btn-default btn-sm" name="submit-comment" value="Post comment" />
									
									
										<div class="comments" id="comment<?php echo $counter; ?>">
											<ul>
											<?php if(count($comments)): ?>
												<?php 
												//print_r($comments);
													foreach ($comments as $comment) { 

														$user_photo = user_info('profile_pic', $comment->user_id);

														if($user_photo == '' || $user_photo == NULL)
														{
															$profile_pic = base_url('public/images/nophoto.jpg');
														}
														else{
															$profile_pic = base_url("uploads/".$comment->user_id."/".user_info('profile_pic', $comment->user_id));
														}
														?>
														
														<li>
															<div class="profile">
																<div class="pull-left" style="margin-right:5px;"><a href="#"><img class="img-responsive" src="<?php echo $profile_pic; ?>" alt="<?php echo user_info('first_name', $comment->user_id).' '.user_info('last_name', $comment->user_id); ?>" width="40" /></a></div>
																<div class="pull-left"><a href="#"><?php echo user_info('first_name', $comment->user_id).' '.user_info('last_name', $comment->user_id); ?></a></strong>&nbsp;<br /> <small>on <?php echo gd_date($comment->entered, 'M j, Y g:i a') ?></small></div>
															</div>
															<div class="comment-text">															
																<div><?php echo $comment->comment; ?></div>
																<div class="pull-right"><?php echo $comment->user_id == $this->session->userdata('user_id') ? "<a href=".site_url('plan/ajax_edit_comment/'.$comment->id)." data-toggle=\"modal\" data-target=\"#edit_comment\"><i class=\"fa fa-pencil-square-o\" aria-hidden=\"true\" data-toggle='tooltip' data-placement='bottom' title='Edit comment'></i></a>&nbsp;&nbsp;<a href='javascript:;' id='".$comment->id."' onclick='delete_my_comment(this, ".$counter.");'><i class='fa fa-trash-o' data-toggle='tooltip' data-placement='bottom' title='Delete comment'></i></a>": ""; ?></div>
															</div>
														</li>
														
													<?php } ?>	
											<?php endif; ?>
											</ul>
										</div>
									</div>
								</div> <!-- comment-box -->

							</div>
						</div>	
						
						
						
						
					</div> <!-- end .section -->
					</li>
				<?php $counter++; endforeach; ?>
				</ul>

			<?php endif; ?>
			
			<div id="section-nav">
				<div class="col-sm-6">
					<?php //echo $btn_prev; ?>
					<a href="#" id="change-section">Change whats on this section</a>
				</div>
				<div class="col-sm-6"><span class="pull-right"><?php echo $btn_next; ?></span></div>
			</div>
		</div>
	</div>

</div>

<!-- Edit Section title -->
<div id="section_edit" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Section name</h4>
      </div>
      <div class="modal-body">
        <input type="text" id="section-title" name="title" class="form-control" />
        <input type="hidden" id="field" name="field" value="title" />
        <input type="hidden" id="section_id" name="section_id" value="" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="submit-section-title-edit" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Edit Subsection title -->
<div id="subsection_edit" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Section name</h4>
      </div>
      <div class="modal-body">
        <input type="text" id="subsection-title" name="title" class="form-control" />
        <input type="hidden" id="field" name="field" value="title" />
        <input type="hidden" id="subsection_id" name="subsection_id" value="" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="submit-subsection-title-edit" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Edit Instruction content -->
<div id="instructions" class="modal fade" tabindex="-1" role="dialog" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Instructions</h4>
      </div>
      <div class="modal-body">
      	<form id="instructions-data">
	        <textarea class="form-control editor" id="instructions_field" name="instructions"></textarea>
	        <input type="hidden" name="sec_id" value="">
	        <input type="hidden" name="table" value="">
	        <input type="hidden" name="uid" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="submit-instruction-content" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Edit Example content -->
<div id="example" class="modal fade" tabindex="-1" role="dialog" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Example</h4>
      </div>
      <div class="modal-body">
      	<form id="example-data">
	        <textarea class="form-control editor" id="example_field" name="example"></textarea>
	        <input type="hidden" name="sec_id" value="">
	        <input type="hidden" name="table" value="">
	        <input type="hidden" name="uid" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="submit-example-content" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Edit Comment content -->
<div id="edit_comment" class="modal fade" tabindex="-1" role="dialog" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="text/javascript">
	// global section_id
	var g_section_id = <?php echo $section_id; ?>;
</script>

<script src="<?php echo base_url() ?>public/sub_section_position.js" type="text/javascript"></script>