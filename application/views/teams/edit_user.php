<?php $this->load->view('includes/header'); ?>
<div class="bg-white-wrapper clearfix">
<div class="col-sm-8 col-sm-offset-2">
	<?php echo form_open('teams/edit_user/'.encrypt($user_id)."/".encrypt($organ_id), array('class' => 'form-horizontal')); ?>
	<div class="form-group">
		<label for="first-name" class="control-label col-sm-2">First Name</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="first-name" name="first_name" value="<?php echo $first_name; ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="last-name" class="control-label col-sm-2">Last Name</label>
		<div class="col-sm-8"> 
			<input type="text" class="form-control" id="last-name" name="last_name" value="<?php echo $last_name; ?>">
		</div>
	</div>
	<!-- <div class="form-group">
		<label for="username" class="control-label col-sm-2">Username</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
		</div>
	</div> -->
	<div class="form-group">
		<label for="username" class="control-label col-sm-2">Password</label>
		<div class="col-sm-8">
			<a href="#changepass" data-toggle="modal" data-target="#changepass">Change password</a>
		</div>
	</div>
	<div class="form-group">
		<label for="company_name" class="control-label col-sm-2">Company</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="company_name" name="company" value="<?php echo $company; ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="phone" class="control-label col-sm-2">Phone</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="phone" name="tel_number" value="<?php echo $tel_number; ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="job" class="control-label col-sm-2">Job</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="job" name="job_title" value="<?php echo $job_title; ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">&nbsp;</label>
		<div class="col-sm-8">
			<input type="submit" class="btn btn-primary" value="Save"> or <a href="<?php echo site_url('teams/user'); ?>">Go back</a>
		</div>
	</div>

	<?php echo form_close(); ?>
</div>
</div>
<!-- Modal -->
<div class="modal fade" id="changepass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Change password</h4>
			</div>
			<div class="modal-body">
				<div id="response-msg"></div>
				<div class="form-group">
					<label for="job" class="control-label">Current password</label>
					<input type="password" class="form-control" id="old_password" placeholder="Enter your current password">
				</div>
				<div class="form-group">
					<label for="job" class="control-label">Your new password</label>
					<input type="password" class="form-control" id="new_password" placeholder="Enter your new password">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="savepass" class="btn btn-primary">Save changes</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function(){

	$("#savepass").click(function(){
		$.ajax({
			method: "POST",
			url: "<?php echo site_url('account/change_password/'.$my_user_id); ?>",
			data: { oldpass: $('#old_password').val(), newpass: $('#new_password').val() }
		})
		.done(function( msg ) {
			var myObj = $.parseJSON(msg);

			$("#response-msg").html(myObj.msg);
			$('#old_password').val("");
			$('#new_password').val("");
		});	
	})
	

})
</script>
<?php $this->load->view('includes/footer'); ?>