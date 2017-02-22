<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$headline->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<?php echo form_open('pitch/edit/vision'); ?>
		<h3>Vision</h3>
		<p>Describe your business as it will be in the future. What is the prime goal or objective? What will you do for customers? How will they benefit? How big or small will your business be? How many customer and employees will there be? What do people say about your business?</p>
		<br />
		<p><strong class="heading">Describe the essence of your company in one sentence:</strong></p>
		<textarea name="headline" class="editor form-control"><?php echo @$headline->value; ?></textarea>
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
			  data: {plan_id: id, table:'pitch_headline', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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
			  data: {plan_id: id, table:'pitch_headline', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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