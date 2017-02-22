<div class="bg-white-wrapper col-sm-9">

	<div class="content-section sub-section" id="content-section">
		<div class="inner-content-section">
			
			
				<?php 
					$counter = 1;
					// retreive comments on subsections
					$this->db->order_by('entered', 'DESC');
					$comments = $this->subsection_comment->get_many_by('subsection_id', $subsec_info->subsection_id);
					?>
					<div class="plan-section clearfix">
						<div class="form-group clearfix">
							<h4 id="section-title-<?php echo $subsec_info->subsection_id; ?>"><span><?php echo $subsec_info->title; ?></span> <a class="edit-section-title" href="javascript:;" id="<?php echo $subsec_info->subsection_id; ?>" onclick="update_subsection(this);" title="<?php echo $subsec_info->title; ?>">Edit title</a></h4>
							<div class="plan-section-content clearfix">
							<?php if($subsec_info->type == 'text'): ?>
							<?php 
							$subsection_data = array();
							$subsection_data['instructions'] = $subsec_info->instructions;
							$subsection_data['example'] = $subsec_info->example;
							$subsection_data['sec_id'] = $subsec_info->subsection_id;
							$subsection_data['table'] = 'subsection';

							$this->load->view('includes/instruction-example', $subsection_data); 

							?>
							<div class="subsection-content editable editable-subsection" subid="<?php echo $subsec_info->subsection_id; ?>" field="data" data-toggle="tooltip" data-placement="left" title="Click to edit this content">
								<?php echo $subsec_info->data != NULL ? html_entity_decode($subsec_info->data) : 'Click to edit this section'; ?>
							</div>
							<?php else: ?>
								
								<!-- Chart here -->

							<?php endif; ?>
								<div class="comment-box" id="comment-box<?php echo $counter; ?>">
									<a href="#" class="toggle-comment-box"><span class="comment-icon"></span><span id="total-comment"><?php echo count($comments) > 0 ? count($comments) : 0; ?></span> <?php echo count($comments) < 2 ? 'comment' : 'comments'; ?></a>
									
									<div class="comment-area">
										<textarea rows="2" id="comment-field" class="form-control" placeholder="Write a comment..."></textarea>
										<input type="submit" id="submit-comment" subsectionid="<?php echo $subsec_info->subsection_id; ?>" class="btn btn-default btn-sm" name="submit-comment" value="Post comment" />
									
									
										<div class="comments" id="comment<?php echo $counter; ?>">
											<ul>
											<?php if(count($comments)): ?>
												<?php 
												//print_r($comments);
													foreach ($comments as $comment) { ?>
														
														<li>
															<div class="profile">
																<div class="pull-left" style="margin-right:5px;">
																	<a href="#">
																		<?php if(user_info('profile_pic', $comment->user_id) != ''): ?>
																		<img class="img-responsive" src="<?php echo base_url("uploads/".$comment->user_id."/".user_info('profile_pic', $comment->user_id)); ?>" alt="<?php echo user_info('first_name', $comment->user_id).' '.user_info('last_name', $comment->user_id); ?>" width="40" />
																		<?php else: ?>
																		<img class="img-responsive" src="<?php echo base_url('public/images/nophoto.jpg'); ?>" alt="<?php echo user_info('first_name', $comment->user_id).' '.user_info('last_name', $comment->user_id); ?>" width="40" />
																		<?php endif; ?>
																	</a>
																</div>
																<div class="pull-left"><a href="#"><?php echo user_info('first_name', $comment->user_id).' '.user_info('last_name', $comment->user_id); ?></a></strong>&nbsp;<br /> <small>on <?php echo date('M j, Y, g:i a', strtotime($comment->entered)) ?></small></div>
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

				
			
			
			<div id="section-nav">
				<div class="col-sm-6">
					<?php //echo $btn_prev; ?>
					<a href="#" id="change-section">Change whats on this section</a>
				</div>
				<div class="col-sm-6">
					<span class="pull-right">
						<?php echo $btn_prev; ?>
						<?php echo $btn_next; ?>
					</span>
				</div>
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
        <h4 class="modal-title">Section title</h4>
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
        <h4 class="modal-title">Section title</h4>
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
	$(function(){
		
		$(".comment-box").each(function(index, value){
			var _this = $(this);
			
			_this.find("#submit-comment").click(function(e){
				e.preventDefault();
				var subsection_id = $(this).attr('subsectionid');
				var comment = $.trim(_this.find("#comment-field").val());
				
				if(comment.length > 1){
					$.ajax({
					  method: "POST",
					  url: "<?php echo site_url('plan/submit_subsection_comment'); ?>",
					  data: { subsection_id: subsection_id, comment: comment, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
					}).done(function( msg ) {
						var data = JSON.parse(msg);
						if(data.status == 'success'){
							_this.find( "#comment1" ).load( location.href+" #comment1" );
							_this.find("#comment-field").val('').focus();
							
							var comment_count = parseInt($("#comment-box1").find("#total-comment").text());
							$("#comment-box1").find("#total-comment").text(comment_count+1);	
						}
					});
			  
				}
				else{
					_this.find("#comment-field").val('').focus();
				}
			})	
		});
		
		
		
		// toggle comment box
		$(".toggle-comment-box").each(function(){
			var _this = $(this);
			$(this).click(function(e){
				e.preventDefault();
				_this.next(".comment-box .comment-area").toggle();	
			})
		})
		
		
		// change whats on this section
		$("#change-section").click(function(e){
			e.preventDefault();
			$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('plan/update_sections'); ?>",
			  data: { section_id: <?php echo $section_id; ?>, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
			    $("#content-section").html(msg);
			  });
		});
		
		// editable section
		$(".editable-section").each(function(){
			var _this = $(this);
			
			$(this).focusout(function(){
				var id = _this.attr("id");
				var field = _this.attr("field");
				var content = _this.html();
			
				$.ajax({
				  method: "POST",
				  url: "<?php echo site_url('plan/update_section_field'); ?>",
				  data: { section_id: id, field: field, content: content, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				}).done(function( msg ) {
					
				});
			})
		})
		
		// editable sub-section
		$(".editable-subsection").each(function(){
			var _this = $(this);
			
			$(this).focusout(function(){
				var id = _this.attr("subid");
				var field = _this.attr("field");
				var content = _this.html();
			
				$.ajax({
				  method: "POST",
				  url: "<?php echo site_url('plan/update_subsection_data'); ?>",
				  data: { subsection_id: id, field: field, content: content, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				}).done(function( msg ) {
					
				});
			})
		});
		
		// edit section form
		$("#submit-section-title-edit").click(function(){
			var new_title = $("#section_edit #section-title").val();
			var field = $("#section_edit #field").val();
			var id = $("#section_edit #section_id").val(); 
			
			$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('plan/update_section_field'); ?>",
			  data: { section_id: id, field: field, content: new_title, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				$(".preview-content h3 span").text(new_title);
				$('#section_edit').modal('hide');
			});
		});
		
		// edit sub section form
		$("#submit-subsection-title-edit").click(function(){
			var new_title = $("#subsection_edit #subsection-title").val();
			var field = $("#subsection_edit #field").val();
			var id = $("#subsection_edit #subsection_id").val(); 
			
			$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('plan/update_subsection_data'); ?>",
			  data: { subsection_id: id, field: field, content: new_title, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				$("#section-title-"+id).find("span").text(new_title);
				$('#subsection_edit').modal('hide');
			});
		});

		// display example and instruction navbar
		$(".editable").each(function(){
			var _this = $(this);
			$(this).click(function(){
				//_this.prev(".example-instruction").find('.example-instruction-wrapper').show();
				_this.prev(".example-instruction").find('i.toggle-instruction').toggleClass("up down");	
			})
		});		

		// toggle instruction-example div show/hide
		$(".toggle-in-ex").each(function(){
			var _this = $(this);
			$(this).click(function(e){
				e.preventDefault();
				$(this).toggleClass("up down");
				_this.next('div').toggle();
				// if(_this.text() === "Close"){
				// 	_this.next('div').hide();
				// 	_this.html('<i class="fa fa-chevron-up"></i>');
				// }
				// else{
				// 	_this.next('div').show();
				// 	_this.html('<i class="fa fa-chevron-down"></i>');
				// }
			})
		});

		$(".example-instruction").each(function(){
			
			$(this).find('.instruction-edit').click(function(){
				var sec_id 	= $(this).attr('id');
				var table 	= $(this).attr('table');
				var uid 	= $(this).attr('uid');
				var instruct = $("#"+uid).find('#instructions-content').html();
				//$("#instructions textarea#instructions_field").val(instruct);
				$(tinymce.get('instructions_field').getBody()).html(instruct);

				$("#instructions").modal('show');

				//console.log(instruct);

				$("input[name='sec_id']").val(sec_id);
				$("input[name='table']").val(table);
				$("input[name='uid']").val(uid);
				
			});

			$(this).find('.example-edit').click(function(){
				var sec_id = $(this).attr('id');
				var table = $(this).attr('table');
				var uid = $(this).attr('uid');
				var examp = $("#"+uid).find('#example-content').html();
				//$("#example textarea#example_field").val(examp);
				$(tinymce.get('example_field').getBody()).html(examp);

				$("#example").modal('show');

				//console.log(examp);

				$("input[name='sec_id']").val(sec_id);
				$("input[name='uid']").val(uid);
				$("input[name='table']").val(table);
				
			})
		});

		// submit instruction content
		$("#submit-instruction-content").click(function(){
			var content = $('#instructions-data').serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";
			
			$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('plan/submit_instruction'); ?>",
			  data: content
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.status == 'success'){
					$("#"+data.uid).find("#instructions-content").html(data.text);
					$("#instructions-data").trigger( "reset" );
					$("#instructions").modal('hide');

				}
			});
		})

		// submit example content
		$("#submit-example-content").click(function(){
			var content = $('#example-data').serialize()+"&<?php csrf_name(); ?>=<?php csrf_hash(); ?>";
			
			$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('plan/submit_example'); ?>",
			  data: content
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.status == 'success'){
					$("#"+data.uid).find("#example-content").html(data.text);
					$("#example-data").trigger( "reset" );
					$("#example").modal('hide');
				}
			});
		});

		// select a chart
		$(".no-chart-selected").each(function(){
			var _this = $(this);

			_this.click(function(){
				_this.hide();
				_this.next('.chart-types').show();
			});
		})
		
	});
	
	// delete comment
	function delete_my_comment(_this, ctr){
		var result = confirm("Are you sure you want to delete your comment?");
		
		if(result){
				$.ajax({
				  method: "POST",
				  url: "<?php echo site_url('plan/delete_my_comment'); ?>",
				  data: { comment_id: _this.id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
				}).done(function( msg ) {
					var data = JSON.parse(msg);
					if(data.status == 'success'){
						_this.closest("li").remove();
						
						var comment_count = $("#comment-box"+ctr).find("#total-comment").text();
						$("#comment-box"+ctr).find("#total-comment").text(parseInt(comment_count-1));
					}
				});
		}
	}
	
	// edit comment
	function edit_comment(comment_id){
		$("#edit_comment").modal('show');
	}
	
	
	// update section title
	function update_section_title(_this){
		$("#section_edit").modal('show');
		$("#section_edit #section-title").val(_this.title);
		$("#section_edit #section_id").val(_this.id);	
	}
	
	// update subsection info
	function update_subsection(_this){
		$('#subsection_edit').modal('show');
		$("#subsection_edit #subsection-title").val(_this.title);
		$("#subsection_edit #subsection_id").val(_this.id);	
	}
	
</script>