	<div class="col-sm-9">
		<div class="content-section" id="content-section">
			
			<h3><?php echo $chapter_info->title; ?></h3>
			<br>
			
			
			<div class="form-group clearfix" id="chapter-content">
					<ul id="sections" class="list-group">
						<?php 
						//print_r($sections);
						if(count($sections)){
							$counter = 0;
							foreach ($sections as $section) { ?>
								<li class="list-group-item plan-section" id="item_<?php echo $section->section_id ?>">
									<h4>
										<a class="section-title" style="color:#fff;" href="<?php echo site_url('plan/chapter/'.encrypt($chapter_info->chapter_id).'/'.encrypt($section->section_id)); ?>"><?php echo $section->title ?></a>
										<a href="#" title="Edit section" data-toggle="modal" data-target="#edit-section" section="<?php echo $section->section_id ?>" section_title="<?php echo $section->title ?>" class="edit-section">Edit Title</a> 
										<div class="button-group">
											<i class="fa fa-arrows move-position" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Click &amp; drag to a new position"></i>
											
											<?php if($section->position == 0): ?>
												<i data-toggle="tooltip" data-placement="top" title="Move down" style="cursor:pointer" class="fa fa-arrow-down" id="section-move-down-<?php echo $counter; ?>" data-position="<?php echo $section->position; ?>" data-section-id="<?php echo $section->section_id; ?>"></i> 
											<?php else: ?>
												<i data-toggle="tooltip" data-placement="top" title="Move up" style="cursor:pointer" class="fa fa-arrow-up" id="section-move-up-<?Php echo $counter; ?>" data-position="<?php echo $section->position; ?>" data-section-id="<?php echo $section->section_id; ?>"></i> 
												<i data-toggle="tooltip" data-placement="top" title="Move down" style="cursor:pointer" class="fa fa-arrow-down" id="section-move-down-<?php echo $counter; ?>" data-position="<?php echo $section->position; ?>" data-section-id="<?php echo $section->section_id; ?>"></i> 
											<?php endif; ?>
											

											<a href="#" title="Delete section" section="<?php echo $section->section_id ?>" section_title="<?php echo $section->title ?>" class="delete-section"></a>
										</div>
									</h4>
									
									<div class="section-content">
										<?php echo html_entity_decode($section->content); ?>
										<p><strong>In this section:</strong></p>
										<ul class="in-this-section">
											<?php 
											$this->db->order_by('position', 'ASC');
											$subsecs = $this->subsection->get_many_by('section_id', $section->section_id);
											//print_r($subsections);
											if(count($subsecs)): ?>
												<?php foreach($subsecs as $sub): ?>
													<li>
														<?php if($sub->type == 'text'): ?>
														<i class="text"></i>
														<?php else: ?>
														<i class="chart"></i>	
														<?php endif; ?> 
														<?php echo $sub->title; ?>
													</li>
												<?php endforeach; ?>
											<?php else: ?>
												<li>No item yet.</li>
											<?php  endif; ?>
										</ul>
									</div>
									<div style="padding:1em;overflow:hidden;">
										<div class="pull-left">
											<a href="#" onclick="return section_setup(<?php echo $section->section_id ?>);">Change whats on this section</a>
										</div>
										<!-- <div class="pull-right">
										<a class="btn btn-primary btn-sm" href="<?php echo site_url('plan/chapter/'.encrypt($chapter_info->chapter_id).'/'.encrypt($section->section_id)); ?>"><i class="fa fa-angle-right" aria-hidden="true"></i> Go to this section</a>
										</div> -->
									</div>
								</li>
						<?php	
							$counter++;
							} // end foreach
						}
						else{
							echo '<li>No Section created yet.</li>';
						}
						?>
					</ul>
			</div>
			
			<div class="form-group clearfix">
				<div class="pull-left">
					<a href="#new-section" data-toggle="modal" class="btn btn-primary pull-right btn-sm"><i class="fa fa-plus"></i> Add Section</a>
				</div>
				<div class="pull-right">
					<div class="col-sm-6"><?php echo $btn_prev; ?></div>
					<div class="col-sm-6"><span class="pull-right"><?php echo $btn_next; ?></span></div>
				</div>
			</div>
	
		</div>
	</div>

<script type="text/javascript">
	function section_setup(section_id){
			$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('plan/update_sections'); ?>",
			  data: { section_id: section_id, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
			    $(".content-section").html(msg);
			});
	}
</script>
