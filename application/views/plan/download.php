<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('plan/includes/menu'); ?>
<?php //$this->load->view('includes/section-sidebar'); ?>

<div class="bg-white-wrapper clearfix">
	<div class="col-sm-6">
		<div class="well">
			<h4>Ready to print?</h4>
			<p>Your plan is available as a PDF document, which is useful to save locally, email to others, or print from any printer. You can also transfer its content to Microsoft Word or other document layout software.</p>
			<br>
			<a href="<?php echo site_url('plan/download/pdf'); ?>" target="_blank" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Download as PDF</a>	
			<!-- <a href="#" onclick="alert('Sorry, this feature is not yet available. Were working on it.');" class="btn btn-default"><i class="fa fa-file-word-o"></i> Download as Doc</a>	 -->
		</div>
	</div>
	<div class="col-sm-6">
		<?php 
		if(count($print)){
		
			$print_options = unserialize($print->print_options);
			
			if($print_options != NULL){
				foreach ($print_options as $o => $v){
					$$o = $v;
				}	
			}
			
		}
		?>

		

		<div class="well">
			<h4>Print &amp; output</h4>
			
			
			<?php if(!count($print)): ?>
				<div class="alert alert-warning">You need to setup a <a href="<?php echo site_url('plan/cover_page'); ?>">cover page</a> to access print &amp; output options.</div>
			<?php else: ?>
				<div class="pull-right">
					<a href="<?php echo site_url('plan/ajax_document_options'); ?>" data-toggle="modal" data-target="#document_options" class="btn btn-default" ><i class="fa fa-wrench"></i> Document Options</a>
				</div> 
			<?php endif; ?>
			
			<p><strong>Line spacing:</strong>
				<?php 
				if(isset($spacing) AND $spacing == 1){
					echo 'Single spaced';
				}
				elseif(isset($spacing) AND $spacing == 1.5){
					echo '1.5 spacing';
				}
				elseif(isset($spacing) AND $spacing == 2){
					echo 'Double spaced';
				}
				else{
					echo '--';
				}
				?>
			</p>
			<p><strong>Paper size:</strong>
				<?php 
				if(isset($paper_size) AND $paper_size == 'a4'){
					echo 'A4 (210mm x 297mm)';
				}
				elseif(isset($paper_size) AND $paper_size == 'letter'){
					echo 'Letter (8.5 x 11 in)';
				}
				else{
					echo '--';
				}
				?>
			</p>
			<p><strong>Page title in header:</strong> 
				<?php 
				if(isset($is_plan_title) AND $is_plan_title == 1){
					echo 'On';
				}
				else{
					echo 'Off';
				}
				?>
			</p>
			<p><strong>Page numbers:</strong> 
				<?php 
				if(isset($is_paging) AND $is_paging == 1){
					echo 'On';
				}
				else{
					echo 'Off';
				}
				?>
			</p>
			<p><strong>Confidentiality message:</strong>
				<?php 
				if(isset($is_confidential_msg) AND $is_confidential_msg == 1){
					echo 'On';
				}
				else{
					echo 'Off';
				}
				?>
			</p>
			<?php 
			if(isset($is_toc) AND $is_toc == 1){
				echo '<span class="text-muted">Table of contents included.</span>';
			}
			?>
		</div>
		
	</div>
</div> <!-- .bg-white-wrapper -->

<!-- Modal for Document option -->
<div class="modal fade" id="document_options" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div>

<?php $this->load->view('includes/section-footer'); ?>
<?php $this->load->view('includes/footer'); ?>