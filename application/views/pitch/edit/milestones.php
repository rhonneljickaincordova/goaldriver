<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$milestones[0]->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<h3>Milestones</h3>
		<p>What are the key steps your business needs to take? Think about the next 12-36 months.</p>
		<br>
		<div class="late-milestones milestones">
			<h4>Late</h4>
			<?php if(count($late_milestones)): ?>
			<?php foreach ($late_milestones as $late): ?>
				<div class="milestone clearfix">
					<span class="text-muted"><?php echo date('F j, Y', strtotime($late->dueDate)); ?></span><br />
					<?php echo $late->name; ?>&nbsp;&nbsp;&nbsp;<span class="text-muted"><?php echo $late->first_name; ?></span>
					<!-- <div class="option pull-right">
						<a href="#"><i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a>
						<a href="#"><i class="fa fa-trash-o" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>
					</div> -->
				</div>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="upcoming-milestones milestones">
			<h4>Upcoming</h4>
			<?php if(count($upcoming_milestones)): ?>
			<?php foreach ($upcoming_milestones as $upcoming): ?>
				<div class="milestone clearfix">
					<span class="text-muted"><?php echo date('F j, Y', strtotime($upcoming->dueDate)); ?></span><br />
					<?php echo $upcoming->name; ?>&nbsp;&nbsp;&nbsp;<span class="text-muted"><?php echo $upcoming->first_name; ?></span>
					<!-- <div class="option pull-right">
						<a href="#"><i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="bottom" title="Edit"></i></a>
						<a href="#"><i class="fa fa-trash-o" data-toggle="tooltip" data-placement="bottom" title="Delete"></i></a>
					</div> -->
				</div>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="pull-right">
			<a href="<?php echo site_url('milestone'); ?>">Edit Milestones</a> OR 
			<a href="<?php echo site_url('pitch/edit/team_key_roles'); ?>" class="btn btn-success btn-sm">Continue</a>
		</div>
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
			  data: {plan_id: id, table:'pitch_milestone', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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
			  data: {plan_id: id, table:'pitch_milestone', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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