<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$teamkey[0]->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<h3>Team and key roles</h3>
		<p>Who are the key players in the business? What value do they offer the business? As well as the management team and key employees you can include people outside the business including mentors and advisers.</p>
		
		<?php if(count($users)): ?>
			<div id="teams">
			<?php 
			//print_r($users);
			foreach ($users as $user): ?>
				
				<div class="team">
					<strong><?php echo $user->first_name.' '.$user->last_name; ?></strong>&nbsp;&nbsp;&nbsp;<span class="text-muted"><?php echo $user->job_title; ?></span>
				</div>

			<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<br><br>
		<a href="<?php echo site_url('teams/user'); ?>"><i class="fa fa-plus"></i> Add someone</a>
		<a href="<?php echo site_url('pitch/edit/partners'); ?>" class="btn btn-success btn-sm pull-right">Continue</a>
	</div>
</div>

<script type="text/javascript">

$(function(){
	// toggle hide/show of this section in the view
	$("#hide_show").change(function(){
		var id = $(this).val();
		if ($(this).is(':checked')) {
	        //alert('checked');

	        $.ajax({
			  method: "POST",
			  url: "<?php echo site_url('pitch/hide_view'); ?>",
			  data: {plan_id: id, table:'pitch_teamkey', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.action == 'success'){
					//alert('This section is hidden from the view');
				}

			});
	    }
	    else{
	    	$.ajax({
			  method: "POST",
			  url: "<?php echo site_url('pitch/hide_view'); ?>",
			  data: {plan_id: id, table:'pitch_teamkey', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.action == 'success'){
					//alert('This section is visible from the view');
				}

			});
	    }
	});
})

</script>

<?php $this->load->view('includes/footer'); ?>