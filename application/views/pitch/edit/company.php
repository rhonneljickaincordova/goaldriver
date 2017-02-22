<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('pitch/includes/menu'); ?>
<?php $this->load->view('pitch/includes/sidebar'); ?>

<div id="main-content" class="col-sm-9">
	
	<div class="pull-right">
		<label><input type="checkbox" id="hide_show" <?php echo @$company[0]->hide == 1 ? 'checked' : ''; ?> value="<?php echo $this->session->userdata('plan_id'); ?>"> Hide from view</label>
	</div>
	
	<div id="pitch">
		<?php 
		echo $alert; 
		?>
		<?php echo form_open_multipart('pitch/edit/company'); ?>
		<div class="company-name">
			<h3>Business name and logo</h3>
			<p>Your business name and logo will display the top of your strategy. Don't worry if you don’t have a logo, you can add one later.</p>
			
			<div class="company-name-textfield">
				<h4>Enter your company name:</h4>
				<div class="input">
					<input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo @$company[0]->name ? $company[0]->name : ''; ?>">
					<small class="help-text">This is the same name that will appear on the cover page of your plan.</small>
				</div>
			</div>
		</div>
		
		<div class="company-logo">
			<h4>Upload your logo (optional):</h4>
			<p>If you have a logo add it to your strategy. This is the same image that will appear on the cover page of your plan.</p>
			
			<div class="upload-field">
				<label class="logo-output">
					<input type="file" name="company_logo" onchange="readURL(this)">
				</label>
				
				<div id="upload-preview">
					<?php if(@$company[0]->logo != ''): ?>
						<img src="<?php echo base_url('uploads/'.$company[0]->logo); ?>" width="200" />
					<?php endif; ?>
				</div>
			</div>
			
		</div>
		<div class="pull-right">
			<input type="submit" value="Save and continue" class="btn btn-success btn-sm pull-right" />
		</div>
		<?php echo form_close(); ?>
	</div>
</div>

<script>

$(function(){

	$("#hide_show").change(function(){
		var id = $(this).val();
		if ($(this).is(':checked')) {
	        //alert('checked');

	        $.ajax({
			  method: "POST",
			  url: "<?php echo site_url('pitch/hide_view'); ?>",
			  data: {plan_id: id, table:'pitch_company', hide: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
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
			  data: {plan_id: id, table:'pitch_company', hide: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>" }
			}).done(function( msg ) {
				var data = JSON.parse(msg);

				if(data.action == 'success'){
					//alert('This section is visible from the view');
				}

			});
	    }
	});

})

	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
			var filename = input.files[0].name;

            var extension = filename.substr( (filename.lastIndexOf('.') +1) );

            if(extension == 'jpg' || extension == 'jpeg' || extension == 'gif' || extension == 'png'){
	           	reader.onload = function (e) {
	    			// var img = $('<img>'); 
					// img.attr('src', e.target.result);
					// img.attr('width', '200');
					// img.appendTo('#upload-preview');
					var img = '<img src="'+e.target.result+'" width="200" />';
	           		$('#upload-preview').append(img);
	            };

	            reader.readAsDataURL(input.files[0]);
			}
            else{
            	alert('Please select a valid image file.');
    	 		//$("#ajax-msg").show().html('<div class="alert alert-danger"><i class="fa fa-info-circle"></i> Error, Please select an image file.</div>');

     			//setTimeout(function(){
					// $("#ajax-msg").hide();            		
     			//}, 2000)
            }
		}
    }
</script>

<?php $this->load->view('includes/footer'); ?>