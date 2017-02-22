<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$purpose->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<?php echo form_open('pitch/edit/purpose'); ?>
		<h3>Purpose</h3>
		<p>Profit is not a reason to run a business; it is the result of running a good business. Your business needs a non-financial purpose to get the buy in of everyone it comes into contact with.</p>
		<br />
		<p><strong class="heading">Describe the essence of your company in one sentence:</strong></p>
		<textarea name="purpose" class="editor form-control"><?php echo @$purpose->value; ?></textarea>
		<br>
		<div class="pull-right">
			<input type="submit" value="Save and continue" class="btn btn-success btn-sm pull-right">
		</div>
		<?php echo form_close(); ?>
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
			  data: {plan_id: id, table:'pitch_purpose', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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
			  data: {plan_id: id, table:'pitch_purpose', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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