<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$funding_needs->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<?php echo form_open('pitch/edit/funding_needs'); ?>
		<h3>Funding needs</h3>
		<p>How much cash does your business need to get to break-even? What will you do with the money? You will probably need to build a cashflow forecast with key underlying assumptions.</p>
		<p><strong class="heading">How much funding are you seeking?</strong></p>

		<div class="form-group">
			<div class="input-group">
				<div class="input-group-addon">&pound;</div>
				<input type="text" name="amount" value="<?php echo @$funding_needs->amount; ?>" class="form-control" />
			</div>
		</div>

		<div class="form-group">
			<label class="heading">How will you use these funds?</label>
			<textarea name="text" class="form-control"><?php echo @$funding_needs->text; ?></textarea>
		</div>

		<input name="submit" class="btn btn-success btn-sm pull-right" type="submit" value="Save and continue">
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
			  data: {plan_id: id, table:'pitch_funding', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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
			  data: {plan_id: id, table:'pitch_funding', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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