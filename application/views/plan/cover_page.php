<?php $this->load->view('includes/header'); ?>
<?php $this->load->view('plan/includes/menu'); ?>
<?php //$this->load->view('includes/section-sidebar'); ?>

<div class="clearfix"></div>
<?php
	$code = $this->session->flashdata('res_code');
	$msg = $this->session->flashdata('res_message');

	$alert = '';
	switch ($code) {
		case 'notice':
			$alert = 'alert-info';
			break;
		case 'warning':
			$alert = 'alert-warning';
			break;
		case 'danger':
			$alert = 'alert-danger';
			break;
		case 'success':
			$alert = 'alert-success';
			break;
	}
	echo '<div class="alert '.$alert.'">'.$msg.'</div>';
?>

<?php
//print_r($cp_info);
if(count($cp_info)){
	foreach ($cp_info[0] as $k => $v) {
		$$k = $v;
	}
}


if(isset($print_options) AND $print_options != "")
{
	$print_settings = unserialize($print_options);
}
?>
<div class="bg-white-wrapper clearfix">
	<div id="cover-page-settings" style="<?php echo ($plan[0]->is_coverpage == 1) ? 'display:block;' : 'display:hidden;'; ?>">
		<div class="col-sm-9">
			<div class="pull-right">
				<a href="#" id="hide-cover-page-settings">Remove</a>
			</div>
			<?php echo form_open_multipart('plan/cover_page', array('id' => 'coverpageform')); ?>
			<div class="form-group">
				<label>Company Logo</label>
				<div id="thumb-wrapper">
					<?php if(isset($company_logo)): ?>
					<div class="fileUpload btn btn-default">
						<span><i class="fa fa-upload"></i> Upload a company logo...</span>
						<input type="file" class="upload" name="company_logo" onchange="readURL(this);" >
					</div><br><br>
					<img class="thumbnail" src="<?php echo base_url('uploads/'.$company_logo); ?>" width="150" alt="Company Logo">
					<?php else:?>
					<div class="fileUpload btn btn-default">
						<span><i class="fa fa-upload"></i> Upload a company logo...</span>
						<input type="file" class="upload" name="company_logo" onchange="readURL(this);" >
					</div><br><br>
					<img src="" class="thumbnail">
					<?php endif; ?>
				</div>
			</div>
			<div class="form-group">
				<label>Company Name <small class="required">(required)</small> </label>
				<input type="text" name="company_name" class="form-control" value="<?php echo isset($company_name) ? $company_name : ''; ?>">
			</div>
			<div class="form-group">
				<label>Slogan or Tagline</label>
				<p class="help-block">Got a catchy description of your company, products, or mission?</p>
				<input type="text" name="slogan" class="form-control" value="<?php echo isset($slogan) ? $slogan : ''; ?>">
			</div>

			<div class="row">
				<div class="col-sm-8">
					<div class="form-group">
						<label>Street Address</label>
						<input type="text" name="address" class="form-control" value="<?php echo isset($street_address) ? $street_address : ''; ?>">
					</div>
					<div class="row">
						<div class="form-group col-sm-6">
							<label>Country/State</label>
							<input type="text" name="state" class="form-control" value="<?php echo isset($state) ? $state : ''; ?>">
						</div>
						<div class="form-group col-sm-6">
							<label>Postal Code</label>
							<input type="text" name="postal" class="form-control" value="<?php echo isset($postal) ? $postal : ''; ?>">
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label>City</label>
						<input type="text" name="city" class="form-control" value="<?php echo isset($city) ? $city : ''; ?>">
					</div>
					<div class="form-group">
							<label>Country</label>
							<input type="text" name="country" class="form-control" value="<?php echo isset($country) ? $country : ''; ?>">
						</div>
				</div>
			</div> <!-- end .row -->

			<div class="form-group">
				<label>Contact Name <small class="required">(required)</small></label>
				<input type="text" name="contact_name" class="form-control" value="<?php echo isset($contact_name) ? $contact_name : ''; ?>">
			</div>
			<div class="form-group">
				<label>Contact Email <small class="required">(required)</small></label>
				<input type="text" name="contact_email" class="form-control" value="<?php echo isset($contact_email) ? $contact_email : ''; ?>">
			</div>
			<div class="form-group">
				<label>Contact Phone</label>
				<input type="text" name="contact_phone" class="form-control" value="<?php echo isset($contact_phone) ? $contact_phone : ''; ?>">
			</div>
			<div class="form-group">
				<label>Company Website</label>
				<input type="text" name="company_website" placeholder="http://" class="form-control" value="<?php echo isset($company_website) ? $company_website : ''; ?>">
			</div>
			<div class="form-group">
				<label>Confidentiality Message</label>
				<input type="text" name="confidentiality_message" class="form-control" value="<?php echo isset($confidentiality_message) ? $confidentiality_message : ''; ?>">
			</div>

			<div class="pull-right">
				<input type="submit" value="Save Changes" class="btn btn-success">
			</div>

			<input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>">

		</div>
		<div class="col-sm-3">
			<div class="well">
				<h5>Want to include a table of contents too?</h5>
				<p>Select the option below to include a table of contents in your plan document.</p>

				<input type="checkbox" <?php echo (isset($print_settings['is_toc']) AND $print_settings['is_toc'] == 1) ? 'checked' : ''; ?> name="is_toc"> Include a table of contents
			</div>
		</div>

		<?php echo form_close(); ?>
	</div> <!-- end #cover-page-settings -->

	<div id="no-cover-page">
		<div class="col-sm-8">
			<img src="<?php echo base_url('public/images/cover-page-cs.png'); ?>">
		</div>
		<div class="col-sm-4">
			<h3>Add a cover page to your document</h3>
			<p>Want to dress up your plan document with a nice-looking front page and table of contents? Click below to enter the details to include.</p>
			<a href="#" id="get-started-cover-settings" class="btn btn-primary">Get started</a>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		<?php if($plan[0]->is_coverpage == 1): ?>
		$("#no-cover-page").hide();
		$("#cover-page-settings").show();
		<?php elseif($plan[0]->is_coverpage == 0): ?>
		$("#no-cover-page").show();
		$("#cover-page-settings").hide();
		<?php endif; ?>

		$("#get-started-cover-settings").click(function(){
			$("#no-cover-page").hide();
			$("#cover-page-settings").show();
			// update status = 1
			$.ajax({
		  	method: "POST",
			  url: "<?php echo site_url('plan/is_activate_cover_page'); ?>",
			  data: {activate: 1, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>"}
			}).done(function( msg ) {
				var data = JSON.parse(msg);
			});
		});

		$("#hide-cover-page-settings").click(function(){

			var hide_conf = confirm("Are you sure you want to remove the cover page from your plan? You can always add one again later.");

			if(hide_conf)
			{
				$("#no-cover-page").show();
				$("#cover-page-settings").hide();
				// update status = 0
				$.ajax({
			  	method: "POST",
				  url: "<?php echo site_url('plan/is_activate_cover_page'); ?>",
				  data: {activate: 0, <?php csrf_name(); ?>:"<?php csrf_hash(); ?>"}
				}).done(function( msg ) {
					var data = JSON.parse(msg);
				});
			}
			
		})

		// validate
		$("#coverpageform").submit(function(){
			var company_name = $.trim($("input[name='company_name']").val());
			var contact_name = $.trim($("input[name='contact_name']").val());
			var contact_email = $.trim($("input[name='contact_email']").val());

			if(company_name == '')
			{
				alert('Company name is required.');
				$("input[name='company_name']").focus();
				return false;
			}
			if(contact_name == '')
			{
				alert('Contact name is required.');
				$("input[name='contact_name']").focus();
				return false;
			}
			if(contact_email == '' || !isEmail(contact_email))
			{
				alert('Valid email is required.');
				$("input[name='contact_email']").focus();
				return false;
			}
		})
	})

	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
			var filename = input.files[0].name;

            var extension = filename.substr( (filename.lastIndexOf('.') +1) );

            if(extension == 'jpg' || extension == 'jpeg' || extension == 'gif' || extension == 'png'){
	           	reader.onload = function (e) {
	                $('.thumbnail')
	                    .attr('src', e.target.result)
	                    .width(150)
	            };

	            reader.readAsDataURL(input.files[0]);
			}
            else{
            	$("#ajax-msg").show().html('<div class="alert alert-danger"><i class="fa fa-info-circle"></i> Error, Please select an image file.</div>');

            	setTimeout(function(){
					$("#ajax-msg").hide();            		
            	}, 2000)
            }
		}
    }

	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
</script>
<?php $this->load->view('includes/section-footer'); ?>
<?php $this->load->view('includes/footer'); ?>